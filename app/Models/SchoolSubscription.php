<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : SchoolSubscription (Abonnement et quotas SaaS par école)
 * 
 * Niveau 1 : Architecture Plateforme Multi-Tenant.
 * Encadre la licence logicielle, la période de validité et les quotas de l'établissement.
 */
class SchoolSubscription extends Model
{
    use HasFactory;

    protected $table = 'school_subscriptions';

    protected $fillable = [
        'school_id',
        'plan_name',
        'starts_at',
        'ends_at',
        'status',
        'max_students',
        'max_storage_mb',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'max_students' => 'integer',
        'max_storage_mb' => 'integer',
    ];

    /**
     * Établissement scolaire souscripteur.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
