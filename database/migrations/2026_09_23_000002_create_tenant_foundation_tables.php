<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ==============================================================================================
 * NIVEAU 2 : FONDATION TENANT & GESTION DES MEMBRES MULTI-ÉTABLISSEMENTS (PIVOT / RBAC)
 * ==============================================================================================
 * 
 * Cette migration implémente les fondations nécessaires à l'isolation et à la gestion multi-tenant,
 * conformément au document "Multi-Tenant SaaS Modernization Roadmap" (Section 3 & Section 5).
 * 
 * Contexte & Justification :
 * Dans une solution SaaS pour écoles, un utilisateur (enseignant, administrateur ou parent)
 * peut appartenir à plusieurs établissements simultanément avec des rôles et des prérogatives distincts :
 * - Un enseignant peut dispenser des cours à la fois à l'École A et à l'École B.
 * - La table `users` contient uniquement les informations d'authentification (identifiants / mot de passe).
 * - La table `school_user` gère l'adhésion, le statut et le rôle effectif au sein de chaque établissement.
 * - La table `school_settings` stocke les configurations spécifiques à l'établissement (logos, barèmes,
 *   coordonnées bancaires, modèles de bulletins) sans interférer avec les autres locataires.
 * 
 * Tables créées :
 * 1. school_user     : Table pivot centrale (Users ↔ Schools) pour l'appartenance et le RBAC scopé.
 * 2. school_settings : Paramètres et préférences isolés par établissement.
 */
return new class extends Migration
{
    /**
     * Exécution des modifications de Niveau 2 : Fondation Tenant & Pivot RBAC.
     */
    public function up(): void
    {
        // --------------------------------------------------------------------------------------
        // 1. TABLE : school_user (Pivot d'adhésion et de droits par établissement)
        // --------------------------------------------------------------------------------------
        // Établit le lien N-N entre les utilisateurs et les établissements scolaires.
        // Chaque ligne formalise l'affectation d'un utilisateur à une école avec un rôle précis.
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('school_user')) {
            Schema::create('school_user', function (Blueprint $table) {
                $table->id()->comment('Clé primaire de l\'adhésion');
                
                // Clés étrangères
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade')
                    ->comment('Référence à l\'identité globale dans la table users');
                
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Référence à l\'établissement scolaire fréquenté');
                
                // Rôle et privilèges au sein de cet établissement spécifique
                $table->string('role', 50)
                    ->default('student')
                    ->comment('Rôle dans cette école : admin, teacher, student, parent, staff, operator');
                
                // Statut de l'adhésion
                $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])
                    ->default('active')
                    ->comment('État de l\'accès de l\'utilisateur dans cet établissement');
                
                // Historique et date d'entrée
                $table->timestamp('joined_at')->nullable()->comment('Date d\'intégration ou d\'inscription dans l\'école');
                $table->timestamps();

                // Contraintes d'intégrité et index de performance
                // Un utilisateur peut avoir un rôle unique ou distinct par établissement
                $table->unique(['school_id', 'user_id', 'role'], 'school_user_unique_membership');
                $table->index(['school_id', 'status']);
                $table->index(['user_id', 'status']);
            });
        }

        // --------------------------------------------------------------------------------------
        // 2. TABLE : school_settings (Paramètres et préférences personnalisés par école)
        // --------------------------------------------------------------------------------------
        // Permet à chaque établissement d'administrer ses propres options sans toucher au code global
        // ni impacter les données des autres établissements (clé-valeur typée).
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('school_settings')) {
            Schema::create('school_settings', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Établissement propriétaire du paramètre');
                
                $table->string('key')->comment('Clé unique du paramètre (ex: academic.default_currency, exam.pass_rate)');
                $table->longText('value')->nullable()->comment('Valeur sérialisée ou texte du paramètre');
                $table->string('type', 30)->default('string')->comment('Type de donnée : string, boolean, integer, json, array');
                $table->timestamps();

                // Unicité de la clé au sein d'un même établissement scolaire
                $table->unique(['school_id', 'key'], 'school_settings_unique_key_per_school');
                $table->index('school_id');
            });
        }
    }

    /**
     * Annulation des modifications de Niveau 2.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('school_user');
    }
};
