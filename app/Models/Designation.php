<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = ['name'];

    public function assigned_teachers()
    {
        return $this->hasMany(AssignDesignation::class, 'designation_id');
    }
}
