<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'customer_id', 'customer_name', 'customer_phone', 'customer_address', 'notes', 'total',
        'payment_method', 'cash_amount', 'change_amount',
        'payment_proof', 'payment_proof_at', 'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_proof_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Buat kode pesanan unik seperti TT123456
     */
    public static function generateOrderCode(): string
    {
        do {
            $code = 'TT' . random_int(100000, 999999);
        } while (self::where('order_code', $code)->exists());

        return $code;
    }
}
