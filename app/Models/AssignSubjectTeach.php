<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignSubjectTeach extends Model
{
    protected $fillable = ['year_id', 'class_id', 'teacher_id', 'subject_id', 'designation_id', 'total_hours', 'total_sessions', 'comments'];

    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }
 
    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
