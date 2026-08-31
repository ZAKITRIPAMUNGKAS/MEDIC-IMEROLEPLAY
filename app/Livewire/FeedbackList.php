<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

use Illuminate\Support\Facades\Log;

class FeedbackList extends Component
{
    public $feedbackList = [];
    public $filterStatus = 'all';
    public $filterType = 'all';
    public $filterHospital = 'all';
    public $filterReporterType = 'all';
    public $selectedFeedback = null;
    public $adminNotes = '';

    public function mount()
    {
        $user = Auth::user();
        if (!$user || !$user->hasPermission('access_feedback')) {
            abort(403, 'Unauthorized');
        }

        // Set default hospital filter based on user's assignment
        if (!$user->isAdmin()) {
            $this->filterHospital = $user->isRoxwood() ? 'roxwood' : 'alta';
        } else {
            $this->filterHospital = 'all';
        }

        $this->loadFeedback();
    }

    public function updated($propertyName)
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            // Lock hospital to user's branch for non-admins
            $this->filterHospital = $user->isRoxwood() ? 'roxwood' : 'alta';
        }

        if (in_array($propertyName, ['filterStatus', 'filterType', 'filterHospital', 'filterReporterType'])) {
            $this->loadFeedback();
            $this->selectedFeedback = null;
        }
    }

    public function loadFeedback()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('feedback', 'hospital')) {
                \Illuminate\Support\Facades\Schema::table('feedback', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('hospital')->default('alta')->nullable();
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('feedback', 'reporter_type')) {
                \Illuminate\Support\Facades\Schema::table('feedback', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('reporter_type')->default('warga')->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Ignore schema alter exception
        }

        try {
            $user = Auth::user();
            $query = Feedback::with(['user', 'reviewer']);

            // Strict hospital isolation for non-admins
            if ($user && !$user->isAdmin()) {
                if ($user->isRoxwood()) {
                    $query->where('hospital', 'roxwood');
                } else {
                    $query->where(function($q) {
                        $q->where('hospital', 'alta')
                          ->orWhere('hospital', 'Alta Hospital')
                          ->orWhere('hospital', 'Alta Street Hospital')
                          ->orWhereNull('hospital');
                    });
                }
            } else {
                $query->hospital($this->filterHospital);
            }

            // Apply other filters
            $query->status($this->filterStatus);
            $query->type($this->filterType);
            $query->reporterType($this->filterReporterType);

            $this->feedbackList = $query->orderBy('status', 'asc') // New first
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $this->feedbackList = Feedback::orderBy('created_at', 'desc')->get();
        }
    }

    public function selectFeedback($id)
    {
        Log::info("Selecting feedback ID: " . $id);
        $feedback = Feedback::with(['user', 'reviewer'])->find($id);
        
        if ($feedback) {
            $user = Auth::user();
            if ($user && !$user->isAdmin()) {
                // Prevent viewing feedback from the other hospital
                if ($user->isRoxwood() && ($feedback->hospital ?? 'alta') !== 'roxwood') {
                    return;
                }
                if ($user->isAlta() && ($feedback->hospital ?? 'alta') === 'roxwood') {
                    return;
                }
            }
            
            $this->selectedFeedback = $feedback;
            $this->adminNotes = $this->selectedFeedback?->notes ?? '';
        }
    }

    public function markAsReviewed()
    {
        if (!$this->selectedFeedback) {
            return;
        }

        $this->selectedFeedback->update([
            'status' => 'reviewed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        $this->loadFeedback();
        $this->selectedFeedback = Feedback::with(['user', 'reviewer'])->find($this->selectedFeedback->id);
    }

    public function markAsResolved()
    {
        $this->validate([
            'adminNotes' => 'nullable|max:1000'
        ]);

        if (!$this->selectedFeedback) {
            return;
        }

        $this->selectedFeedback->update([
            'status' => 'resolved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'notes' => $this->adminNotes
        ]);

        $this->loadFeedback();
        $this->selectedFeedback = Feedback::with(['user', 'reviewer'])->find($this->selectedFeedback->id);
    }

    public function markAsNew()
    {
        if (!$this->selectedFeedback) {
            return;
        }

        $this->selectedFeedback->update([
            'status' => 'new',
            'reviewed_by' => null,
            'reviewed_at' => null
        ]);

        $this->loadFeedback();
        $this->selectedFeedback = Feedback::with(['user', 'reviewer'])->find($this->selectedFeedback->id);
    }

    public function deleteFeedback($id)
    {
        $feedback = Feedback::find($id);

        if ($feedback) {
            $user = Auth::user();
            if ($user && !$user->isAdmin()) {
                if ($user->isRoxwood() && ($feedback->hospital ?? 'alta') !== 'roxwood') {
                    return;
                }
                if ($user->isAlta() && ($feedback->hospital ?? 'alta') === 'roxwood') {
                    return;
                }
            }

            $feedback->delete();

            if ($this->selectedFeedback && $this->selectedFeedback->id == $id) {
                $this->selectedFeedback = null;
            }

            $this->loadFeedback();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $statsQuery = Feedback::query();

        if ($user && !$user->isAdmin()) {
            if ($user->isRoxwood()) {
                $statsQuery->where('hospital', 'roxwood');
            } else {
                $statsQuery->where(function($q) {
                    $q->where('hospital', 'alta')
                      ->orWhere('hospital', 'Alta Hospital')
                      ->orWhere('hospital', 'Alta Street Hospital')
                      ->orWhereNull('hospital');
                });
            }
        }

        // Get statistics
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'new' => (clone $statsQuery)->where('status', 'new')->count(),
            'reviewed' => (clone $statsQuery)->where('status', 'reviewed')->count(),
            'resolved' => (clone $statsQuery)->where('status', 'resolved')->count(),
            'laporan' => (clone $statsQuery)->where('type', 'laporan')->count(),
            'masukan' => (clone $statsQuery)->where('type', 'masukan')->count(),
        ];

        return view('livewire.feedback-list', compact('stats'));
    }
}
