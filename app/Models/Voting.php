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

    public function totalVotesCount()
    {
        return $this->votes()->count();
    }

    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVoteCandidateId($userId)
    {
        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote ? $vote->candidate_id : null;
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
