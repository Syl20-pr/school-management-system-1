<?php
// app/Models/TeacherUnavailability.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherUnavailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 
        'day_of_week', 
        'period_id', 
        'specific_date', 
        'reason'
    ];

    protected $casts = [
        'specific_date' => 'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}