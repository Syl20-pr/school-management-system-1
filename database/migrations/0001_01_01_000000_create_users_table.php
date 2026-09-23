<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création des tables de base pour la gestion des utilisateurs.
 *
 * Tables créées :
 *  - users              : Table centrale regroupant tous les utilisateurs
 *                         (étudiants, employés, administrateurs).
 *  - password_reset_tokens : Jetons de réinitialisation de mot de passe.
 *  - sessions           : Sessions actives des utilisateurs.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création des tables.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : users
        // Contient toutes les personnes du système (élèves, enseignants,
        // administrateurs). Le type d'utilisateur est déterminé par le
        // champ 'usertype' et le système de rôles Spatie.
        // ---------------------------------------------------------------
        Schema::create('users', function (Blueprint $table) {

            // --- Identification principale ---
            $table->id();                                                          // Clé primaire auto-incrémentée
            $table->string('usertype')->nullable()->comment('Student, Employee, Admin'); // Type d'utilisateur
            $table->string('name')->nullable();                                    // Nom complet de l'utilisateur
            $table->string('email')->nullable();                                   // Adresse e-mail (identifiant de connexion)
            $table->timestamp('email_verified_at')->nullable();                    // Date de vérification de l'e-mail
            $table->string('password');                                            // Mot de passe hashé (bcrypt)

            // --- Coordonnées personnelles ---
            $table->string('mobile')->nullable();                                  // Numéro de téléphone mobile
            $table->string('address')->nullable();                                 // Adresse postale complète
            $table->string('gender')->nullable();                                  // Genre (Masculin / Féminin)
            $table->string('image')->nullable();                                   // Chemin vers la photo de profil

            // --- Informations familiales ---
            $table->string('fname')->nullable();                                   // Nom du père (father name)
            $table->string('mname')->nullable();                                   // Nom de la mère (mother name)
            $table->string('f_no')->nullable();                                    // Numéro de téléphone du père

            // --- Informations civiles ---
            $table->string('religion')->nullable();                                // Religion de l'utilisateur
            $table->string('id_no')->nullable();                                   // Numéro d'identification unique (matricule)
            $table->date('dob')->nullable();                                       // Date de naissance (date of birth)
            $table->string('lob')->nullable();                                     // Lieu de naissance (location of birth)

            // --- Informations professionnelles / scolaires ---
            $table->string('code')->nullable();                                    // Code interne de l'utilisateur
            $table->string('role')->nullable()->comment(
                'admin = responsable logiciel, operator = opérateur, user = employé'
            );                                                                     // Rôle interne (distinct des rôles Spatie)
            $table->date('join_date')->nullable();                                 // Date d'entrée dans l'établissement
            $table->integer('designation_id')->nullable();                         // Référence au poste/grade occupé
            $table->double('salary')->nullable();                                  // Salaire mensuel brut

            // --- Statut du compte ---
            $table->tinyInteger('status')->default(1)->comment('0 = inactif, 1 = actif'); // Statut du compte utilisateur

            // --- Données de session et de profil ---
            $table->rememberToken();                                               // Jeton "se souvenir de moi"
            $table->foreignId('current_team_id')->nullable();                      // Équipe courante (Jetstream)
            $table->string('profile_photo_path', 2048)->nullable();               // Chemin vers la photo de profil (Jetstream)

            $table->timestamps();                                                  // created_at et updated_at
        });

        // ---------------------------------------------------------------
        // TABLE : password_reset_tokens
        // Stocke les jetons temporaires utilisés lors de la réinitialisation
        // du mot de passe via l'e-mail.
        // ---------------------------------------------------------------
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();    // E-mail de l'utilisateur (clé primaire)
            $table->string('token');               // Jeton de réinitialisation sécurisé
            $table->timestamp('created_at')->nullable(); // Date de création du jeton
        });

        // ---------------------------------------------------------------
        // TABLE : sessions
        // Gère les sessions actives des utilisateurs (driver : database).
        // Permet de suivre les connexions simultanées et l'activité.
        // ---------------------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                       // Identifiant unique de la session
            $table->foreignId('user_id')->nullable()->index();     // Référence à l'utilisateur connecté
            $table->string('ip_address', 45)->nullable();          // Adresse IP de la session
            $table->text('user_agent')->nullable();                // Navigateur / agent utilisateur
            $table->longText('payload');                           // Données sérialisées de la session
            $table->integer('last_activity')->index();             // Timestamp de la dernière activité
        });
    }

    /**
     * Annulation de la migration : suppression des tables dans l'ordre inverse.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');                  // Suppression de la table des utilisateurs
        Schema::dropIfExists('password_reset_tokens'); // Suppression des jetons de réinitialisation
        Schema::dropIfExists('sessions');              // Suppression des sessions
    }
};
