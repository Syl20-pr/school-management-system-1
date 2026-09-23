<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ==============================================================================================
 * NIVEAU 1 : ARCHITECTURE PLATEFORME CENTRALE MULTI-TENANT (CENTRAL PLATFORM)
 * ==============================================================================================
 * 
 * Cette migration met en œuvre le premier niveau architectural décrit dans la feuille de route :
 * "Multi-Tenant SaaS Modernization Roadmap" (Section 1 & 3 - Central Platform Tables).
 * 
 * Contexte & Justification :
 * Dans une architecture SaaS multi-tenant à base de données partagée (Shared DB / Tenant Scoped),
 * les données de la plateforme globale doivent être strictement isolées des données métiers d'un établissement.
 * Ces tables centrales ne portent PAS de 'school_id' propre puisqu'elles gèrent l'infrastructure,
 * les souscriptions, les domaines de routage et les super-administrateurs transversaux.
 * 
 * Tables créées :
 * 1. schools              : Entité racine des établissements (tenants).
 * 2. school_domains       : Routage DNS et sous-domaines personnalisés pour chaque établissement.
 * 3. platform_admins      : Super-administrateurs transversaux de la plateforme SaaS.
 * 4. school_subscriptions : Plans de facturation, licences et quotas d'élèves/stockage.
 * 5. global_settings      : Paramètres système globaux de la plateforme.
 */
return new class extends Migration
{
    /**
     * Exécution des modifications de Niveau 1 : Création des tables centrales SaaS.
     */
    public function up(): void
    {
        // --------------------------------------------------------------------------------------
        // 1. TABLE : schools (Entité racine des locataires / Tenants)
        // --------------------------------------------------------------------------------------
        // Représente chaque établissement scolaire hébergé sur la plateforme SaaS.
        // Utilise un identifiant numérique auto-incrémenté pour la performance des clés étrangères
        // combiné à un UUID public immuable pour les échanges sécurisés et API externes.
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                // Clés d'identification
                $table->id()->comment('Clé primaire interne de l\'établissement');
                $table->uuid('uuid')->unique()->comment('Identifiant universel public (ULID/UUID) pour sécurité API');
                
                // Informations signalétiques
                $table->string('name')->comment('Raison sociale / Nom officiel de l\'établissement scolaire');
                $table->string('slug')->unique()->comment('Identifiant URL unique (ex: ecole-grapmult)');
                $table->string('code')->unique()->comment('Code court ou matricule administratif officiel (ex: EGM)');
                
                // Coordonnées de contact de l\'administration de l\'école
                $table->string('email')->nullable()->comment('Adresse e-mail administrative principale');
                $table->string('phone')->nullable()->comment('Ligne téléphonique principale');
                $table->text('address')->nullable()->comment('Adresse physique ou géographique de l\'établissement');
                $table->string('logo')->nullable()->comment('Chemin vers le logo officiel stocké');
                
                // Statut opérationnel et configuration régionale
                $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])
                    ->default('active')
                    ->comment('État du compte locataire : active, inactive, suspended, trial');
                $table->string('timezone', 50)->default('Africa/Porto-Novo')->comment('Fuseau horaire de référence de l\'école');
                
                // Traçabilité temporelle
                $table->timestamps();
                
                // Index d'optimisation
                $table->index('status');
                $table->index('created_at');
            });
        }

        // --------------------------------------------------------------------------------------
        // 2. TABLE : school_domains (Routage dynamique et domaines personnalisés)
        // --------------------------------------------------------------------------------------
        // Permet l'isolation du locataire via le nom de domaine ou sous-domaine
        // (ex : ecole-a.plateforme.com ou mon-ecole.edu).
        // Résolution requise au niveau du middleware TenantContext (Section 4 du document SaaS).
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('school_domains')) {
            Schema::create('school_domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Référence à l\'établissement propriétaire du domaine');
                
                $table->string('domain')->unique()->comment('Nom de domaine ou sous-domaine complet (FQDN)');
                $table->boolean('is_primary')->default(false)->comment('Indique s\'il s\'agit du domaine d\'accès par défaut');
                $table->boolean('is_verified')->default(false)->comment('Indique si la validation DNS a été effectuée');
                $table->timestamps();

                $table->index(['school_id', 'is_primary']);
            });
        }

        // --------------------------------------------------------------------------------------
        // 3. TABLE : platform_admins (Super-administrateurs globaux)
        // --------------------------------------------------------------------------------------
        // Gère les utilisateurs ayant des privilèges transversaux sur l'ensemble de la plateforme SaaS
        // (Superviseurs, support technique niveau 3, auditeurs). Ils disposent d'un flag 'global_access = true'.
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('platform_admins')) {
            Schema::create('platform_admins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade')
                    ->comment('Référence vers le compte utilisateur centralisé');
                
                $table->enum('role', ['superadmin', 'support', 'auditor'])
                    ->default('superadmin')
                    ->comment('Niveau de privilège au niveau plateforme');
                $table->boolean('is_active')->default(true)->comment('Activation de l\'accès administrateur plateforme');
                $table->timestamps();

                $table->unique('user_id');
            });
        }

        // --------------------------------------------------------------------------------------
        // 4. TABLE : school_subscriptions (Gestion des abonnements et quotas SaaS)
        // --------------------------------------------------------------------------------------
        // Encadre les offres commerciales, limites de volumétrie et périodes de validité de chaque école.
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('school_subscriptions')) {
            Schema::create('school_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Établissement abonné');
                
                $table->string('plan_name')->default('standard')->comment('Nom du plan d\'abonnement (starter, standard, enterprise)');
                $table->timestamp('starts_at')->nullable()->comment('Date de début de la période de validité');
                $table->timestamp('ends_at')->nullable()->comment('Date d\'expiration de l\'abonnement');
                $table->enum('status', ['trialing', 'active', 'past_due', 'canceled', 'expired'])
                    ->default('active')
                    ->comment('Statut de facturation');
                
                // Quotas de sécurité et de dimensionnement
                $table->integer('max_students')->nullable()->comment('Plafond du nombre d\'élèves inscrits autorisés');
                $table->integer('max_storage_mb')->nullable()->comment('Quota de stockage alloué pour les pièces jointes (en Mo)');
                $table->timestamps();

                $table->index(['school_id', 'status']);
            });
        }

        // --------------------------------------------------------------------------------------
        // 5. TABLE : global_settings (Configuration globale de la plateforme SaaS)
        // --------------------------------------------------------------------------------------
        // Stocke les configurations applicatives transversales au niveau du serveur / de la plateforme
        // (clés d'API tierces globales, modes de maintenance, paramètres d'onboarding).
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('global_settings')) {
            Schema::create('global_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->comment('Clé de configuration globale unique');
                $table->longText('value')->nullable()->comment('Valeur sérialisée ou brute du paramètre');
                $table->string('type', 30)->default('string')->comment('Type de valeur : string, boolean, integer, json');
                $table->string('description')->nullable()->comment('Explication fonctionnelle de la clé');
                $table->timestamps();
            });
        }
    }

    /**
     * Annulation des modifications de Niveau 1 (Ordre inverse pour respecter l'intégrité référentielle).
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
        Schema::dropIfExists('school_subscriptions');
        Schema::dropIfExists('platform_admins');
        Schema::dropIfExists('school_domains');
        Schema::dropIfExists('schools');
    }
};
