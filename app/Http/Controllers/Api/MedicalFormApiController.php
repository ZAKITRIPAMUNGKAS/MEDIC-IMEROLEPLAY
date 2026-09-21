<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalForm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MedicalFormApiController extends Controller
{
    /**
     * GET /api/medical-forms
     * Ambil semua form medis / rekam medis pasien dengan filter opsional.
     *
     * Query params (semua opsional):
     *   - hospital        : alta | roxwood
     *   - status          : pending | approved | rejected
     *   - form_type       : surat_kesehatan | tes_psikologi | surat_psikolog | operasi_plastik | janji_temu | pendaftaran_karakter
     *   - character_name  : string (partial match)
     *   - citizen_id      : string (partial match)
     *   - per_page        : integer (default 15, max 100)
     */
    public function index(Request $request): JsonResponse
    {
        $query = MedicalForm::with(['processedBy:id,name,staff_id'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('hospital')) {
            $query->where('hospital', strtolower($request->hospital));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        if ($request->filled('character_name')) {
            $query->where('character_name', 'like', '%' . $request->character_name . '%');
        }

        if ($request->filled('citizen_id')) {
            $query->where('citizen_id', 'like', '%' . $request->citizen_id . '%');
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $forms = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $forms->map(fn($f) => $this->formatForm($f)),
            'meta'    => [
                'current_page' => $forms->currentPage(),
                'last_page'    => $forms->lastPage(),
                'per_page'     => $forms->perPage(),
                'total'        => $forms->total(),
            ],
        ]);
    }

    /**
     * GET /api/medical-forms/{id}
     * Ambil detail satu form medis berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        $form = MedicalForm::with(['processedBy:id,name,staff_id'])->find($id);

        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form medis tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatForm($form),
        ]);
    }

    /**
     * GET /api/medical-forms/pasien/{citizen_id}
     * Ambil semua riwayat form medis milik satu pasien berdasarkan citizen_id.
     */
    public function byPatient(string $citizenId): JsonResponse
    {
        $forms = MedicalForm::with(['processedBy:id,name,staff_id'])
            ->where('citizen_id', $citizenId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success'    => true,
            'citizen_id' => $citizenId,
            'total'      => $forms->count(),
            'data'       => $forms->map(fn($f) => $this->formatForm($f)),
        ]);
    }

    /**
     * GET /api/medical-forms/testimoni
     * Ambil semua testimoni pasien yang sudah disetujui.
     */
    public function testimoni(Request $request): JsonResponse
    {
        $query = MedicalForm::approvedTestimonials()
            ->orderBy('updated_at', 'desc');

        if ($request->filled('hospital')) {
            $query->where('hospital', strtolower($request->hospital));
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $forms = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $forms->map(fn($f) => [
                'id'             => $f->id,
                'character_name' => $f->character_name,
                'form_type'      => $f->form_type,
                'hospital'       => $f->hospital,
                'testimoni'      => $f->testimoni,
                'rating'         => $f->rating,
                'created_at'     => $f->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $forms->currentPage(),
                'last_page'    => $forms->lastPage(),
                'per_page'     => $forms->perPage(),
                'total'        => $forms->total(),
            ],
        ]);
    }

    /**
     * POST /api/medical-forms
     * Buat form medis baru via API (misalnya dari website lain).
     *
     * Body JSON:
     *   character_name, citizen_id, form_type, hospital, description,
     *   form_data (object), linked_form_id
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'character_name' => 'required|string|max:255',
            'citizen_id'     => 'nullable|string|max:100',
            'form_type'      => 'required|in:surat_kesehatan,tes_psikologi,surat_psikolog,operasi_plastik,janji_temu,pendaftaran_karakter',
            'hospital'       => 'required|in:alta,roxwood',
            'description'    => 'nullable|string',
            'form_data'      => 'nullable|array',
            'linked_form_id' => 'nullable|exists:medical_forms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['status']     = 'pending';
        $data['ip_address'] = $request->ip();

        $form = MedicalForm::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Form medis berhasil dikirim.',
            'data'    => $this->formatForm($form),
        ], 201);
    }

    /**
     * PATCH /api/medical-forms/{id}/status
     * Update status form medis (approve / reject).
     *
     * Body JSON:
     *   status       : approved | rejected
     *   notes        : string (opsional)
     *   processed_by : user id (opsional)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $form = MedicalForm::find($id);

        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form medis tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status'       => 'required|in:approved,rejected',
            'notes'        => 'nullable|string',
            'processed_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $form->update([
            'status'       => $request->status,
            'notes'        => $request->notes,
            'processed_by' => $request->processed_by,
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status form medis berhasil diperbarui.',
            'data'    => $this->formatForm($form->fresh('processedBy')),
        ]);
    }

    /**
     * DELETE /api/medical-forms/{id}
     * Hapus form medis (butuh API key).
     */
    public function destroy(int $id): JsonResponse
    {
        $form = MedicalForm::find($id);

        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form medis tidak ditemukan.'], 404);
        }

        $form->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form medis berhasil dihapus.',
        ]);
    }

    /**
     * Format satu MedicalForm menjadi array respons JSON.
     */
    private function formatForm(MedicalForm $form): array
    {
        return [
            'id'                  => $form->id,
            'character_name'      => $form->character_name,
            'citizen_id'          => $form->citizen_id,
            'form_type'           => $form->form_type,
            'hospital'            => $form->hospital,
            'description'         => $form->description,
            'form_data'           => $form->form_data,
            'status'              => $form->status,
            'notes'               => $form->notes,
            'linked_form_id'      => $form->linked_form_id,
            'processed_by'        => $form->relationLoaded('processedBy') && $form->processedBy ? [
                'id'       => $form->processedBy->id,
                'name'     => $form->processedBy->name,
                'staff_id' => $form->processedBy->staff_id,
            ] : null,
            'processed_at'        => $form->processed_at?->toIso8601String(),
            'testimoni'           => $form->testimoni,
            'rating'              => $form->rating,
            'testimoni_approved'  => (bool) $form->testimoni_approved,
            'created_at'          => $form->created_at?->toIso8601String(),
            'updated_at'          => $form->updated_at?->toIso8601String(),
        ];
    }
}
