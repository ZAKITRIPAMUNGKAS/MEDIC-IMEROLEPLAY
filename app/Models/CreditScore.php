<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
     * Accessor agar $creditScore->score dan $creditScore->balance kompatibel.
     */
    public function getScoreAttribute(): int
    {
        return (int) ($this->balance ?? 100);
    }

    /**
     * Ambil atau buat record credit score untuk user tertentu.
     * Saldo awal default 100. Otomatis buat tabel jika belum ada di database.
     */
    public static function getOrCreate(int $userId): self
    {
        try {
            return self::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 100]
            );
        } catch (\Throwable $e) {
            try {
                if (!Schema::hasTable('credit_scores')) {
                    Schema::create('credit_scores', function ($table) {
                        $table->id();
                        $table->unsignedBigInteger('user_id')->unique();
                        $table->integer('balance')->default(100);
                        $table->timestamps();
                    });
                }

                if (!Schema::hasTable('credit_score_logs')) {
                    Schema::create('credit_score_logs', function ($table) {
                        $table->id();
                        $table->unsignedBigInteger('user_id')->index();
                        $table->unsignedBigInteger('issued_by')->nullable()->index();
                        $table->integer('amount');
                        $table->integer('balance_after');
                        $table->text('reason')->nullable();
                        $table->string('type', 20)->default('add');
                        $table->timestamps();
                        $table->index(['user_id', 'created_at']);
                    });
                }

                return self::firstOrCreate(
                    ['user_id' => $userId],
                    ['balance' => 100]
                );
            } catch (\Throwable $ex) {
                Log::error('CreditScore::getOrCreate fallback error: ' . $ex->getMessage());
                $record = new self();
                $record->user_id = $userId;
                $record->balance = 100;
                return $record;
            }
        }
    }

    /**
     * Tambah poin. Return saldo baru.
     */
    public function add(int $amount, ?int $issuedBy = null, ?string $reason = ''): int
    {
        $current = (int) ($this->balance ?? 100);
        $this->balance = min(100, max(0, $current + $amount));
        $this->save();

        try {
            CreditScoreLog::create([
                'user_id'       => $this->user_id,
                'issued_by'     => $issuedBy ?? $this->user_id,
                'amount'        => $amount,
                'balance_after' => $this->balance,
                'reason'        => mb_substr($reason ?: 'Penambahan poin Credit Score', 0, 500),
                'type'          => 'add',
            ]);
        } catch (\Throwable $e) {
            Log::error('CreditScoreLog create error in add(): ' . $e->getMessage());
        }

        return $this->balance;
    }

    /**
     * Kurangi poin. Return saldo baru.
     */
    public function deduct(int $amount, ?int $issuedBy = null, ?string $reason = ''): int
    {
        $current = (int) ($this->balance ?? 100);
        $this->balance = min(100, max(0, $current - abs($amount)));
        $this->save();

        try {
            CreditScoreLog::create([
                'user_id'       => $this->user_id,
                'issued_by'     => $issuedBy ?? $this->user_id,
                'amount'        => -abs($amount),
                'balance_after' => $this->balance,
                'reason'        => mb_substr($reason ?: 'Pengurangan poin Credit Score', 0, 500),
                'type'          => 'deduct',
            ]);
        } catch (\Throwable $e) {
            Log::error('CreditScoreLog create error in deduct(): ' . $e->getMessage());
        }

        return $this->balance;
    }

    /**
     * Set / Edit saldo langsung. Return saldo baru.
     */
    public function setScore(int $newScore, ?int $issuedBy = null, ?string $reason = ''): int
    {
        $oldBalance = (int) ($this->balance ?? 100);
        $clampedScore = min(100, max(0, $newScore));
        $diff = $clampedScore - $oldBalance;
        $this->balance = $clampedScore;
        $this->save();

        try {
            CreditScoreLog::create([
                'user_id'       => $this->user_id,
                'issued_by'     => $issuedBy ?? $this->user_id,
                'amount'        => $diff,
                'balance_after' => $this->balance,
                'reason'        => mb_substr($reason ?: 'Penyesuaian / Edit langsung skor oleh Comdis', 0, 500),
                'type'          => $diff >= 0 ? 'add' : 'deduct',
            ]);
        } catch (\Throwable $e) {
            Log::error('CreditScoreLog create error in setScore(): ' . $e->getMessage());
        }

        return $this->balance;
    }
}
