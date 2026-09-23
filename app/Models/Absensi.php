<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $table = 'absensi';
    
    protected $fillable = [
        'player_id',
        'player_name',
        'clock_in',
        'clock_out',
        'time_on_duty',
        'source',
        'notes'
    ];
    
    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'time_on_duty' => 'datetime:H:i:s'
    ];
    
    /**
     * Scope untuk mencari absensi yang belum clock out
     */
    public function scopeActive($query)
    {
        return $query->whereNull('clock_out');
    }
    
    /**
     * Scope untuk mencari absensi berdasarkan player_id
     */
    public function scopeByPlayer($query, $playerId)
    {
        $raw = (string)$playerId;
        $stripped = preg_replace('/^(char\d+:|citizen:|cid:|id:|license:|steam:|discord:)/i', '', strtolower(trim($raw)));
        $stripped = trim(str_replace(['#', ' ', '-', '.'], '', $stripped));

        $variants = array_filter(array_unique([
            $raw,
            strtolower($raw),
            strtoupper($raw),
            $stripped,
            strtoupper($stripped),
            'char1:' . $stripped,
            'citizen:' . $stripped,
        ]));

        return $query->whereIn('player_id', $variants);
    }
    
    /**
     * Scope untuk absensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('clock_in', today());
    }
    
    /**
     * Scope untuk absensi minggu ini
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('clock_in', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    
    /**
     * Scope untuk absensi bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('clock_in', [now()->startOfMonth(), now()->endOfMonth()]);
    }
    
    /**
     * Scope untuk absensi tahun ini
     */
    public function scopeThisYear($query)
    {
        return $query->whereBetween('clock_in', [now()->startOfYear(), now()->endOfYear()]);
    }
    
    /**
     * Scope untuk absensi yang sudah selesai (clock out)
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('clock_out');
    }
    
    /**
     * Scope untuk absensi dalam rentang tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('clock_in', [$startDate, $endDate]);
    }
    
    /**
     * Cek apakah player sudah clock in tapi belum clock out
     */
    public static function isPlayerActive($playerId)
    {
        return self::byPlayer($playerId)->active()->exists();
    }
    
    /**
     * Hitung durasi kerja dalam detik
     */
    public function getDurationInSeconds()
    {
        if (!$this->clock_out) {
            return null;
        }
        
        return $this->clock_in->diffInSeconds($this->clock_out);
    }
    
    /**
     * Get formatted duration (HH:MM:SS)
     */
    public function getFormattedDuration()
    {
        $seconds = $this->getDurationInSeconds();
        return \App\Helpers\TimeHelper::formatDuration($seconds);
    }
    
    /**
     * Get duration in hours (decimal)
     */
    public function getDurationInHours()
    {
        $seconds = $this->getDurationInSeconds();
        return \App\Helpers\TimeHelper::secondsToHours($seconds);
    }
    
    /**
     * Relationship to User model (if exists)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'player_id', 'citizen_id');
    }

    /**
     * Dynamic user resolution supporting both citizen_id and staff_id
     */
    public function getUserAttribute()
    {
        if ($this->relationLoaded('user') && $this->getRelation('user')) {
            return $this->getRelation('user');
        }

        $playerId = (string)$this->player_id;
        $stripped = preg_replace('/^(char\d+:|citizen:|cid:|id:|license:|steam:|discord:)/i', '', strtolower(trim($playerId)));
        $stripped = trim(str_replace(['#', ' ', '-', '.'], '', $stripped));

        $user = User::where(function($q) use ($playerId, $stripped) {
            $q->where('citizen_id', $playerId)
              ->orWhere('staff_id', $playerId)
              ->orWhereRaw('LOWER(TRIM(citizen_id)) = ?', [$stripped])
              ->orWhereRaw('LOWER(TRIM(staff_id)) = ?', [$stripped]);
        })->first();

        $this->setRelation('user', $user);
        return $user;
    }
}
