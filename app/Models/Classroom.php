<?php
// app/Models/Classroom.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'capacity', 
        'features', 
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'capacity' => 'integer'
    ];

    public function slots()
    {
        return $this->hasMany(TimeTableSlot::class);
    }
}