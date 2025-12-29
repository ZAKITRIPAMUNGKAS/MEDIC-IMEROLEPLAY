<?php

namespace App\Services;

use App\Models\MedicalForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormService
{
    /**
     * Get form detail by ID
     *
     * @param int $id
     * @return MedicalForm
     */
    public function getFormDetail(int $id): MedicalForm
    {
        return MedicalForm::with('processedBy')->findOrFail($id);
    }

    /**
     * Approve form
     *
     * @param int $id
     * @param string|null $notes
     * @param int $processedBy
     * @return array
     */
    public function approveForm(int $id, ?string $notes, int $processedBy): array
    {
        try {
            $form = MedicalForm::findOrFail($id);
            
            if ($form->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Formulir tidak dapat disetujui karena status bukan pending'
                ];
            }

            DB::beginTransaction();
            
            $form->update([
                'status' => 'approved',
                'processed_by' => $processedBy,
                'processed_at' => now(),
                'notes' => $notes
            ]);

            DB::commit();

            Log::info('Form approved', [
                'form_id' => $id,
                'processed_by' => $processedBy,
                'form_type' => $form->form_type
            ]);

            return [
                'success' => true,
                'message' => 'Formulir berhasil disetujui.',
                'data' => $form
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Form approval failed', [
                'form_id' => $id,
                'processed_by' => $processedBy,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui formulir: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reject form
     *
     * @param int $id
     * @param string|null $notes
     * @param int $processedBy
     * @return array
     */
    public function rejectForm(int $id, ?string $notes, int $processedBy): array
    {
        try {
            $form = MedicalForm::findOrFail($id);
            
            if ($form->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Formulir tidak dapat ditolak karena status bukan pending'
                ];
            }

            DB::beginTransaction();
            
            $form->update([
                'status' => 'rejected',
                'processed_by' => $processedBy,
                'processed_at' => now(),
                'notes' => $notes
            ]);

            DB::commit();

            Log::info('Form rejected', [
                'form_id' => $id,
                'processed_by' => $processedBy,
                'form_type' => $form->form_type
            ]);

            return [
                'success' => true,
                'message' => 'Formulir berhasil ditolak.',
                'data' => $form
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Form rejection failed', [
                'form_id' => $id,
                'processed_by' => $processedBy,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak formulir: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get form statistics
     *
     * @return array
     */
    public function getFormStatistics(): array
    {
        return [
            'total' => MedicalForm::count(),
            'pending' => MedicalForm::where('status', 'pending')->count(),
            'approved' => MedicalForm::where('status', 'approved')->count(),
            'rejected' => MedicalForm::where('status', 'rejected')->count(),
            'today' => MedicalForm::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Get forms by status
     *
     * @param string $status
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFormsByStatus(string $status, int $limit = 10)
    {
        return MedicalForm::with('processedBy')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get forms by type
     *
     * @param string $type
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFormsByType(string $type, int $limit = 10)
    {
        return MedicalForm::with('processedBy')
            ->where('form_type', $type)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent forms
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForms(int $limit = 5)
    {
        return MedicalForm::with('processedBy')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update form status
     *
     * @param int $id
     * @param string $status
     * @param string|null $notes
     * @param int $processedBy
     * @return array
     */
    public function updateFormStatus(int $id, string $status, ?string $notes, int $processedBy): array
    {
        $validStatuses = ['pending', 'approved', 'rejected'];
        
        if (!in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Status tidak valid'
            ];
        }

        try {
            $form = MedicalForm::findOrFail($id);
            
            DB::beginTransaction();
            
            $updateData = [
                'status' => $status,
                'processed_by' => $processedBy,
                'processed_at' => now()
            ];
            
            if ($notes) {
                $updateData['notes'] = $notes;
            }
            
            $form->update($updateData);

            DB::commit();

            Log::info('Form status updated', [
                'form_id' => $id,
                'status' => $status,
                'processed_by' => $processedBy
            ]);

            return [
                'success' => true,
                'message' => 'Status formulir berhasil diperbarui.',
                'data' => $form
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Form status update failed', [
                'form_id' => $id,
                'status' => $status,
                'processed_by' => $processedBy,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
            ];
        }
    }
}
