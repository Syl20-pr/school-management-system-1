<?php
// app/Models/TimeTableSlot.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_id', 
        'day_of_week', 
        'period_id', 
        'subject_id', 
        'teacher_id', 
        'classroom_id'
    ];

    public function timetable()
    {
        return $this->belongsTo(TimeTable::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function subject()
    {
        return $this->belongsTo(SchoolSubject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}