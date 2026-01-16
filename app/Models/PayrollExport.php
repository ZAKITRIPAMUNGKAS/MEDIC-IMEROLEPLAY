<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_year',
        'export_month',
        'exported_by',
        'exported_at',
        'filters',
        'records_count',
    ];

    protected $casts = [
        'filters' => 'array',
        'exported_at' => 'datetime',
    ];

    /**
     * Get the user who exported the payroll
     */
    public function exporter()
    {
        return $this->belongsTo(User::class, 'exported_by');
    }

    /**
     * Check if export exists for current month
     */
    public static function existsForCurrentMonth()
    {
        return self::where('export_year', now()->year)
            ->where('export_month', now()->month)
            ->exists();
    }

    /**
     * Get current month's export record
     */
    public static function getCurrentMonthExport()
    {
        return self::where('export_year', now()->year)
            ->where('export_month', now()->month)
            ->with('exporter')
            ->first();
    }
}
