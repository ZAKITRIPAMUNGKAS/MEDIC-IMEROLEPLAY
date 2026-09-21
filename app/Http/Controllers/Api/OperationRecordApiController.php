<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperationRecord;
use App\Models\OperationRecordLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OperationRecordApiController extends Controller
{
    /**
     * GET /api/rekam-medis
     * Ambil semua rekam operasi dengan filter opsional.
     *
     * Query params (semua opsional):
     *   - hospital       : alta | roxwood
     *   - nama_pasien    : string (partial match)
     *   - citizen_id     : string (partial match di medical_details->pasien->citizen_id)
     *   - jenis_operasi  : Operasi Minor | Operasi Mayor | Emergency | Konsultasi Spesialisasi | Lainnya
     *   - bulan          : 1-12
     *   - tahun          : 4-digit year
     *   - per_page       : integer (default 15, max 100)
     */
    public function index(Request $request): JsonResponse
    {
        $query = OperationRecord::with(['creator:id,name,staff_id', 'dpjp:id,name,staff_id', 'members:id,name,staff_id', 'photos'])
            ->orderBy('tanggal_waktu', 'desc');

        // Filter hospital
        if ($request->filled('hospital')) {
            $query->where('hospital', strtolower($request->hospital));
        }

        // Filter nama pasien
        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        // Filter citizen_id dari JSON medical_details
        if ($request->filled('citizen_id')) {
            $query->where('medical_details->pasien->citizen_id', 'like', '%' . $request->citizen_id . '%');
        }

        // Filter jenis operasi
        if ($request->filled('jenis_operasi')) {
            $query->where('jenis_operasi', $request->jenis_operasi);
        }

        // Filter bulan & tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_waktu', $request->bulan)
                  ->whereYear('tanggal_waktu', $request->tahun);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('tanggal_waktu', $request->tahun);
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $records = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $records->map(fn($r) => $this->formatRecord($r)),
            'meta'    => [
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
            ],
        ]);
    }

    /**
     * GET /api/rekam-medis/{id}
     * Ambil detail satu rekam operasi berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        $record = OperationRecord::with(['creator:id,name,staff_id', 'dpjp:id,name,staff_id', 'members:id,name,staff_id', 'photos', 'logs.user:id,name,staff_id'])
            ->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Rekam medis tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatRecord($record, true),
        ]);
    }

    /**
     * GET /api/rekam-medis/pasien/{citizen_id}
     * Ambil semua rekam operasi milik satu pasien berdasarkan citizen_id.
     */
    public function byPatient(string $citizenId): JsonResponse
    {
        $records = OperationRecord::with(['creator:id,name,staff_id', 'dpjp:id,name,staff_id', 'members:id,name,staff_id'])
            ->where('medical_details->pasien->citizen_id', $citizenId)
            ->orderBy('tanggal_waktu', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'citizen_id' => $citizenId,
            'total'   => $records->count(),
            'data'    => $records->map(fn($r) => $this->formatRecord($r)),
        ]);
    }

    /**
     * POST /api/rekam-medis
     * Buat rekam operasi baru via API.
     *
     * Body JSON:
     *   tanggal_waktu, lokasi, jenis_operasi, hospital, nama_pasien,
     *   diagnosa, tindakan_operasi, hasil_operasi, catatan, dpjp_id,
     *   members (array of user IDs), medical_details (object)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_waktu'    => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'jenis_operasi'    => 'required|in:Operasi Minor,Operasi Mayor,Emergency,Konsultasi Spesialisasi,Lainnya',
            'hospital'         => 'required|in:alta,roxwood',
            'nama_pasien'      => 'required|string|max:255',
            'diagnosa'         => 'nullable|string',
            'tindakan_operasi' => 'nullable|string',
            'hasil_operasi'    => 'nullable|string',
            'catatan'          => 'nullable|string',
            'dpjp_id'          => 'nullable|exists:users,id',
            'members'          => 'nullable|array',
            'members.*'        => 'exists:users,id',
            'medical_details'  => 'nullable|array',
            'created_by'       => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $medicalDetails = $data['medical_details'] ?? [];

            $record = OperationRecord::create([
                'tanggal_waktu'    => $data['tanggal_waktu'],
                'lokasi'           => $data['lokasi'],
                'jenis_operasi'    => $data['jenis_operasi'],
                'hospital'         => $data['hospital'],
                'nama_pasien'      => $data['nama_pasien'],
                'diagnosa'         => $data['diagnosa'] ?? ($medicalDetails['anamnesis']['anamnesis_utama'] ?? null),
                'tindakan_operasi' => $data['tindakan_operasi'] ?? ($medicalDetails['tindakan']['nama_tindakan'] ?? null),
                'hasil_operasi'    => $data['hasil_operasi'] ?? ($medicalDetails['pasca_operasi']['kondisi_keluar'] ?? null),
                'catatan'          => $data['catatan'] ?? null,
                'created_by'       => $data['created_by'] ?? null,
                'dpjp_id'          => $data['dpjp_id'] ?? null,
                'medical_details'  => $medicalDetails,
            ]);

            if (!empty($data['members'])) {
                $record->members()->sync($data['members']);
            }

            OperationRecordLog::create([
                'operation_record_id' => $record->id,
                'user_id'             => $data['created_by'] ?? null,
                'action'              => 'create',
                'details'             => 'Rekam operasi dibuat via API',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekam operasi berhasil disimpan.',
                'data'    => $this->formatRecord($record->load(['creator:id,name,staff_id', 'dpjp:id,name,staff_id', 'members:id,name,staff_id'])),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan rekam operasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/rekam-medis/{id}
     * Update rekam operasi yang sudah ada.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $record = OperationRecord::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Rekam medis tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_waktu'    => 'sometimes|date',
            'lokasi'           => 'sometimes|string|max:255',
            'jenis_operasi'    => 'sometimes|in:Operasi Minor,Operasi Mayor,Emergency,Konsultasi Spesialisasi,Lainnya',
            'hospital'         => 'sometimes|in:alta,roxwood',
            'nama_pasien'      => 'sometimes|string|max:255',
            'diagnosa'         => 'nullable|string',
            'tindakan_operasi' => 'nullable|string',
            'hasil_operasi'    => 'nullable|string',
            'catatan'          => 'nullable|string',
            'dpjp_id'          => 'nullable|exists:users,id',
            'members'          => 'nullable|array',
            'members.*'        => 'exists:users,id',
            'medical_details'  => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();

            // Merge medical_details agar field yang tidak dikirim tidak hilang
            if (isset($data['medical_details'])) {
                $data['medical_details'] = array_replace_recursive(
                    $record->medical_details ?? [],
                    $data['medical_details']
                );
            }

            $record->update($data);

            if (isset($data['members'])) {
                $record->members()->sync($data['members']);
            }

            OperationRecordLog::create([
                'operation_record_id' => $record->id,
                'user_id'             => null,
                'action'              => 'edit',
                'details'             => 'Rekam operasi diperbarui via API',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekam operasi berhasil diperbarui.',
                'data'    => $this->formatRecord($record->fresh(['creator:id,name,staff_id', 'dpjp:id,name,staff_id', 'members:id,name,staff_id'])),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekam operasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/rekam-medis/{id}
     * Hapus rekam operasi (butuh API key).
     */
    public function destroy(int $id): JsonResponse
    {
        $record = OperationRecord::find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Rekam medis tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            // Hapus foto fisik
            foreach ($record->photos as $photo) {
                $fullPath = public_path($photo->file_path);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $record->photos()->delete();
            $record->members()->detach();
            $record->logs()->delete();
            $record->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekam operasi berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekam operasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format satu record menjadi array respons JSON.
     */
    private function formatRecord(OperationRecord $record, bool $withLogs = false): array
    {
        $data = [
            'id'               => $record->id,
            'tanggal_waktu'    => $record->tanggal_waktu?->toIso8601String(),
            'lokasi'           => $record->lokasi,
            'jenis_operasi'    => $record->jenis_operasi,
            'hospital'         => $record->hospital,
            'nama_pasien'      => $record->nama_pasien,
            'diagnosa'         => $record->diagnosa,
            'tindakan_operasi' => $record->tindakan_operasi,
            'hasil_operasi'    => $record->hasil_operasi,
            'catatan'          => $record->catatan,
            'medical_details'  => $record->medical_details,
            'creator'          => $record->relationLoaded('creator') && $record->creator ? [
                'id'       => $record->creator->id,
                'name'     => $record->creator->name,
                'staff_id' => $record->creator->staff_id,
            ] : null,
            'dpjp'             => $record->relationLoaded('dpjp') && $record->dpjp ? [
                'id'       => $record->dpjp->id,
                'name'     => $record->dpjp->name,
                'staff_id' => $record->dpjp->staff_id,
            ] : null,
            'members'          => $record->relationLoaded('members')
                ? $record->members->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'staff_id' => $m->staff_id])->values()
                : [],
            'photos'           => $record->relationLoaded('photos')
                ? $record->photos->map(fn($p) => ['id' => $p->id, 'url' => asset($p->file_path)])->values()
                : [],
            'poin'             => [
                'base'  => $record->base_points,
                'dpjp'  => $record->dpjp_points,
            ],
            'created_at'       => $record->created_at?->toIso8601String(),
            'updated_at'       => $record->updated_at?->toIso8601String(),
        ];

        if ($withLogs && $record->relationLoaded('logs')) {
            $data['logs'] = $record->logs->map(fn($l) => [
                'id'      => $l->id,
                'action'  => $l->action,
                'details' => $l->details,
                'user'    => $l->user ? ['id' => $l->user->id, 'name' => $l->user->name] : null,
                'waktu'   => $l->created_at?->toIso8601String(),
            ])->values();
        }

        return $data;
    }
}
