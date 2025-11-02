<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'atelier_id',
        'category_id',
        'name',
        'slug',
        'short_desc',
        'description',
        'price',
        'is_personalizable',
        'personalization_hint',
        'is_available',
        'is_featured',
        'position',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_personalizable' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'position' => 'integer',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Route key
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(Atelier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function coverPhoto(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->where('is_cover', true);
    }

    // Scopes

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    // Accessors & Mutators

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }
}
