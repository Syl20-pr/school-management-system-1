<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ==============================================================================================
 * NIVEAU 5 : SCOPING MULTI-TENANT ROW-LEVEL (school_id) SUR LES TABLES DU DOMAINE MÉTIER
 * ==============================================================================================
 * 
 * Cette migration applique la stratégie centrale d'isolation des données définie dans :
 * "Multi-Tenant SaaS Modernization Roadmap" (Section 1, Section 3 & Section 6 - Stage 3/5).
 * 
 * Modèle Architectural : Shared Database / Tenant Scoped
 * Dans ce modèle, toutes les entités du domaine scolaire partagent les mêmes tables relationnelles
 * mais sont strictement isolées au niveau logique par la colonne `school_id`.
 * 
 * Principes de mise en œuvre :
 * 1. Ajout de la clé étrangère `school_id` nullable (pour permettre la transition sans coupure
 *    et le rétro-remplissage historique sans blocage de contrainte).
 * 2. Création automatique de contraintes de clé étrangère vers `schools(id)` avec suppression en cascade.
 * 3. Pose systématique d'index sur `school_id` et d'index composites `['school_id', ...]` pour que
 *    toutes les requêtes filtrées par le Global Scope Eloquent `TenantScope` soient exécutées
 *    en temps quasi-instantané (évite les scans séquentiels sur les grosses tables comme student_marks).
 * 
 * Tables du domaine métier concernées (34 tables) :
 * - Académique : student_classes, student_years, student_groups, student_shifts, school_subjects,
 *                assign_subjects, assign_subject_teaches, periods, classrooms, time_tables,
 *                time_table_slots, teacher_unavailabilities.
 * - Évaluations & Bulletins : exam_types, term_types, assign_exam_types, student_marks, marks_grades,
 *                             student_inaptitudes.
 * - Élèves & Cursus : assign_students, discount_students, student_absences, student_promotion_histories,
 *                     student_status_history.
 * - Finances & Comptabilité : fee_categories, fee_category_amounts, account_student_fees,
 *                             account_other_costs, account_employee_salaries.
 * - Ressources Humaines : designations, assign_designations, employee_sallary_logs, leave_purposes,
 *                         employee_leaves, employee_attendances.
 */
return new class extends Migration
{
    /**
     * Liste exhaustive des tables du domaine scolaire devant être partitionnées par école.
     */
    protected array $domainTables = [
        // Structure académique
        'student_classes',
        'student_years',
        'student_groups',
        'student_shifts',
        'school_subjects',
        'assign_subjects',
        'assign_subject_teaches',
        'periods',
        'classrooms',
        'time_tables',
        'time_table_slots',
        'teacher_unavailabilities',
        
        // Examens et évaluations
        'exam_types',
        'term_types',
        'assign_exam_types',
        'student_marks',
        'marks_grades',
        'student_inaptitudes',
        
        // Vie scolaire & Élèves
        'assign_students',
        'discount_students',
        'student_absences',
        'student_promotion_histories',
        'student_status_history',
        
        // Finances & Comptabilité
        'fee_categories',
        'fee_category_amounts',
        'account_student_fees',
        'account_other_costs',
        'account_employee_salaries',
        
        // Ressources Humaines
        'designations',
        'assign_designations',
        'employee_sallary_logs',
        'leave_purposes',
        'employee_leaves',
        'employee_attendances',
    ];

    /**
     * Exécution des modifications de Niveau 5 : Ajout de school_id et index d'isolation.
     */
    public function up(): void
    {
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'school_id')) {
                        // Ajout de la clé de partitionnement de l'établissement
                        $table->unsignedBigInteger('school_id')
                            ->nullable()
                            ->after('id')
                            ->comment('Identifiant de l\'établissement scolaire (Scoping multi-tenant)');

                        // Clé étrangère vers la table des écoles
                        $table->foreign('school_id')
                            ->references('id')
                            ->on('schools')
                            ->onDelete('cascade');

                        // Index simple d'isolation
                        $table->index('school_id', "{$tableName}_school_id_idx");
                    }
                });
            }
        }

        // --------------------------------------------------------------------------------------
        // Index composites multi-tenant spécialisés pour les requêtes à forte fréquence
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('assign_students')) {
            Schema::table('assign_students', function (Blueprint $table) {
                $table->index(['school_id', 'year_id', 'class_id'], 'assign_students_school_year_class_idx');
            });
        }

        if (Schema::hasTable('student_marks')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->index(['school_id', 'year_id', 'class_id'], 'student_marks_school_year_class_idx');
            });
        }

        if (Schema::hasTable('account_student_fees')) {
            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->index(['school_id', 'year_id', 'class_id'], 'account_student_fees_school_year_idx');
            });
        }

        if (Schema::hasTable('time_tables')) {
            Schema::table('time_tables', function (Blueprint $table) {
                $table->index(['school_id', 'academic_year_id', 'class_id'], 'time_tables_school_year_class_idx');
            });
        }
    }

    /**
     * Annulation des modifications de Niveau 5 : Retrait de school_id et des contraintes.
     */
    public function down(): void
    {
        // Retrait des index composites spécialisés
        if (Schema::hasTable('time_tables')) {
            Schema::table('time_tables', function (Blueprint $table) {
                $table->dropIndex('time_tables_school_year_class_idx');
            });
        }

        if (Schema::hasTable('account_student_fees')) {
            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->dropIndex('account_student_fees_school_year_idx');
            });
        }

        if (Schema::hasTable('student_marks')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->dropIndex('student_marks_school_year_class_idx');
            });
        }

        if (Schema::hasTable('assign_students')) {
            Schema::table('assign_students', function (Blueprint $table) {
                $table->dropIndex('assign_students_school_year_class_idx');
            });
        }

        // Retrait de school_id sur chaque table du domaine
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'school_id')) {
                        $table->dropForeign(['school_id']);
                        $table->dropIndex("{$tableName}_school_id_idx");
                        $table->dropColumn('school_id');
                    }
                });
            }
        }
    }
};
