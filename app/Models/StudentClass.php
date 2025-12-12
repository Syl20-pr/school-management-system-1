<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level', 'level_name'];

    /**
     * Relation avec les étudiants assignés à cette classe
     */
    public function assignStudents()
    {
        return $this->hasMany(AssignStudent::class, 'class_id');
    }

    /**
     * Relation avec les années via les étudiants assignés
     */
    public function years()
    {
        return $this->belongsToMany(StudentYear::class, 'assign_students', 'class_id', 'year_id')
                    ->distinct();
    }
    
    public function teacher_class_assignments()
    {
        return $this->hasMany(AssignSubjectTeach::class);
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////MISE A JOUR A PROPOS DES NIVEAUX DE CLASSE////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Méthode pour obtenir toutes les classes du même niveau
    public function sameLevelClasses()
    {
        if ($this->level === null) {
            return collect();
        }
        
        return StudentClass::where('level', $this->level)
            ->where('id', '!=', $this->id)
            ->get();
    }

    // Méthode pour obtenir les classes du niveau suivant
    public function getNaturalNextClasses()
    {
        if ($this->level === null) {
            return collect();
        }
        
        $nextLevel = $this->level - 1; // 6ème -> 5ème (6 -> 5)
        
        // Terminale est le niveau 0, donc pas de niveau suivant
        if ($this->level === 0) {
            return collect();
        }
        
        return StudentClass::where('level', $nextLevel)->get();
    }

    // Méthode pour obtenir le nom du niveau suivant
    public function getNextLevelName()
    {
        $levelNames = [
            6 => '5ème',
            5 => '4ème',
            4 => '3ème',
            3 => '2nd',
            2 => '1ère',
            1 => 'Terminale',
            0 => null // Terminale n'a pas de niveau suivant
        ];
        
        return $levelNames[$this->level] ?? null;
    }

    // Scope pour les requêtes courantes
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeNextLevel($query, $currentLevel)
    {
        if ($currentLevel === null || $currentLevel === 0) {
            return $query->whereNull('level');
        }
        
        return $query->where('level', $currentLevel - 1);
    }
    ////////////////////////////////FIN DE LA MISE A JOUR/////////////////////////////////////////////
    
}
