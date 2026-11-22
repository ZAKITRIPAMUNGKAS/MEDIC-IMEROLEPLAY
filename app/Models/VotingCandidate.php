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

    /** Jumlah voter (kepala) untuk kandidat ini */
    public function votesCount()
    {
        return $this->votes()->count();
    }

    /** Total bobot suara untuk kandidat ini */
    public function weightedScore()
    {
        return $this->votes()->sum('weight');
    }

    /** Persentase berbasis bobot (bukan jumlah kepala) */
    public function percentageOfTotal()
    {
        $totalWeight = $this->voting ? $this->voting->totalWeightedVotes() : 0;
        if ($totalWeight === 0) {
            return 0;
        }
        return round(($this->weightedScore() / $totalWeight) * 100, 1);
    }
}
