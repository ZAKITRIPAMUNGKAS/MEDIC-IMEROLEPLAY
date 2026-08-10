<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OperationRecord;
use App\Models\OperationPhoto;
use App\Models\OperationRecordLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OperationRecordController extends Controller
{
    /**
     * Check if user has permission
     */
    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }
        
        $userRole = strtolower(trim($user->role?->name ?? ''));

        // Trainee tidak diperbolehkan mengisi/mengakses fitur Rekam Operasi
        if ($userRole === 'trainee') {
            abort(403, 'Akses ditolak: Anggota dengan status Trainee tidak diperbolehkan mengisi rekam operasi.');
        }

        // Izinkan semua user yang terautentikasi (selain Trainee) untuk mengakses rekam operasi
        return true;
    }

    public function index(Request $request)
    {
        $this->checkPermission();

        // Isolasi data per rumah sakit — hanya tampilkan rekam operasi milik hospital user login
        $userHospital = strtolower(trim(Auth::user()->hospital ?? 'roxwood'));

        $query = OperationRecord::with(['creator', 'dpjp', 'members', 'logs.user'])
                    ->where('hospital', $userHospital);

        // Filters
        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        if ($request->filled('citizen_id')) {
            $citId = trim($request->citizen_id);
            $query->where('medical_details->pasien->citizen_id', 'like', '%' . $citId . '%');
        }

        if ($request->filled('tim_operasi')) {
            $timSearch = trim($request->tim_operasi);
            $query->where(function($q) use ($timSearch) {
                $q->whereHas('members', function($qm) use ($timSearch) {
                    $qm->where('name', 'like', '%' . $timSearch . '%')
                       ->orWhere('staff_id', 'like', '%' . $timSearch . '%');
                })
                ->orWhereHas('dpjp', function($qd) use ($timSearch) {
                    $qd->where('name', 'like', '%' . $timSearch . '%')
                       ->orWhere('staff_id', 'like', '%' . $timSearch . '%');
                })
                ->orWhereHas('creator', function($qc) use ($timSearch) {
                    $qc->where('name', 'like', '%' . $timSearch . '%')
                       ->orWhere('staff_id', 'like', '%' . $timSearch . '%');
                });
            });
        }
        
        if ($request->filled('jenis_operasi')) {
            $query->where('jenis_operasi', $request->jenis_operasi);
        }

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_waktu', $request->bulan)
                  ->whereYear('tanggal_waktu', $request->tahun);
        }

        $operations = $query->orderBy('tanggal_waktu', 'desc')->paginate(15);

        return view('staff.operations.index', compact('operations'));
    }

    public function create()
    {
        $this->checkPermission();
        return view('staff.operations.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission();
        $userHospital = strtolower(trim(Auth::user()->hospital ?? 'roxwood'));

        // Clean empty dpjp_id string to null
        if ($request->input('dpjp_id') === '') {
            $request->merge(['dpjp_id' => null]);
        }

        $validated = $request->validate([
            'tanggal_waktu'   => 'required|date',
            'lokasi'          => 'required|string|max:255',
            'jenis_operasi'   => 'required|in:Operasi Minor,Operasi Mayor,Emergency,Konsultasi Spesialisasi,Lainnya',
            'nama_pasien'     => 'required|string|max:255',
            'diagnosa'        => 'nullable|string',
            'tindakan_operasi'=> 'nullable|string',
            'hasil_operasi'   => 'nullable|string',
            'catatan'         => 'nullable|string',
            'dpjp_id'         => 'nullable|exists:users,id',
            'members'         => $request->input('jenis_operasi') === 'Konsultasi Spesialisasi' ? 'nullable|array' : 'required|array|min:1',
            'members.*'       => 'exists:users,id',
            'photos'          => 'nullable|array',
            'photos.*'        => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'medical_details' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $medicalDetails = $request->input('medical_details', []);

            $diagnosaVal = !empty($validated['diagnosa']) 
                ? $validated['diagnosa'] 
                : (!empty($medicalDetails['anamnesis']['anamnesis_utama']) ? $medicalDetails['anamnesis']['anamnesis_utama'] : 'Rekam Medis Lengkap');

            $tindakanVal = !empty($validated['tindakan_operasi']) 
                ? $validated['tindakan_operasi'] 
                : (!empty($medicalDetails['tindakan']['nama_tindakan']) ? $medicalDetails['tindakan']['nama_tindakan'] : 'Operasi Medis');

            $hasilVal = !empty($validated['hasil_operasi']) 
                ? $validated['hasil_operasi'] 
                : 'Selesai Sesuai Prosedur';

            // Create record
            $operation = OperationRecord::create([
                'tanggal_waktu'    => $validated['tanggal_waktu'],
                'lokasi'           => $validated['lokasi'],
                'jenis_operasi'    => $validated['jenis_operasi'],
                'hospital'         => $userHospital,
                'nama_pasien'      => $validated['nama_pasien'],
                'diagnosa'         => $diagnosaVal,
                'tindakan_operasi' => $tindakanVal,
                'hasil_operasi'    => $hasilVal,
                'catatan'          => $validated['catatan'] ?? null,
                'created_by'       => Auth::id(),
                'dpjp_id'          => $validated['dpjp_id'] ?? null,
                'medical_details'  => $medicalDetails,
            ]);

            // Sync members
            $operation->members()->sync($validated['members']);

            // Upload photos — simpan ke public/uploads/operations/ (folder yang sudah ada di server)
            if ($request->hasFile('photos')) {
                \Illuminate\Support\Facades\Log::info('Memulai proses upload foto operasi', ['count' => count($request->file('photos'))]);
                $uploadDir = public_path('uploads/operations');
                \Illuminate\Support\Facades\Log::info('Target direktori upload: ' . $uploadDir);
                
                if (!file_exists($uploadDir)) {
                    $created = @mkdir($uploadDir, 0777, true);
                    \Illuminate\Support\Facades\Log::info('Direktori belum ada, proses mkdir: ' . ($created ? 'Berhasil' : 'Gagal'));
                }

                foreach ($request->file('photos') as $idx => $photo) {
                    \Illuminate\Support\Facades\Log::info("Memproses foto ke-$idx", [
                        'valid' => $photo->isValid(),
                        'error' => $photo->getError(),
                        'originalName' => $photo->getClientOriginalName()
                    ]);
                    
                    if (!$photo->isValid()) continue;

                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

                    try {
                        // Pindahkan file ke public/uploads/operations/
                        $photo->move($uploadDir, $filename);
                        \Illuminate\Support\Facades\Log::info("Berhasil move foto ke $uploadDir/$filename");

                        // Simpan path relatif — akan diakses via asset('uploads/operations/filename.jpg')
                        OperationPhoto::create([
                            'operation_record_id' => $operation->id,
                            'file_path' => 'uploads/operations/' . $filename,
                        ]);
                        \Illuminate\Support\Facades\Log::info("Berhasil simpan record DB untuk $filename");
                    } catch (\Exception $ex) {
                        \Illuminate\Support\Facades\Log::error("Gagal move/simpan foto $filename: " . $ex->getMessage());
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('Tidak ada file "photos" dalam request form.');
            }

            
            // Record log creation
            OperationRecordLog::create([
                'operation_record_id' => $operation->id,
                'user_id'             => Auth::id(),
                'action'              => 'create',
                'details'             => 'Membuat rekam operasi medis baru',
            ]);

            DB::commit();

            return redirect()->route('staff.operations.index')->with('success', 'Rekam Operasi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan rekam operasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan rekam operasi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Check if user is allowed to edit/complete this specific operation record
     */
    public function canUserEdit($user, OperationRecord $operation)
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($operation->created_by == $user->id || $operation->dpjp_id == $user->id) {
            return true;
        }
        // Check if user is in tagged members
        return $operation->members()->where('users.id', $user->id)->exists();
    }

    public function show($id)
    {
        $this->checkPermission();
        $operation = OperationRecord::with(['creator', 'dpjp', 'members.role', 'photos', 'logs.user'])->findOrFail($id);
        
        // Proteksi: staff hanya bisa lihat rekam operasi dari hospital mereka sendiri
        $userHospital = strtolower(trim(Auth::user()->hospital ?? 'roxwood'));
        if (strtolower(trim($operation->hospital ?? '')) !== $userHospital) {
            abort(403, 'Anda tidak memiliki akses ke rekam operasi ini.');
        }

        // Log view action (debounce 2 mins)
        try {
            $lastView = OperationRecordLog::where('operation_record_id', $operation->id)
                ->where('user_id', Auth::id())
                ->where('action', 'view')
                ->where('created_at', '>=', now()->subMinutes(2))
                ->first();

            if (!$lastView) {
                OperationRecordLog::create([
                    'operation_record_id' => $operation->id,
                    'user_id'             => Auth::id(),
                    'action'              => 'view',
                    'details'             => 'Melihat detail rekam operasi',
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mencatat log view: ' . $e->getMessage());
        }

        $canEdit = $this->canUserEdit(Auth::user(), $operation);
        
        return view('staff.operations.show', compact('operation', 'canEdit'));
    }

    public function edit($id)
    {
        $this->checkPermission();
        $operation = OperationRecord::with(['creator', 'dpjp', 'members', 'photos'])->findOrFail($id);
        
        $userHospital = strtolower(trim(Auth::user()->hospital ?? 'roxwood'));
        if (strtolower(trim($operation->hospital ?? '')) !== $userHospital) {
            abort(403, 'Anda tidak memiliki akses ke rekam operasi ini.');
        }

        if (!$this->canUserEdit(Auth::user(), $operation)) {
            abort(403, 'Akses Ditolak: Hanya anggota tim yang di-tag / dicantumkan pada rekam medis ini yang dapat mengedit/melengkapi data.');
        }

        return view('staff.operations.edit', compact('operation'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission();
        $operation = OperationRecord::with(['members', 'photos'])->findOrFail($id);

        $userHospital = strtolower(trim(Auth::user()->hospital ?? 'roxwood'));
        if (strtolower(trim($operation->hospital ?? '')) !== $userHospital) {
            abort(403, 'Anda tidak memiliki akses ke rekam operasi ini.');
        }

        if (!$this->canUserEdit(Auth::user(), $operation)) {
            abort(403, 'Akses Ditolak: Hanya anggota tim yang di-tag / dicantumkan pada rekam medis ini yang dapat mengedit/melengkapi data.');
        }

        // Clean empty dpjp_id string to null
        if ($request->input('dpjp_id') === '') {
            $request->merge(['dpjp_id' => null]);
        }

        $validated = $request->validate([
            'tanggal_waktu'   => 'required|date',
            'lokasi'          => 'required|string|max:255',
            'jenis_operasi'   => 'required|in:Operasi Minor,Operasi Mayor,Emergency,Konsultasi Spesialisasi,Lainnya',
            'nama_pasien'     => 'required|string|max:255',
            'diagnosa'        => 'nullable|string',
            'tindakan_operasi'=> 'nullable|string',
            'hasil_operasi'   => 'nullable|string',
            'catatan'         => 'nullable|string',
            'dpjp_id'         => 'nullable|exists:users,id',
            'members'         => 'nullable|array',
            'members.*'       => 'exists:users,id',
            'photos'          => 'nullable|array',
            'photos.*'        => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'medical_details' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Merge existing medical_details with new values
            $existingDetails = $operation->medical_details ?? [];
            $newDetails = $request->input('medical_details', []);
            $mergedDetails = array_replace_recursive($existingDetails, $newDetails);

            $operation->update([
                'tanggal_waktu'    => $validated['tanggal_waktu'],
                'lokasi'           => $validated['lokasi'],
                'jenis_operasi'    => $validated['jenis_operasi'],
                'nama_pasien'      => $validated['nama_pasien'],
                'diagnosa'         => $validated['diagnosa'] ?? $operation->diagnosa,
                'tindakan_operasi' => $validated['tindakan_operasi'] ?? $operation->tindakan_operasi,
                'hasil_operasi'    => $validated['hasil_operasi'] ?? $operation->hasil_operasi,
                'catatan'          => $validated['catatan'] ?? $operation->catatan,
                'dpjp_id'          => $validated['dpjp_id'] ?? $operation->dpjp_id,
                'medical_details'  => $mergedDetails,
            ]);

            // Sync members if provided
            if (!empty($validated['members'])) {
                $operation->members()->sync($validated['members']);
            }

            // Delete selected photos if requested
            if ($request->has('delete_photos') && is_array($request->input('delete_photos'))) {
                foreach ($request->input('delete_photos') as $photoId) {
                    $photoRecord = OperationPhoto::where('operation_record_id', $operation->id)->find($photoId);
                    if ($photoRecord) {
                        $fullPath = public_path($photoRecord->file_path);
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                        $photoRecord->delete();
                    }
                }
            }

            // Upload additional photos if any
            if ($request->hasFile('photos')) {
                $uploadDir = public_path('uploads/operations');
                if (!file_exists($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                foreach ($request->file('photos') as $photo) {
                    if (!$photo->isValid()) continue;
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    try {
                        $photo->move($uploadDir, $filename);
                        OperationPhoto::create([
                            'operation_record_id' => $operation->id,
                            'file_path' => 'uploads/operations/' . $filename,
                        ]);
                    } catch (\Exception $ex) {
                        \Illuminate\Support\Facades\Log::error("Gagal move foto $filename: " . $ex->getMessage());
                    }
                }
            }

            // Record log edit
            OperationRecordLog::create([
                'operation_record_id' => $operation->id,
                'user_id'             => Auth::id(),
                'action'              => 'edit',
                'details'             => 'Mengubah / memperbarui rekam operasi',
            ]);

            DB::commit();

            return redirect()->route('staff.operations.show', $operation->id)->with('success', 'Rekam operasi berhasil diperbarui / dilengkapi.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Gagal memperbarui rekam operasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui rekam operasi: ' . $e->getMessage())->withInput();
        }
    }

    // API for Member Search (Select2 / Autocomplete)
    public function searchMembers(Request $request)
    {
        $this->checkPermission();
        $userHospital = strtolower(trim(Auth::user()->hospital ?? ''));
        
        $search = trim($request->get('q', ''));
        
        $query = User::query()->where('is_active', true);

        if ($userHospital !== '') {
            $query->where(function($q) use ($userHospital) {
                $q->where('hospital', $userHospital)
                  ->orWhere('hospital', 'like', '%' . $userHospital . '%')
                  ->orWhereNull('hospital')
                  ->orWhere('hospital', '');
            });
        }
        
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('staff_id', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->select('id', 'name', 'staff_id')->limit(25)->get();
        
        $formatted = $users->map(function ($user) {
            $label = $user->name;
            if ($user->staff_id) {
                $label .= ' (' . $user->staff_id . ')';
            }
            return ['id' => $user->id, 'text' => $label];
        });
        
        return response()->json($formatted);
    }
}
