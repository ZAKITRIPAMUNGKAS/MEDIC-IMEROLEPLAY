<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AdminChat extends Component
{
    public $activeSessionId;
    public $replyMessage;
    public $filterStatus = 'open'; // open, closed, all

    // Derived properties
    public $sessions = [];
    public $activeSession;
    public $chatMessages = [];

    public function mount()
    {
        if (!Auth::user()->canReplyChat()) {
            abort(403, 'Unauthorized');
        }
        $this->loadSessions();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'filterStatus') {
            $this->loadSessions();
        }
    }



    public function loadSessions()
    {
        $query = ChatSession::with('messages');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $this->sessions = $query->orderBy('is_read', 'asc') // Unread first
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($this->activeSessionId) {
            $this->loadMessages();
        }
    }

    public function selectSession($id)
    {
        $this->activeSessionId = $id;
        $this->activeSession = ChatSession::find($id);

        if ($this->activeSession) {
            // Mark as read
            $this->activeSession->update(['is_read' => true]);
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        if ($this->activeSessionId) {
            $this->chatMessages = ChatMessage::where('chat_session_id', $this->activeSessionId)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
        }
    }

    public function sendReply()
    {
        $this->validate([
            'replyMessage' => 'required|min:1'
        ]);

        if (!$this->activeSession)
            return;

        ChatMessage::create([
            'chat_session_id' => $this->activeSessionId,
            'user_id' => Auth::id(),
            'message' => $this->replyMessage,
            'is_staff_reply' => true
        ]);

        $this->activeSession->touch(); // Update updated_at
        $this->replyMessage = '';
        $this->loadMessages();
    }

    public function closeSession()
    {
        if ($this->activeSession) {
            $this->activeSession->update(['status' => 'closed']);
            $this->loadSessions();
            $this->activeSessionId = null;
            $this->activeSession = null; // Reset active session
            $this->chatMessages = []; // Clear messages
        }
    }

    public function render()
    {
        return view('livewire.admin-chat');
    }
}
