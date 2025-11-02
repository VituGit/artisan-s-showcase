<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtelierLpConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'atelier_id',
        'schema_version',
        'data',
        'draft',
        'status',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'schema_version' => 'integer',
        'data' => 'array',
        'draft' => 'array',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(Atelier::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Methods

    public function publish(): bool
    {
        $this->status = 'published';
        $this->published_at = now();
        $this->data = $this->draft;

        return $this->save();
    }

    public function saveDraft(array $draftData): bool
    {
        $this->draft = $draftData;
        $this->status = 'draft';

        return $this->save();
    }
}
