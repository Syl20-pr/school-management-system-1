<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : PlatformAdmin (Super-administrateur de la plateforme SaaS)
 * 
 * Niveau 1 : Architecture Plateforme Multi-Tenant.
 * Utilisateurs disposant d'un droit de regard et d'administration transversal.
 */
class PlatformAdmin extends Model
{
    use HasFactory;

    protected $table = 'platform_admins';

    protected $fillable = [
        'user_id',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Compte utilisateur central associé à ce rôle super-administrateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
