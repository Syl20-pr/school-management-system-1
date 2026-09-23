<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // AJOUTEZ CETTE LIGNE
use App\Models\Designation;
use App\Models\AssignDesignation;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles; // AJOUTEZ CETTE LIGNE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'designation_id', 
        'usertype',
        'statusclass',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
            'statusclass' => 'string',
        ];
    }

    // ... le reste de vos relations reste inchangé
    public function designation(){
        return $this->belongsTo(Designation::class, 'designation_id','id');
    }

    public function assignedDesignations()
    {
        return $this->hasMany(AssignDesignation::class, 'teacher_id');
    }

    public function assignedStudents(): HasMany
    {
        return $this->hasMany(AssignStudent::class, 'student_id');
    }

    public function assignedSubjects(): HasMany
    {
        return $this->hasMany(AssignSubjectTeach::class, 'teacher_id');
    }

    public function unavailabilities()
    {
        return $this->hasMany(TeacherUnavailability::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(SchoolSubject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function studentMarks(): HasMany
    {
        return $this->hasMany(StudentMarks::class, 'student_id', 'id');
    }

    /**
     * Établissements scolaires auxquels cet utilisateur est rattaché (Niveau 2 : Multi-tenant RBAC).
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_user')
            ->withPivot('role', 'status', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Profils d'élève associés à ce compte utilisateur (Niveau 3 : Découplage des identités).
     */
    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'user_id');
    }

    /**
     * Profils d'employé associés à ce compte utilisateur (Niveau 3 : Découplage des identités).
     */
    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class, 'user_id');
    }

    /**
     * Droits d'administration globale de la plateforme SaaS (Niveau 1 : Plateforme centrale).
     */
    public function platformAdmin()
    {
        return $this->hasOne(PlatformAdmin::class, 'user_id');
    }
}