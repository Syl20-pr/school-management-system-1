<?php
// app/Models/Period.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time', 
        'end_time', 
        'name', 
        'order', 
        'is_break'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_break' => 'boolean'
    ];

    public function slots()
    {
        return $this->hasMany(TimeTableSlot::class);
    }
}