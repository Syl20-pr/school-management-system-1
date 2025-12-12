<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignDesignation extends Model
{
    public function designation(){
    return $this->belongsTo(Designation::class,'designation_id');
   }

    public function teacher(){
    return $this->belongsTo(User::class,'teacher_id');
   }

    public function teacher_class_assignments()
    {
        return $this->hasMany(AssignSubjectTeach::class);
    }
   

   //////////////////////////////// ME ADDING /////////////////////

    protected $fillable = [
        'designation_id',
        'teacher_id',
        'comment',
    ];
}
