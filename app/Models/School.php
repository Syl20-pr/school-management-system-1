<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modèle : School (Établissement scolaire / Tenant SaaS)
 * 
 * Niveau 1 : Architecture Plateforme Multi-Tenant.
 * Représente un établissement scolaire autonome dans l'architecture SaaS partagée.
 */
class School extends Model
{
    use HasFactory;

    protected $table = 'schools';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'status',
        'timezone',
    ];

    /**
     * Domaines et sous-domaines rattachés à cette école.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(SchoolDomain::class, 'school_id');
    }

    /**
     * Souscriptions et abonnements SaaS de l'école.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class, 'school_id');
    }

    /**
     * Paramètres personnalisés de l'établissement.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class, 'school_id');
    }

    /**
     * Utilisateurs membres de cet établissement scolaire via le pivot school_user.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_user')
            ->withPivot('role', 'status', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Profils d'élèves inscrits dans cet établissement.
     */
    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'school_id');
    }

    /**
     * Profils d'employés et d'enseignants affectés à cet établissement.
     */
    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class, 'school_id');
    }
}
