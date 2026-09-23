<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : SchoolDomain (Domaines et Routage SaaS)
 * 
 * Niveau 1 : Architecture Plateforme Multi-Tenant.
 * Gère le routage par nom de domaine ou sous-domaine pour identifier le tenant.
 */
class SchoolDomain extends Model
{
    use HasFactory;

    protected $table = 'school_domains';

    protected $fillable = [
        'school_id',
        'domain',
        'is_primary',
        'is_verified',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Établissement propriétaire de ce nom de domaine.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
