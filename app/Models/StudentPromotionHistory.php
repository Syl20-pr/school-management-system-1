<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromotionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_year_id',
        'from_class_id',
        'to_year_id',
        'to_class_id',
        'action',
        'reason',
        'decision_by',
        'decision_date',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason' // Ajouter ce champ
    ];

    protected $casts = [
        'decision_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function student() { return $this->belongsTo(User::class,'student_id'); }
    public function fromYear(){ return $this->belongsTo(StudentYear::class,'from_year_id'); }
    public function toYear(){ return $this->belongsTo(StudentYear::class,'to_year_id'); }
    public function fromClass(){ return $this->belongsTo(StudentClass::class,'from_class_id'); }
    public function toClass(){ return $this->belongsTo(StudentClass::class,'to_class_id'); }
    public function decisionMaker(){ return $this->belongsTo(User::class,'decision_by'); }
    //for bulk canceling
    // Ajoutez cette relation pour récupérer l'utilisateur qui a annulé
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
