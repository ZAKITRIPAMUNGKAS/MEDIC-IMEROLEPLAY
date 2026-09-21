<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditScore extends Model
{
    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(CreditScoreLog::class, 'user_id', 'user_id')
                    ->orderByDesc('created_at');
    }

    /**
     * Ambil atau buat record credit score untuk user tertentu.
     * Saldo awal default 100.
     */
    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 100]
        );
    }

    /**
     * Tambah poin. Return saldo baru.
     */
    public function add(int $amount, int $issuedBy, string $reason): int
    {
        $this->balance += $amount;
        $this->save();

        CreditScoreLog::create([
            'user_id'       => $this->user_id,
            'issued_by'     => $issuedBy,
            'amount'        => $amount,
            'balance_after' => $this->balance,
            'reason'        => $reason,
            'type'          => 'add',
        ]);

        return $this->balance;
    }

    /**
     * Kurangi poin. Return saldo baru.
     */
    public function deduct(int $amount, int $issuedBy, string $reason): int
    {
        $this->balance -= abs($amount);
        $this->save();

        CreditScoreLog::create([
            'user_id'       => $this->user_id,
            'issued_by'     => $issuedBy,
            'amount'        => -abs($amount),
            'balance_after' => $this->balance,
            'reason'        => $reason,
            'type'          => 'deduct',
        ]);

        return $this->balance;
    }
}
