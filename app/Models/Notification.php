<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'recipient',
        'message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function send(Order $order, string $type, string $message, ?string $recipient = null): self
    {
        $target = $recipient ?? ($order->customer_phone ?: $order->customer_name);
        
        return self::create([
            'order_id' => $order->id,
            'type' => $type,
            'recipient' => $target,
            'message' => $message,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}

