<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationRecord;
use Illuminate\Http\Request;

class OperationRecordController extends Controller
{
    /**
     * Admin inherits functionality but has delete capabilities
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $role = strtolower(trim($user->role->name ?? ''));
        $canDelete = $user->isAdmin() || in_array($role, ['ceo', 'executive', 'direktur', 'high_command']);

        if (!$canDelete) {
            abort(403, 'Akses ditolak: Hanya Admin dan Direktur/CEO yang dapat menghapus rekam operasi.');
        }

        $operation = OperationRecord::findOrFail($id);
        
        // Hapus foto dari public/uploads/operations/
        foreach ($operation->photos as $photo) {
            // file_path = "uploads/operations/filename.jpg"
            $fileFull = public_path($photo->file_path);
            if (file_exists($fileFull)) {
                @unlink($fileFull);
            }
        }
        
        $operation->delete();
        
        return redirect()->route('staff.operations.index')->with('success', 'Rekam Operasi berhasil dihapus.');
    }
}
