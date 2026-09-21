<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IeContractController extends Controller
{
    private function checkIsIe(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('ie')) {
            abort(403, 'Hanya divisi IE yang dapat menerbitkan Surat Perjanjian Kontrak Medis.');
        }
    }

    /**
     * GET /portal/ie/contracts
     * Daftar seluruh surat perjanjian kontrak medis.
     */
    public function index(Request $request)
    {
        $this->checkIsIe();
        $user = Auth::user();

        $query = MemberCertification::with(['user:id,name,staff_id', 'issuedBy:id,name'])
            ->where('type', 'medical_contract')
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($search = $request->get('q')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('staff_id', 'like', "%{$search}%"));
        }

        $contracts = $query->paginate(30)->withQueryString();

        $staffList = User::where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id']);

        return view('portal.ie.index', compact('contracts', 'staffList'));
    }

    /**
     * POST /portal/ie/contracts
     * Terbitkan surat perjanjian kontrak medis — otomatis sync ke profil anggota.
     */
    public function store(Request $request)
    {
        $this->checkIsIe();

        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'title'              => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:100',
            'issue_date'         => 'required|date',
            'expiry_date'        => 'nullable|date|after_or_equal:issue_date',
            'notes'              => 'nullable|string|max:500',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('certifications/ie', 'public');
        }

        $cert = MemberCertification::create([
            'user_id'            => $validated['user_id'],
            'type'               => 'medical_contract',
            'division'           => 'ie',
            'title'              => $validated['title'],
            'certificate_number' => $validated['certificate_number'] ?? null,
            'issued_by_user_id'  => Auth::id(),
            'issue_date'         => $validated['issue_date'],
            'expiry_date'        => $validated['expiry_date'] ?? null,
            'file_path'          => $filePath,
            'notes'              => $validated['notes'] ?? null,
            'status'             => 'active',
        ]);

        if (empty($filePath)) {
            $filePath = \App\Services\CertificateGeneratorService::generate($cert);
            $cert->update(['file_path' => $filePath]);
        }

        return redirect()->route('portal.ie.index')
            ->with('success', 'Surat Perjanjian Kontrak Medis berhasil diterbitkan dan otomatis sinkron ke profil anggota.');
    }

    /**
     * POST /portal/ie/contracts/{certification}/revoke
     */
    public function revoke(MemberCertification $certification)
    {
        $this->checkIsIe();
        abort_unless($certification->type === 'medical_contract', 404);

        $certification->update(['status' => 'revoked']);
        return back()->with('success', 'Kontrak medis ' . $certification->user->name . ' dicabut.');
    }
}
