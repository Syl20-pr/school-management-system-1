<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_current',
        'created_at',
        'updated_at'
        // ... autres colonnes
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Scope pour récupérer l'année en cours
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Définir cette année comme année en cours
     * et désactiver les autres
     */
    public function setAsCurrent()
    {
        // Désactiver toutes les autres années
        self::where('id', '!=', $this->id)->update(['is_current' => false]);
        
        // Activer cette année
        $this->update(['is_current' => true]);
    }

    public function teacher_class_assignments()
    {
        return $this->hasMany(AssignSubjectTeach::class);
    }
}
