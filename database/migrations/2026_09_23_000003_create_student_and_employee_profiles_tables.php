<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ==============================================================================================
 * NIVEAU 3 : DÉCOUPLAGE DES IDENTITÉS (IDENTITY DECOUPLING - REFONTE DU GOD CLASS USER)
 * ==============================================================================================
 * 
 * Cette migration met en application directe la recommandation critique du document :
 * "Backend Revision Plan & Architectural Roadmap" (Section 2 - God Class Anti-pattern & Phase 3/4).
 * 
 * Contexte & Justification :
 * Dans le schéma initial, la table `users` gérait de façon monolithique l'ensemble des attributs
 * hétérogènes des élèves (parents, date/lieu de naissance, matricule), des employés (date d'embauche,
 * salaire brut, poste/designation) et des administrateurs. Cette surcharge entraînait :
 * - Un gonflement excessif de la table (`schema bloat`),
 * - Une fragilité majeure lors de la validation des formulaires,
 * - L'impossibilité de gérer proprement un profil dans plusieurs écoles sans dupliquer l'identité globale.
 * 
 * Solution architecturale :
 * 1. La table `users` est recentrée sur sa responsabilité première : l'authentification et les identifiants purs.
 * 2. Création de la table `student_profiles` : concentre les données pédagogiques, civiles et familiales des élèves.
 * 3. Création de la table `employee_profiles` : concentre le parcours RH, le salaire et l'affectation du personnel.
 * 
 * Les deux profils sont nativement scopés par `school_id`, garantissant la compatibilité multi-tenant.
 */
return new class extends Migration
{
    /**
     * Exécution des modifications de Niveau 3 : Création des tables de profils spécialisés.
     */
    public function up(): void
    {
        // --------------------------------------------------------------------------------------
        // 1. TABLE : student_profiles (Profils pédagogiques et civils des élèves)
        // --------------------------------------------------------------------------------------
        // Isole les données relatives à la scolarité et à la situation familiale de l'élève.
        // Lié de façon univoque au compte utilisateur (users.id) et à l'établissement (schools.id).
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('student_profiles')) {
            Schema::create('student_profiles', function (Blueprint $table) {
                $table->id()->comment('Clé primaire du profil élève');
                
                // Clés d'association
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade')
                    ->comment('Référence à l\'utilisateur central');
                
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Établissement scolaire d\'inscription de l\'élève');
                
                // Identification scolaire
                $table->string('id_no')->nullable()->comment('Numéro matricule unique de l\'élève dans l\'établissement');
                
                // Filiation et tuteurs légaux (Parents)
                $table->string('fname')->nullable()->comment('Nom et prénom du père');
                $table->string('mname')->nullable()->comment('Nom et prénom de la mère');
                $table->string('f_no')->nullable()->comment('Numéro de téléphone direct du père ou tuteur légal');
                
                // État civil de l'élève
                $table->date('dob')->nullable()->comment('Date de naissance (Date of Birth)');
                $table->string('lob')->nullable()->comment('Lieu de naissance (Location of Birth)');
                $table->string('gender', 20)->nullable()->comment('Genre : Masculin / Féminin');
                $table->string('religion', 50)->nullable()->comment('Affiliation religieuse (facultatif)');
                
                // Coordonnées & Données médicales / antécédents
                $table->string('mobile')->nullable()->comment('Numéro de téléphone propre de l\'élève');
                $table->text('address')->nullable()->comment('Adresse de résidence de l\'élève');
                $table->string('image')->nullable()->comment('Photo d\'identité de l\'élève');
                $table->string('blood_group', 10)->nullable()->comment('Groupe sanguin');
                $table->string('emergency_contact')->nullable()->comment('Personne à contacter en cas d\'urgence');
                $table->string('previous_school')->nullable()->comment('Établissement scolaire de provenance');
                
                $table->timestamps();

                // Contraintes et index
                $table->unique(['school_id', 'user_id'], 'student_profile_unique_per_school');
                $table->index(['school_id', 'id_no']);
                $table->index('user_id');
            });
        }

        // --------------------------------------------------------------------------------------
        // 2. TABLE : employee_profiles (Profils professionnels et RH des collaborateurs)
        // --------------------------------------------------------------------------------------
        // Isole les données contractuelles, salariales et d'affectation pour les enseignants et agents.
        // --------------------------------------------------------------------------------------
        if (!Schema::hasTable('employee_profiles')) {
            Schema::create('employee_profiles', function (Blueprint $table) {
                $table->id()->comment('Clé primaire du profil employé');
                
                // Clés d'association
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade')
                    ->comment('Référence au compte utilisateur central');
                
                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->onDelete('cascade')
                    ->comment('Établissement scolaire employeur');
                
                // Identification RH & Fonction
                $table->string('id_no')->nullable()->comment('Numéro matricule ou code interne de l\'employé');
                $table->unsignedBigInteger('designation_id')->nullable()->comment('Poste / Titre occupé (référence designations.id)');
                
                // Données contractuelles & Rémunération
                $table->date('join_date')->nullable()->comment('Date d\'entrée en service dans l\'établissement');
                $table->double('salary')->nullable()->comment('Salaire brut mensuel de base');
                $table->string('qualification')->nullable()->comment('Diplôme le plus élevé ou niveau d\'études');
                
                // État civil & Coordonnées
                $table->string('gender', 20)->nullable()->comment('Genre : Masculin / Féminin');
                $table->date('dob')->nullable()->comment('Date de naissance');
                $table->string('lob')->nullable()->comment('Lieu de naissance');
                $table->string('religion', 50)->nullable()->comment('Religion');
                $table->text('address')->nullable()->comment('Adresse de résidence');
                $table->string('mobile')->nullable()->comment('Numéro de téléphone de contact');
                $table->string('image')->nullable()->comment('Photo professionnelle');
                $table->string('emergency_contact')->nullable()->comment('Contact en cas d\'urgence');
                
                // Statut opérationnel RH
                $table->tinyInteger('status')->default(1)->comment('1 = en activité, 0 = contrat terminé ou suspendu');
                $table->timestamps();

                // Contraintes et index
                $table->unique(['school_id', 'user_id'], 'employee_profile_unique_per_school');
                $table->index(['school_id', 'id_no']);
                $table->index(['school_id', 'designation_id']);
                $table->index('user_id');
            });
        }
    }

    /**
     * Annulation des modifications de Niveau 3.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
