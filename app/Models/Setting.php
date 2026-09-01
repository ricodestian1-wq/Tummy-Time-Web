<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const CREATED_AT = null;

    protected $fillable = [
        'shop_name', 'wa_number', 'is_open', 'closed_message',
        'qris_image', 'qris_merchant_name',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Selalu ada tepat 1 baris pengaturan (id = 1).
     */
    public static function current(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'shop_name' => 'Tummy Time',
            'wa_number' => '6285187408288',
            'is_open' => true,
            'closed_message' => 'Maaf, kami sedang tutup. Silakan order lagi ya!',
            'qris_merchant_name' => 'Tummy Time',
        ]);
    }
}
