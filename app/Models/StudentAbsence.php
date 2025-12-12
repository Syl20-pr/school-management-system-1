<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAbsence extends Model
{
    use HasFactory;
    
    protected $table = 'student_absences'; // ✅ Nom explicite de la table
    
    protected $fillable = [
        'student_id',
        'id_no',
        'year_id',
        'class_id',
        'type_tri_sem',
        'absences',
    ];

    // ✅ Relations
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }
}