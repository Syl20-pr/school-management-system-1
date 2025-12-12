<?php
// app/Models/TimeTable.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'academic_year_id', 
        'class_id', 
        'term', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function academicYear()
    {
        return $this->belongsTo(StudentYear::class, 'academic_year_id');
    }

    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    } 
    /* public function class()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    } */

    public function slots()
    {
        return $this->hasMany(TimeTableSlot::class, 'timetable_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }
}