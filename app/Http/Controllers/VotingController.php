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
     * Display voting list for staff members (with hospital tab filtering).
     */
    public function index(Request $request)
    {
        $hospital = $request->query('hospital', 'all');

        $query = Voting::with(['candidates.user', 'votes'])
            ->where('status', '!=', 'draft');

        if ($hospital === 'alta') {
            $query->whereIn('hospital', ['alta', 'all']);
        } elseif ($hospital === 'roxwood') {
            $query->whereIn('hospital', ['roxwood', 'all']);
        }

        $votings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats counts
        $altaCount = Voting::where('status', '!=', 'draft')
            ->whereIn('hospital', ['alta', 'all'])
            ->count();

        $roxwoodCount = Voting::where('status', '!=', 'draft')
            ->whereIn('hospital', ['roxwood', 'all'])
            ->count();

        $activeCount = Voting::where('status', 'active')->count();

        return view('staff.voting.index', compact('votings', 'hospital', 'altaCount', 'roxwoodCount', 'activeCount'));
    }

    /**
     * Display a specific voting session (detail & cast vote interface).
     */
    public function show($id)
    {
        $voting = Voting::with(['candidates.user', 'candidates.votes', 'votes.voter'])->findOrFail($id);
        $user = Auth::user();

        $hasVoted = $voting->hasUserVoted($user->id);
        $votedCandidateId = $voting->getUserVoteCandidateId($user->id);

        return view('staff.voting.show', compact('voting', 'hasVoted', 'votedCandidateId'));
    }

    /**
     * Submit vote for candidate.
     */
    public function vote(Request $request, $id)
    {
        $voting = Voting::findOrFail($id);
        $user = Auth::user();

        if ($voting->status !== 'active') {
            return redirect()->back()->with('error', 'Sesi voting ini sedang tidak aktif atau sudah ditutup.');
        }

        if ($voting->hasUserVoted($user->id)) {
            return redirect()->back()->with('error', 'Anda sudah memberikan suara pada sesi voting ini.');
        }

        $request->validate([
            'candidate_id' => 'required|exists:voting_candidates,id',
        ]);

        // Ensure candidate belongs to this voting
        $candidate = VotingCandidate::where('id', $request->candidate_id)
            ->where('voting_id', $voting->id)
            ->firstOrFail();

        VotingVote::create([
            'voting_id' => $voting->id,
            'candidate_id' => $candidate->id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('staff.voting.show', $voting->id)
            ->with('success', 'Terima kasih! Suara Anda telah berhasil disalurkan.');
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
