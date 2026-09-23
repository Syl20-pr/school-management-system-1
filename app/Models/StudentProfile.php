<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : StudentProfile (Profil de l'élève)
 * 
 * Niveau 3 : Découplage des Identités (Refactorisation de l'anti-pattern God Class User).
 * Isole les attributs civils, pédagogiques et familiaux de l'élève.
 */
class StudentProfile extends Model
{
    use HasFactory;

    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'school_id',
        'id_no',
        'fname',
        'mname',
        'f_no',
        'dob',
        'lob',
        'gender',
        'religion',
        'address',
        'mobile',
        'image',
        'blood_group',
        'emergency_contact',
        'previous_school',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Compte utilisateur central (identifiants / mot de passe).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Établissement scolaire dans lequel l'élève est inscrit.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
