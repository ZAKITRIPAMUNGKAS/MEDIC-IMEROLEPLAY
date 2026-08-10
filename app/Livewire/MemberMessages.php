<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\MemberMessage;
use App\Models\ChatStreak;
use App\Models\UserRecentSticker;
use Illuminate\Support\Facades\Auth;

class MemberMessages extends Component
{
    public $selectedUserId;
    public $searchQuery = '';
    public $messageText = '';
    
    // Streak data
    public $streakCount = 0;
    public $streakAlmostBroken = false;

    protected $queryString = ['selectedUserId' => ['except' => null, 'as' => 'user']];

    public function mount()
    {
        if ($this->selectedUserId) {
            $this->selectUser($this->selectedUserId);
        }
    }

    /**
     * Select a user to chat with.
     */
    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;
        $this->messageText = '';

        // Mark messages as read
        MemberMessage::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $this->updateStreakData();
    }

    /**
     * Update/Calculate streak info for the selected conversation.
     */
    public function updateStreakData()
    {
        if (!$this->selectedUserId) {
            $this->streakCount = 0;
            $this->streakAlmostBroken = false;
            return;
        }

        $currentUserId = Auth::id();
        $selectedUserId = $this->selectedUserId;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $streak = ChatStreak::where(function ($q) use ($currentUserId, $selectedUserId) {
            $q->where('user_one_id', min($currentUserId, $selectedUserId))
              ->where('user_two_id', max($currentUserId, $selectedUserId));
        })->first();

        if ($streak) {
            // Check if streak is broken (last interaction was before yesterday)
            if ($streak->last_interaction_date && $streak->last_interaction_date->toDateString() < $yesterday) {
                $streak->update([
                    'streak_count' => 0,
                    'last_interaction_date' => null
                ]);
            }

            $this->streakCount = $streak->streak_count;

            // Streak is almost broken if the last interaction was yesterday, and today they haven't completed the interaction yet
            if ($streak->streak_count > 0 && $streak->last_interaction_date && $streak->last_interaction_date->toDateString() === $yesterday) {
                // Check if they already interacted today to complete the streak
                $senderSentToday = MemberMessage::where('sender_id', $currentUserId)
                    ->where('receiver_id', $selectedUserId)
                    ->whereDate('created_at', $today)
                    ->exists();
                $receiverSentToday = MemberMessage::where('sender_id', $selectedUserId)
                    ->where('receiver_id', $currentUserId)
                    ->whereDate('created_at', $today)
                    ->exists();

                $this->streakAlmostBroken = !($senderSentToday && $receiverSentToday);
            } else {
                $this->streakAlmostBroken = false;
            }
        } else {
            $this->streakCount = 0;
            $this->streakAlmostBroken = false;
        }
    }

    /**
     * Helper to process streak increment upon sending a message.
     */
    private function processStreakOnMessage()
    {
        $currentUserId = Auth::id();
        $selectedUserId = $this->selectedUserId;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $streak = ChatStreak::where(function ($q) use ($currentUserId, $selectedUserId) {
            $q->where('user_one_id', min($currentUserId, $selectedUserId))
              ->where('user_two_id', max($currentUserId, $selectedUserId));
        })->first();

        if (!$streak) {
            $streak = ChatStreak::create([
                'user_one_id' => min($currentUserId, $selectedUserId),
                'user_two_id' => max($currentUserId, $selectedUserId),
                'streak_count' => 0,
                'last_interaction_date' => null,
                'status' => 'active'
            ]);
        }

        // Check if the other user has sent any message today or yesterday
        $otherUserSent = MemberMessage::where('sender_id', $selectedUserId)
            ->where('receiver_id', $currentUserId)
            ->whereDate('created_at', '>=', $yesterday)
            ->exists();

        if ($otherUserSent) {
            if (!$streak->last_interaction_date) {
                $streak->update([
                    'streak_count' => 1,
                    'last_interaction_date' => $today
                ]);
            } elseif ($streak->last_interaction_date->toDateString() === $yesterday) {
                $newCount = $streak->streak_count + 1;
                $streak->update([
                    'streak_count' => $newCount,
                    'last_interaction_date' => $today
                ]);

                // Dispatch milestone event if count matches milestone (7, 30, 50, 100, 365)
                if (in_array($newCount, [7, 30, 50, 100, 365])) {
                    $this->dispatch('streak-milestone', count: $newCount);
                }
            }
        } else {
            // If they haven't sent a message today, but the last interaction was yesterday,
            // we don't increment yet, but we ensure the streak isn't reset.
            // If the last interaction was before yesterday, reset to 0.
            if ($streak->last_interaction_date && $streak->last_interaction_date->toDateString() < $yesterday) {
                $streak->update([
                    'streak_count' => 0,
                    'last_interaction_date' => null
                ]);
            }
        }

        $this->streakCount = $streak->streak_count;
        $this->updateStreakData();
    }

    /**
     * Send a private message.
     */
    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|min:1|max:5000',
        ]);

        if (!$this->selectedUserId) {
            return;
        }

        MemberMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUserId,
            'message' => $this->messageText,
            'message_type' => 'text',
            'is_read' => false
        ]);

        $this->processStreakOnMessage();

        $this->messageText = '';
        
        $this->dispatch('message-sent');
    }

    /**
     * Send a sticker.
     */
    public function sendSticker($source, $stickerId, $stickerUrl)
    {
        if (!$this->selectedUserId) {
            return;
        }

        MemberMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUserId,
            'message' => '[Sticker]',
            'message_type' => 'sticker',
            'sticker_source' => $source,
            'sticker_id' => $stickerId,
            'sticker_url' => $stickerUrl,
            'is_read' => false
        ]);

        // Add to user's recently used stickers
        UserRecentSticker::updateOrInsert(
            [
                'user_id' => Auth::id(),
                'source' => $source,
                'sticker_id' => $stickerId
            ],
            [
                'sticker_url' => $stickerUrl,
                'used_at' => now()
            ]
        );

        // Limit recents to latest 30 items
        $recentsCount = UserRecentSticker::where('user_id', Auth::id())->count();
        if ($recentsCount > 30) {
            $oldest = UserRecentSticker::where('user_id', Auth::id())
                ->orderBy('used_at', 'asc')
                ->first();
            if ($oldest) {
                $oldest->delete();
            }
        }

        $this->processStreakOnMessage();

        $this->dispatch('message-sent');
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        $currentUserId = Auth::id();

        // Only fetch users who have sent or received messages with current user
        $chatPartnerIds = MemberMessage::where('sender_id', $currentUserId)
            ->pluck('receiver_id')
            ->concat(
                MemberMessage::where('receiver_id', $currentUserId)->pluck('sender_id')
            )
            ->unique()
            ->filter()
            ->toArray();

        // If a user is explicitly selected (e.g. from Member Directory), ensure they are included in the list
        if ($this->selectedUserId && !in_array((int)$this->selectedUserId, $chatPartnerIds)) {
            $chatPartnerIds[] = (int)$this->selectedUserId;
        }

        $usersQuery = User::whereIn('id', $chatPartnerIds)
            ->where('id', '!=', $currentUserId)
            ->with('role');

        if (!empty($this->searchQuery)) {
            $usersQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhereHas('role', function ($qr) {
                      $qr->where('display_name', 'like', '%' . $this->searchQuery . '%');
                  });
            });
        }

        $allUsers = $usersQuery->get();

        foreach ($allUsers as $user) {
            // Count unread messages from this user to me
            $user->unread_count = MemberMessage::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();

            // Find the latest message between this user and me
            $latestMessage = MemberMessage::where(function ($q) use ($user, $currentUserId) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user, $currentUserId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $currentUserId);
            })
            ->latest()
            ->first();

            $user->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
            $user->latest_message_text = $latestMessage ? ($latestMessage->message_type === 'sticker' ? '🎟️ Stiker' : $latestMessage->message) : '';
            
            // Get streak count for user list preview
            $userStreak = ChatStreak::where(function ($q) use ($user, $currentUserId) {
                $q->where('user_one_id', min($currentUserId, $user->id))
                  ->where('user_two_id', max($currentUserId, $user->id));
            })->first();
            $user->streak_count = $userStreak ? $userStreak->streak_count : 0;
        }

        // Sort: active chats / unread chats first, then by time desc, then by name
        $members = $allUsers->sortBy(function ($user) {
            $time = $user->latest_message_time ? $user->latest_message_time->timestamp : 0;
            return [
                -$user->unread_count,
                -$time,
                $user->name
            ];
        })->values();

        // 2. Load active conversation messages
        $chatMessages = [];
        $activeUser = null;

        if ($this->selectedUserId) {
            $activeUser = User::with('role')->find($this->selectedUserId);
            if ($activeUser) {
                // Ensure messages from active user are marked read
                MemberMessage::where('sender_id', $this->selectedUserId)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now()
                    ]);

                $chatMessages = MemberMessage::where(function ($q) use ($currentUserId) {
                    $q->where('sender_id', $currentUserId)->where('receiver_id', $this->selectedUserId);
                })->orWhere(function ($q) use ($currentUserId) {
                    $q->where('sender_id', $this->selectedUserId)->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
            }
        }

        return view('livewire.member-messages', compact('members', 'chatMessages', 'activeUser'));
    }
}
