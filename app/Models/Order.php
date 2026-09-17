<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'paid',
        'failed',
        'shipped',
    ];

    protected $fillable = [
        'order_number',
        'artwork_id',
        'paypal_order_id',
        'customer_name',
        'customer_email',
        'shipping_address',
        'amount',
        'status',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'amount' => 'decimal:2',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
