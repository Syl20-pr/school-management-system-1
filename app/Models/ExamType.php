<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'term_type_id',
        'created_at',
        'updated_at'
    ];

    public function student_marks()
    {
        return $this->hasMany(StudentMarks::class, 'exam_type_id');
    }
}