<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ChatWidget extends Component
{
    public $sessionToken;
    public $name;
    public $message;
    public $isOpen = false;
    public $hasSession = false;
    // Renamed from messages to chatMessages to avoid conflict with Livewire validation
    public $chatMessages = [];

    public function mount()
    {
        // Auto-fill name if logged in
        if (Auth::check()) {
            $this->name = Auth::user()->name;
        }

        // Check for existing session in session/cookie
        $token = session('chat_session_token');
        $this->sessionToken = $token ? (string) $token : null;

        if ($this->sessionToken) {
            $session = ChatSession::where('session_token', $this->sessionToken)->first();
            if ($session && $session->status === 'open') {
                $this->hasSession = true;
                $this->name = $session->name;
                $this->loadMessages();
            } else {
                // If session exists but is closed, or doesn't exist
                session()->forget('chat_session_token');
                $this->sessionToken = null;
                $this->hasSession = false;
            }
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen && $this->hasSession) {
            $this->loadMessages();
        }
    }

    public function startChat()
    {
        $this->validate([
            'name' => 'required|min:3|max:50',
        ]);

        $this->sessionToken = (string) Str::uuid();

        $session = ChatSession::create([
            'session_token' => $this->sessionToken,
            'name' => $this->name,
            'user_id' => Auth::id(),
            'status' => 'open'
        ]);

        session(['chat_session_token' => $this->sessionToken]);
        $this->hasSession = true;
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|min:1|max:1000'
        ]);

        if (!$this->hasSession) {
            return;
        }

        $session = ChatSession::where('session_token', $this->sessionToken)->first();

        if (!$session) {
            $this->hasSession = false;
            return;
        }

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => Auth::id(), // Null if guest
            'message' => $this->message,
            'is_staff_reply' => false
        ]);

        // Mark session as unread for staff
        $session->update(['is_read' => false]);

        $this->message = '';
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if ($this->sessionToken) {
            // Only load if session matches token
            $session = ChatSession::where('session_token', $this->sessionToken)->first();

            if ($session) {
                // Check if session was closed remotely by admin
                if ($session->status === 'closed') {
                    $this->endSession();
                    return;
                }

                $this->chatMessages = $session->messages()->with('user')->orderBy('created_at', 'asc')->get();
            }
        }
    }

    public function endSession()
    {
        if ($this->sessionToken) {
            $session = ChatSession::where('session_token', $this->sessionToken)->first();
            if ($session) {
                $session->update(['status' => 'closed']);
            }
        }

        session()->forget('chat_session_token');
        $this->sessionToken = null;
        $this->hasSession = false;
        $this->chatMessages = [];
        $this->isOpen = false;
    }

    // Polling fallback
    public function render()
    {
        return view('livewire.chat-widget');
    }
}
