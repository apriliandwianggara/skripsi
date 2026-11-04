<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'previous_status',
        'new_status',
        'changed_by',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function log(Order $order, string $newStatus, ?string $notes = null): self
    {
        $user = auth()->user();
        $actor = $user ? ($user->name . ' (' . ucfirst($user->role) . ')') : 'Pelanggan / Sistem';

        return self::create([
            'order_id' => $order->id,
            'previous_status' => $order->status,
            'new_status' => $newStatus,
            'changed_by' => $actor,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}

