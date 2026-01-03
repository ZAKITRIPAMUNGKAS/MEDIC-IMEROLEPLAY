<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Feedback;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class ChatWidget extends Component
{
    use WithFileUploads;
    public $sessionToken;
    public $name;
    public $message;
    public $attachment;
    public $isOpen = false;
    public $hasSession = false;
    // Renamed from messages to chatMessages to avoid conflict with Livewire validation
    public $chatMessages = [];
    public $hasUnreadMessages = false;

    // Page mode - when true, always show chat interface (no popup)
    public $pageMode = false;

    // Feedback properties
    public $showFeedbackForm = false;
    public $feedbackType = 'saran';
    public $feedbackSubject = '';
    public $feedbackMessage = '';
    public $feedbackImage;
    public $feedbackSuccess = false;

    /**
     * Check if current user can reply to chats (has permission)
     */
    public function getCanReplyProperty()
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission('access_live_chat');
    }

    public function mount()
    {
        // Check for existing session in session/cookie
        $token = session('chat_session_token');
        $this->sessionToken = $token ? (string) $token : null;

        if ($this->sessionToken) {
            $session = ChatSession::where('session_token', $this->sessionToken)->first();
            if ($session && $session->status === 'open') {
                $this->hasSession = true;
                // Check if user has unread messages
                $this->hasUnreadMessages = !$session->is_user_read;

                // If in page mode, mark as read immediately
                if ($this->pageMode) {
                    $session->update(['is_user_read' => true]);
                    $this->hasUnreadMessages = false;
                }

                $this->loadMessages();
            } else {
                // If session exists but is closed, or doesn't exist
                session()->forget('chat_session_token');
                $this->sessionToken = null;
                $this->hasSession = false;
            }
        }

        // Auto-start chat if in page mode and no session
        if ($this->pageMode && !$this->hasSession) {
            $this->startChat();
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen && $this->hasSession) {
            // Mark as read when opening
            ChatSession::where('session_token', $this->sessionToken)->update(['is_user_read' => true]);
            $this->hasUnreadMessages = false;

            $this->loadMessages();
        }
    }

    public function startChat()
    {
        $this->sessionToken = (string) Str::uuid();

        // Generate ticket-style anonymous name
        $tempId = abs(crc32($this->sessionToken)) % 9999 + 1;
        $anonymousName = 'Ticket #' . str_pad($tempId, 4, '0', STR_PAD_LEFT);

        $session = ChatSession::create([
            'session_token' => $this->sessionToken,
            'name' => $anonymousName,
            'user_id' => Auth::id(),
            'status' => 'open'
        ]);

        session(['chat_session_token' => $this->sessionToken]);
        $this->hasSession = true;
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'nullable|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240'
        ], [
            'attachment.mimes' => 'File harus berupa gambar (JPG, PNG, GIF) atau dokumen (PDF, DOC, DOCX)',
            'attachment.max' => 'Ukuran file maksimal 10MB'
        ]);

        // Require either message or attachment
        if (empty($this->message) && !$this->attachment) {
            $this->addError('message', 'Pesan atau lampiran harus diisi');
            return;
        }

        if (!$this->hasSession) {
            return;
        }

        $session = ChatSession::where('session_token', $this->sessionToken)->first();

        if (!$session) {
            $this->hasSession = false;
            return;
        }

        // Handle file upload
        $attachmentPath = null;
        $attachmentType = null;

        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('chat-attachments', 'public');

            // Determine file type
            $mimeType = $this->attachment->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'document';
            }
        }

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => Auth::id(), // Null if guest
            'message' => $this->message ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'is_staff_reply' => false
        ]);

        // Mark session as unread for staff
        $session->update(['is_read' => false]);

        $this->message = '';
        $this->attachment = null;
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

                // Update local unread status from database
                // If chat is OPEN, we mark database as read.
                // If chat is CLOSED, we just update local state to show red dot.
                if ($this->isOpen || $this->pageMode) {
                    if (!$session->is_user_read) {
                        $session->update(['is_user_read' => true]);
                    }
                    $this->hasUnreadMessages = false;
                } else {
                    $this->hasUnreadMessages = !$session->is_user_read;
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

    /**
     * Toggle feedback form visibility
     */
    public function toggleFeedbackForm()
    {
        $this->showFeedbackForm = !$this->showFeedbackForm;
        $this->feedbackSuccess = false;

        // Auto-fill name if logged in
        if (Auth::check() && !$this->name) {
            $this->name = Auth::user()->name;
        }
    }

    /**
     * Submit feedback
     */
    public function submitFeedback()
    {
        $this->validate([
            'feedbackType' => 'required|in:kritik,saran',
            'feedbackSubject' => 'required|min:5|max:200',
            'feedbackMessage' => 'required|min:10|max:2000',
            'feedbackImage' => 'nullable|image|max:5120' // Max 5MB
        ], [
            'feedbackType.required' => 'Tipe feedback harus dipilih',
            'feedbackSubject.required' => 'Subjek harus diisi',
            'feedbackSubject.min' => 'Subjek minimal 5 karakter',
            'feedbackMessage.required' => 'Pesan harus diisi',
            'feedbackMessage.min' => 'Pesan minimal 10 karakter',
            'feedbackImage.image' => 'File harus berupa gambar',
            'feedbackImage.max' => 'Ukuran gambar maksimal 5MB'
        ]);

        // Generate ticket-style anonymous identifier
        $tempId = abs(crc32(uniqid())) % 9999 + 1;
        $anonymousName = 'Ticket #' . str_pad($tempId, 4, '0', STR_PAD_LEFT);

        // Handle image upload
        $imagePath = null;
        if ($this->feedbackImage) {
            $imagePath = $this->feedbackImage->store('feedback-images', 'public');
        }

        Feedback::create([
            'name' => $anonymousName,
            'user_id' => Auth::id(),
            'type' => $this->feedbackType,
            'subject' => $this->feedbackSubject,
            'message' => $this->feedbackMessage,
            'image' => $imagePath,
            'status' => 'new'
        ]);

        // Reset form and show success
        $this->feedbackSubject = '';
        $this->feedbackMessage = '';
        $this->feedbackImage = null;
        $this->feedbackSuccess = true;

        // Auto-hide success message and form after 3 seconds
        $this->dispatch('feedback-submitted');
    }

    // Polling fallback
    public function render()
    {
        return view('livewire.chat-widget');
    }
}
