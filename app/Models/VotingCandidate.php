<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingCandidate extends Model
{
    use HasFactory;

    protected $table = 'voting_candidates';

    protected $fillable = [
        'voting_id',
        'user_id',
        'name',
        'custom_role',
        'vision_mission',
        'photo',
    ];

    public function voting()
    {
        return $this->belongsTo(Voting::class, 'voting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes()
    {
        return $this->hasMany(VotingVote::class, 'candidate_id');
    }

    public function votesCount()
    {
        return $this->votes()->count();
    }

    public function percentageOfTotal()
    {
        $total = $this->voting ? $this->voting->totalVotesCount() : 0;
        if ($total === 0) {
            return 0;
        }
        return round(($this->votesCount() / $total) * 100, 1);
    }
}
