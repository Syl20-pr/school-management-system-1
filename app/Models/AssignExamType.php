<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignExamType extends Model
{
    
    public function term_type(){
    return $this->belongsTo(TermType::class,'term_type_id');
   }

    public function exam_type(){
    return $this->belongsTo(ExamType::class,'exam_type_id');
   }

    

   //////////////////////////////// ME ADDING /////////////////////

    protected $fillable = [
        'term_type_id',
        'exam_type_id',
        'comment',
    ];
}
