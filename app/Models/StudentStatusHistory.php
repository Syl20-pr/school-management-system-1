<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'student_status_history';

    protected $fillable = [
        'student_id',
        'year_id',
        'class_id',
        'old_status',
        'new_status',
        'change_reason',
        'promotion_history_id',
        'changed_by',
        'changed_at',
        'comment'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function year()
    {
        return $this->belongsTo(StudentYear::class, 'year_id');
    }

    public function class()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function promotionHistory()
    {
        return $this->belongsTo(StudentPromotionHistory::class, 'promotion_history_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes utiles
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeRepeatEvents($query)
    {
        return $query->where('change_reason', 'repeat');
    }

    public function scopePromotionEvents($query)
    {
        return $query->where('change_reason', 'promotion');
    }

    // Méthodes helper
    public function isRepeat()
    {
        return $this->change_reason === 'repeat';
    }

    public function isPromotion()
    {
        return $this->change_reason === 'promotion';
    }

    public function getDescriptionAttribute()
    {
        $reasons = [
            'promotion' => 'Promu au niveau supérieur',
            'repeat' => 'Redoublement',
            'initial' => 'Première inscription',
            'correction' => 'Correction manuelle',
            'cancellation' => 'Annulation de décision'
        ];

        return $reasons[$this->change_reason] ?? 'Changement de statut';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->new_status === 'N' 
            ? '<span class="badge-new">Nouveau</span>' 
            : '<span class="badge-pending">Doublant</span>';
    }
}