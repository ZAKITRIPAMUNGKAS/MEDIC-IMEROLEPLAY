<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingVote extends Model
{
    use HasFactory;

    protected $table = 'voting_votes';

    protected $fillable = [
        'voting_id',
        'candidate_id',
        'user_id',
    ];

    public function voting()
    {
        return $this->belongsTo(Voting::class, 'voting_id');
    }

    public function candidate()
    {
        return $this->belongsTo(VotingCandidate::class, 'candidate_id');
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
