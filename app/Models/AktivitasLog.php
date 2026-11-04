<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'aksi',
        'deskripsi',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $deskripsi, ?User $user = null): self
    {
        $currentUser = $user ?? auth()->user();
        return self::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser ? ($currentUser->name . ' (' . ucfirst($currentUser->role) . ')') : 'Sistem / Pelanggan',
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'ip_address' => request()->ip(),
        ]);
    }
}
