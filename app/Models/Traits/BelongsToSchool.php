<?php

namespace App\Models\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait : BelongsToSchool
 * 
 * Niveau 5 : Scoping Multi-Tenant au niveau de la couche Modèle / ORM.
 * 
 * Justification :
 * Ce trait standardise le rattachement de chaque entité métier (classes, élèves, notes,
 * paiements, emplois du temps) à un établissement scolaire (Tenant).
 * Il fournit la relation Eloquent `school()` et prépare l'application à l'activation
 * du Scope Global Eloquent pour l'isolation stricte des requêtes SQL.
 */
trait BelongsToSchool
{
    /**
     * Initialisation du trait : ajoute automatiquement school_id dans les attributs autorisés.
     */
    public function initializeBelongsToSchool(): void
    {
        if (!in_array('school_id', $this->fillable)) {
            $this->fillable[] = 'school_id';
        }
    }

    /**
     * Relation avec l'établissement scolaire propriétaire (Tenant).
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Scope local pour filtrer explicitement par établissement.
     */
    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
