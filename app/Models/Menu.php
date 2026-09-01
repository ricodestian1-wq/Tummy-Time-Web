<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'is_available', 'stock', 'sort_order', 'image_url',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * stock null = tidak dibatasi (selalu tersedia selama is_available true)
     */
    public function isInStock(): bool
    {
        return $this->is_available && ($this->stock === null || $this->stock > 0);
    }

    public function isLowStock(): bool
    {
        return $this->stock !== null && $this->stock > 0 && $this->stock <= 5;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
