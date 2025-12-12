<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMarks extends Model
{
    use HasFactory;

    // Define the relationship to the AssignSubject model
    public function assignedSubject()
    {
        return $this->belongsTo(AssignSubject::class, 'assign_subject_id', 'subject_id');
    }

    // Define other relationships as needed
    public function student(){
        return $this->belongsTo(User::class, 'student_id');
    }

    public function year(){
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    public function student_class(){
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function exam_type(){
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    // ✅ CORRECTION : Ajoutez cette relation
    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    // Define relationship with TermType
    public function term_type(){
    return $this->belongsTo(TermType::class,'term_type_id');
   }

    public function grade()
    {
        return $this->belongsTo(MarksGrade::class, 'grade_id');
    }

    protected $fillable = [
    'student_id',
    'subject_id',
    'year_id',
    'class_id',
    'assign_subject_id',
    'term_type_id',
    'exam_type_id',
    'marks',
    'id_no',
    'inapte', // Include the "inapte" column
];

protected $casts = [
    'inapte' => 'integer', // Cast "inapte" to integer
];

}
