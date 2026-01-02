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
    public $selectedFeedback = null;
    public $adminNotes = '';

    public function mount()
    {
        if (!Auth::user()->hasPermission('access_feedback')) {
            abort(403, 'Unauthorized');
        }
        $this->loadFeedback();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterStatus', 'filterType'])) {
            $this->loadFeedback();
            $this->selectedFeedback = null;
        }
    }

    public function loadFeedback()
    {
        $query = Feedback::with(['user', 'reviewer']);

        // Apply filters
        $query->status($this->filterStatus);
        $query->type($this->filterType);

        $this->feedbackList = $query->orderBy('status', 'asc') // New first
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectFeedback($id)
    {
        Log::info("Selecting feedback ID: " . $id);
        $this->selectedFeedback = Feedback::with(['user', 'reviewer'])->find($id);
        $this->adminNotes = $this->selectedFeedback?->notes ?? '';
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
            $feedback->delete();

            if ($this->selectedFeedback && $this->selectedFeedback->id == $id) {
                $this->selectedFeedback = null;
            }

            $this->loadFeedback();
        }
    }

    public function render()
    {
        // Get statistics
        $stats = [
            'total' => Feedback::count(),
            'new' => Feedback::where('status', 'new')->count(),
            'reviewed' => Feedback::where('status', 'reviewed')->count(),
            'resolved' => Feedback::where('status', 'resolved')->count(),
            'kritik' => Feedback::where('type', 'kritik')->count(),
            'saran' => Feedback::where('type', 'saran')->count(),
        ];

        return view('livewire.feedback-list', compact('stats'));
    }
}
