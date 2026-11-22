<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Voting;
use App\Models\VotingCandidate;
use App\Models\VotingVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotingController extends Controller
{
    /**
     * Bobot suara berdasarkan level role.
     * level 0-4 = staf biasa (1×)
     * level 5   = staff_manager (4×)
     * level 6+  = manajer / executive / admin (5×)
     */
    private function getVoteWeight($user): int
    {
        if ($user->isAdmin()) {
            return 5;
        }
        $level = $user->role?->level ?? 0;
        if ($level >= 6) return 5; // manajer, executive
        if ($level === 5) return 4; // staff_manager
        return 1; // semua staf biasa
    }

    /**
     * Display voting list for staff members (with hospital tab filtering).
     * Voting bertipe 'management' disembunyikan dari staf biasa.
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $hospital = $request->query('hospital', 'all');
        $isManagement = $user->isAdmin() || ($user->role?->level ?? 0) >= 5;

        $query = Voting::with(['candidates.user', 'votes'])
            ->where('status', '!=', 'draft');

        // Sembunyikan voting management-only dari staf biasa
        if (!$isManagement) {
            $query->where('audience', 'all');
        }

        if ($hospital === 'alta') {
            $query->whereIn('hospital', ['alta', 'all']);
        } elseif ($hospital === 'roxwood') {
            $query->whereIn('hospital', ['roxwood', 'all']);
        }

        $votings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats counts (sesuai akses user)
        $baseQuery = Voting::where('status', '!=', 'draft');
        if (!$isManagement) {
            $baseQuery->where('audience', 'all');
        }

        $altaCount    = (clone $baseQuery)->whereIn('hospital', ['alta', 'all'])->count();
        $roxwoodCount = (clone $baseQuery)->whereIn('hospital', ['roxwood', 'all'])->count();
        $activeCount  = (clone $baseQuery)->where('status', 'active')->count();

        return view('staff.voting.index', compact('votings', 'hospital', 'altaCount', 'roxwoodCount', 'activeCount'));
    }

    /**
     * Display a specific voting session (detail & cast vote interface).
     */
    public function show($id)
    {
        $voting = Voting::with(['candidates.user', 'candidates.votes', 'votes.voter'])->findOrFail($id);
        $user   = Auth::user();

        // Blokir staf biasa dari voting management-only
        if (!$voting->canUserAccess($user)) {
            abort(403, 'Voting ini hanya dapat diakses oleh Staff Manager dan Manajer.');
        }

        $hasVoted        = $voting->hasUserVoted($user->id);
        $votedCandidateId = $voting->getUserVoteCandidateId($user->id);
        $userWeight      = $this->getVoteWeight($user);

        return view('staff.voting.show', compact('voting', 'hasVoted', 'votedCandidateId', 'userWeight'));
    }

    /**
     * Submit vote for candidate.
     */
    public function vote(Request $request, $id)
    {
        $voting = Voting::findOrFail($id);
        $user   = Auth::user();

        if (!$voting->canUserAccess($user)) {
            return redirect()->back()->with('error', 'Voting ini hanya dapat diakses oleh Staff Manager dan Manajer.');
        }

        if ($voting->status !== 'active') {
            return redirect()->back()->with('error', 'Sesi voting ini sedang tidak aktif atau sudah ditutup.');
        }

        if ($voting->hasUserVoted($user->id)) {
            return redirect()->back()->with('error', 'Anda sudah memberikan suara. Gunakan tombol "Batalkan Suara" untuk mengganti pilihan.');
        }

        $request->validate([
            'candidate_id' => 'required|exists:voting_candidates,id',
        ]);

        $candidate = VotingCandidate::where('id', $request->candidate_id)
            ->where('voting_id', $voting->id)
            ->firstOrFail();

        VotingVote::create([
            'voting_id'    => $voting->id,
            'candidate_id' => $candidate->id,
            'user_id'      => $user->id,
            'weight'       => $this->getVoteWeight($user),
        ]);

        return redirect()->route('staff.voting.show', $voting->id)
            ->with('success', 'Terima kasih! Suara Anda (bobot ' . $this->getVoteWeight($user) . '×) telah berhasil disalurkan.');
    }

    /**
     * Cancel vote — hapus suara user dari voting ini selama masih aktif.
     */
    public function cancelVote($id)
    {
        $voting = Voting::findOrFail($id);
        $user   = Auth::user();

        if ($voting->status !== 'active') {
            return redirect()->back()->with('error', 'Suara hanya bisa dibatalkan selama voting masih aktif.');
        }

        $deleted = VotingVote::where('voting_id', $voting->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            return redirect()->route('staff.voting.show', $voting->id)
                ->with('success', 'Suara Anda berhasil dibatalkan. Silakan pilih kandidat lain.');
        }

        return redirect()->back()->with('error', 'Tidak ada suara yang ditemukan untuk dibatalkan.');
    }

    /**
     * Admin Index - List all voting sessions.
     */
    public function adminIndex(Request $request)
    {
        $hospital = $request->query('hospital', 'all');
        $status = $request->query('status', 'all');

        $query = Voting::with(['creator', 'candidates', 'votes']);

        if ($hospital !== 'all') {
            $query->where('hospital', $hospital);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $votings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.voting.index', compact('votings', 'hospital', 'status'));
    }

    /**
     * Show form to create new voting session.
     */
    public function create()
    {
        // Get registered users to populate candidate select dropdown
        $users = User::with('role')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.voting.create', compact('users'));
    }

    /**
     * Store a newly created voting session in DB.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'hospital' => 'required|in:alta,roxwood,all',
            'audience' => 'required|in:all,management',
            'status' => 'required|in:draft,active,closed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'candidates' => 'required|array|min:2',
            'candidates.*.user_id' => 'nullable|exists:users,id',
            'candidates.*.name' => 'required|string|max:255',
            'candidates.*.custom_role' => 'nullable|string|max:255',
            'candidates.*.vision_mission' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $voting = Voting::create([
                'title' => $request->title,
                'target_position' => $request->target_position,
                'description' => $request->description,
                'hospital' => $request->hospital,
                'audience' => $request->audience,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->candidates as $candData) {
                // If user_id is provided, auto fill candidate photo if available from user model
                $photo = null;
                if (!empty($candData['user_id'])) {
                    $userObj = User::find($candData['user_id']);
                    if ($userObj && $userObj->profile_image) {
                        $photo = $userObj->profile_image;
                    }
                }

                VotingCandidate::create([
                    'voting_id' => $voting->id,
                    'user_id' => $candData['user_id'] ?? null,
                    'name' => $candData['name'],
                    'custom_role' => $candData['custom_role'] ?? null,
                    'vision_mission' => $candData['vision_mission'] ?? null,
                    'photo' => $photo,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.voting.index')->with('success', 'Sesi voting baru berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat sesi voting: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $voting = Voting::with('candidates')->findOrFail($id);
        $users = User::with('role')->where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.voting.edit', compact('voting', 'users'));
    }

    /**
     * Update voting session.
     */
    public function update(Request $request, $id)
    {
        $voting = Voting::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'target_position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'hospital' => 'required|in:alta,roxwood,all',
            'audience' => 'required|in:all,management',
            'status' => 'required|in:draft,active,closed',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'candidates' => 'required|array|min:2',
            'candidates.*.name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $voting->update([
                'title' => $request->title,
                'target_position' => $request->target_position,
                'description' => $request->description,
                'hospital' => $request->hospital,
                'audience' => $request->audience,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            // Replace candidates
            $voting->candidates()->delete();

            foreach ($request->candidates as $candData) {
                $photo = null;
                if (!empty($candData['user_id'])) {
                    $userObj = User::find($candData['user_id']);
                    if ($userObj && $userObj->profile_image) {
                        $photo = $userObj->profile_image;
                    }
                }

                VotingCandidate::create([
                    'voting_id' => $voting->id,
                    'user_id' => $candData['user_id'] ?? null,
                    'name' => $candData['name'],
                    'custom_role' => $candData['custom_role'] ?? null,
                    'vision_mission' => $candData['vision_mission'] ?? null,
                    'photo' => $photo,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.voting.index')->with('success', 'Sesi voting berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Toggle status (Start / Pause / Close voting).
     */
    public function toggleStatus(Request $request, $id)
    {
        $voting = Voting::findOrFail($id);
        $status = $request->input('status');

        if (in_array($status, ['draft', 'active', 'closed'])) {
            $voting->status = $status;

            if ($status === 'active' && !$voting->start_date) {
                $voting->start_date = now();
            } elseif ($status === 'closed' && !$voting->end_date) {
                $voting->end_date = now();
            }

            $voting->save();

            $statusText = [
                'active' => 'Sesi voting telah DIMULAI',
                'closed' => 'Sesi voting telah DITUTUP',
                'draft' => 'Status voting diubah ke DRAFT',
            ];

            return redirect()->back()->with('success', $statusText[$status] ?? 'Status voting berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    /**
     * Delete a voting session.
     */
    public function destroy($id)
    {
        $voting = Voting::findOrFail($id);
        $voting->delete();

        return redirect()->route('admin.voting.index')->with('success', 'Sesi voting berhasil dihapus.');
    }
}
