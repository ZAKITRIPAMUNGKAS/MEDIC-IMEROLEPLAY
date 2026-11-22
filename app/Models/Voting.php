<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voting extends Model
{
    use HasFactory;

    protected $table = 'votings';

    protected $fillable = [
        'title',
        'target_position',
        'description',
        'hospital',
        'status',
        'audience',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates()
    {
        return $this->hasMany(VotingCandidate::class, 'voting_id');
    }

    public function votes()
    {
        return $this->hasMany(VotingVote::class, 'voting_id');
    }

    /** Total jumlah voter (kepala), bukan bobot */
    public function totalVotesCount()
    {
        return $this->votes()->count();
    }

    /** Total bobot semua suara */
    public function totalWeightedVotes()
    {
        try {
            return (int) $this->votes()->sum('weight');
        } catch (\Throwable $e) {
            return (int) $this->votes()->count();
        }
    }

    public function hasUserVoted($userId)
    {
        try {
            return $this->votes()->where('user_id', $userId)->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getUserVoteCandidateId($userId)
    {
        try {
            $vote = $this->votes()->where('user_id', $userId)->first();
            return $vote ? $vote->candidate_id : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Apakah voting ini hanya untuk management (staff_manager ke atas) */
    public function isManagementOnly()
    {
        return isset($this->audience) && $this->audience === 'management';
    }

    /** Cek apakah user boleh melihat/ikut voting ini */
    public function canUserAccess($user)
    {
        if (!$this->isManagementOnly()) {
            return true;
        }
        // management = level >= 5 atau admin
        $level = $user->role?->level ?? 0;
        return $user->isAdmin() || $level >= 5;
    }

    public function isRoxwood()
    {
        return $this->hospital === 'roxwood';
    }

    public function isAlta()
    {
        return $this->hospital === 'alta';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }
}
