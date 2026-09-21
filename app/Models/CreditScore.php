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
        } catch (\Illuminate\Database\QueryException $e) {
            if (!\Illuminate\Support\Facades\Schema::hasTable('credit_scores')) {
                \Illuminate\Support\Facades\Schema::create('credit_scores', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                    $table->integer('balance')->default(100);
                    $table->timestamps();
                    $table->unique('user_id');
                });

                if (!\Illuminate\Support\Facades\Schema::hasTable('credit_score_logs')) {
                    \Illuminate\Support\Facades\Schema::create('credit_score_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->id();
                        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                        $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
                        $table->integer('amount');
                        $table->integer('balance_after');
                        $table->string('reason');
                        $table->enum('type', ['add', 'deduct'])->default('add');
                        $table->timestamps();
                        $table->index(['user_id', 'created_at']);
                    });
                }

                return self::firstOrCreate(
                    ['user_id' => $userId],
                    ['balance' => 100]
                );
            }
            throw $e;
        }
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
