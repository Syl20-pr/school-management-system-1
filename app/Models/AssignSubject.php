<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignSubject extends Model
{
    use HasFactory;

     public function student_class(){
    return $this->belongsTo(StudentClass::class,'class_id');
    }

    public function school_subject(){
    return $this->belongsTo(SchoolSubject::class,'subject_id', 'id');
    }

   public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function assign_teacher()
    {
        return $this->hasOne(AssignSubjectTeach::class, 'subject_id', 'subject_id');
        /*->whereColumn('assign_subject_teaches.class_id', 'assign_subjects.class_id')
        ->whereColumn('assign_subject_teaches.year_id', 'assign_subjects.year_id');*/
    }
   

   //////////////////////////////// ME ADDING /////////////////////

    protected $fillable = [
        'class_id',
        'year_id',
        'subject_id',
        'full_mark',
        'pass_mark',
        'subjective_mark',
    ];
}
