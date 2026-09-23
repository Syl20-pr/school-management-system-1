<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle : GlobalSetting (Configuration globale de la plateforme SaaS)
 * 
 * Niveau 1 : Architecture Plateforme Multi-Tenant.
 * Stocke les clés/valeurs globales du système applicatif.
 */
class GlobalSetting extends Model
{
    use HasFactory;

    protected $table = 'global_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];
}
