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

        $staffList = User::where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->with(['role', 'medicRole'])
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'citizen_id', 'role_id', 'medic_role_id', 'batch', 'is_active']);

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
            'expiry_date'        => 'nullable|date',
            'notes'              => 'nullable|string|max:500',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('certifications/ie', 'public');
        }

        // Auto-generate nomor kontrak resmi IE jika tidak diisi manual
        $certNumber = $validated['certificate_number'] ?? null;
        if (empty($certNumber)) {
            $issueDate = \Carbon\Carbon::parse($validated['issue_date'] ?? now());
            $monthRomans = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
            $roman = $monthRomans[(int)$issueDate->format('n')] ?? 'IX';
            $year = $issueDate->format('Y');
            $count = MemberCertification::where('type', 'medical_contract')->whereYear('issue_date', $year)->count() + 1;
            $certNumber = sprintf('%03d/IER-IMC/KK/%s/%s', $count, $roman, $year);
        }

        $cert = MemberCertification::create([
            'user_id'            => $validated['user_id'],
            'type'               => 'medical_contract',
            'division'           => 'ie',
            'title'              => $validated['title'],
            'certificate_number' => $certNumber,
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
