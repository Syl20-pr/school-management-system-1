<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : EmployeeProfile (Profil de l'employé / enseignant)
 * 
 * Niveau 3 : Découplage des Identités (Refactorisation de l'anti-pattern God Class User).
 * Isole les attributs RH, salariaux, contractuels et professionnels.
 */
class EmployeeProfile extends Model
{
    use HasFactory;

    protected $table = 'employee_profiles';

    protected $fillable = [
        'user_id',
        'school_id',
        'id_no',
        'designation_id',
        'join_date',
        'salary',
        'qualification',
        'gender',
        'dob',
        'lob',
        'religion',
        'address',
        'mobile',
        'image',
        'emergency_contact',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'dob' => 'date',
        'salary' => 'double',
        'status' => 'integer',
    ];

    /**
     * Compte utilisateur central (identifiants / mot de passe).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Établissement scolaire employeur.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Poste / Titre occupé par l'employé.
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
