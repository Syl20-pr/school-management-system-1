<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    use HasFactory;

    // If your table name is different, specify it here
    // protected $table = 'school_subjects';

    protected $fillable = [
        'name', // Assumes you have a 'name' column for the subject's name
        // Add any other subject-specific fields, if needed
    ];

    /**
     * Relationship with AssignSubject
     * A school subject can be assigned to multiple classes (AssignSubject)
     */
    public function assignSubjects()
    {
        return $this->hasMany(AssignSubject::class, 'subject_id', 'id');
    }
}
