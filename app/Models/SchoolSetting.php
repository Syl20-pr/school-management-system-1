<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle : SchoolSetting (Paramètres et Préférences propres à un établissement)
 * 
 * Niveau 2 : Fondation Tenant & Configuration isolée.
 * Permet à chaque établissement d'ajuster ses paramètres sans impacter les autres.
 */
class SchoolSetting extends Model
{
    use HasFactory;

    protected $table = 'school_settings';

    protected $fillable = [
        'school_id',
        'key',
        'value',
        'type',
    ];

    /**
     * Établissement scolaire propriétaire du paramètre.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
