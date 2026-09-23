<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ==============================================================================================
 * NIVEAU 4 : INTÉGRITÉ DU SCHÉMA, CLÉS ÉTRANGÈRES ET INDEX COMPOSITES (SCHEMA INTEGRITY)
 * ==============================================================================================
 * 
 * Cette migration résout les déficiences critiques identifiées dans le document d'audit :
 * "Backend Revision Plan & Architectural Roadmap" (Section 2 - Schema Drift & Bad FKs / Phase 3 & 4).
 * 
 * Contexte & Justification :
 * 1. Dans les premières migrations du projet, de nombreuses relations étaient déclarées avec de simples
 *    colonnes `$table->integer()` sans contraintes de clés étrangères formelles (cohérence purement applicative).
 *    Cela engendrait des risques d'orphelins, de dérive des données (`schema drift`) et d'incohérences.
 * 2. L'absence d'index composites sur les clés de filtrage courantes (ex: recherche de notes par année/classe/matière,
 *    frais par élève/année) dégradait gravement les performances lors des calculs de bulletins et de relevés.
 * 3. Cette migration aligne les types de colonnes sur `unsignedBigInteger` et installe les contraintes
 *    référentielles ainsi que les index composites de haute performance.
 * 
 * Tables modifiées :
 * - assign_students          : FK vers users, student_classes, student_years, groups, shifts + index composites.
 * - fee_category_amounts     : FK vers fee_categories, student_classes + index unique composite.
 * - assign_subjects          : FK vers student_classes, school_subjects + index composite.
 * - student_marks            : FK vers users, student_years, student_classes, assign_subjects, exam_types + index.
 * - account_student_fees     : FK vers student_years, student_classes, users, fee_categories + index.
 * - account_employee_salaries: FK vers users + index composite [employee_id, date].
 * - discount_students        : FK vers assign_students, fee_categories.
 * - employee_sallary_logs    : FK vers users.
 * - employee_leaves          : FK vers users, leave_purposes.
 * - employee_attendances     : FK vers users + index composite [employee_id, date].
 */
return new class extends Migration
{
    /**
     * Exécution des modifications de Niveau 4 : Harmonisation des clés et index composites.
     */
    public function up(): void
    {
        // --------------------------------------------------------------------------------------
        // 1. TABLE : assign_students (Inscriptions des élèves par classe et année)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('assign_students')) {
            // Passe 1 : Harmonisation des types de colonnes
            Schema::table('assign_students', function (Blueprint $table) {
                $table->unsignedBigInteger('student_id')->change();
                $table->unsignedBigInteger('class_id')->change();
                $table->unsignedBigInteger('year_id')->change();
                $table->unsignedBigInteger('group_id')->nullable()->change();
                $table->unsignedBigInteger('shift_id')->nullable()->change();
            });

            // Passe 2 : Contraintes de clés étrangères et index composites
            Schema::table('assign_students', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
                $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
                $table->foreign('group_id')->references('id')->on('student_groups')->onDelete('set null');
                $table->foreign('shift_id')->references('id')->on('student_shifts')->onDelete('set null');

                // Index composites pour optimiser les listings de classe et les recherches d'élèves par promotion
                $table->index(['year_id', 'class_id'], 'assign_students_year_class_idx');
                $table->index(['student_id', 'year_id', 'class_id'], 'assign_students_student_year_class_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 2. TABLE : fee_category_amounts (Montants des frais par catégorie et classe)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('fee_category_amounts')) {
            Schema::table('fee_category_amounts', function (Blueprint $table) {
                $table->unsignedBigInteger('fee_category_id')->change();
                $table->unsignedBigInteger('class_id')->change();
            });

            Schema::table('fee_category_amounts', function (Blueprint $table) {
                $table->foreign('fee_category_id')->references('id')->on('fee_categories')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');

                // Unicité : une seule définition de montant par catégorie et par classe
                $table->unique(['fee_category_id', 'class_id'], 'fee_category_amounts_unique_cat_class');
            });
        }

        // --------------------------------------------------------------------------------------
        // 3. TABLE : assign_subjects (Attribution des matières par classe avec barèmes)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('assign_subjects')) {
            Schema::table('assign_subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('class_id')->change();
                $table->unsignedBigInteger('subject_id')->change();
            });

            Schema::table('assign_subjects', function (Blueprint $table) {
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('school_subjects')->onDelete('cascade');

                // Unicité : une matière ne peut être affectée qu'une seule fois à une même classe
                $table->unique(['class_id', 'subject_id'], 'assign_subjects_class_subject_unique');
            });
        }

        // --------------------------------------------------------------------------------------
        // 4. TABLE : student_marks (Notes et évaluations scolaires)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('student_marks')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->unsignedBigInteger('student_id')->change();
                $table->unsignedBigInteger('year_id')->nullable()->change();
                $table->unsignedBigInteger('class_id')->nullable()->change();
                $table->unsignedBigInteger('assign_subject_id')->nullable()->change();
                $table->unsignedBigInteger('exam_type_id')->nullable()->change();
            });

            Schema::table('student_marks', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
                $table->foreign('assign_subject_id')->references('id')->on('assign_subjects')->onDelete('cascade');
                $table->foreign('exam_type_id')->references('id')->on('exam_types')->onDelete('cascade');

                // Index composites haute performance pour la saisie collective et la génération de bulletins
                $table->index(['year_id', 'class_id', 'assign_subject_id'], 'marks_lookup_composite_idx');
                $table->index(['student_id', 'year_id', 'class_id'], 'marks_student_year_class_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 5. TABLE : account_student_fees (Paiements et encaissements de scolarité)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('account_student_fees')) {
            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->unsignedBigInteger('year_id')->nullable()->change();
                $table->unsignedBigInteger('class_id')->nullable()->change();
                $table->unsignedBigInteger('student_id')->nullable()->change();
                $table->unsignedBigInteger('fee_category_id')->nullable()->change();
            });

            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->foreign('year_id')->references('id')->on('student_years')->onDelete('set null');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('set null');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('fee_category_id')->references('id')->on('fee_categories')->onDelete('cascade');

                $table->index(['year_id', 'class_id', 'fee_category_id'], 'account_fees_search_idx');
                $table->index(['student_id', 'year_id'], 'account_fees_student_year_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 6. TABLE : account_employee_salaries (Salaires et paiements du personnel)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('account_employee_salaries')) {
            Schema::table('account_employee_salaries', function (Blueprint $table) {
                $table->unsignedBigInteger('employee_id')->change();
            });

            Schema::table('account_employee_salaries', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['employee_id', 'date'], 'employee_salaries_emp_date_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 7. TABLE : discount_students (Remises et réductions accordées sur les frais)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('discount_students')) {
            Schema::table('discount_students', function (Blueprint $table) {
                $table->unsignedBigInteger('assign_student_id')->nullable()->change();
                $table->unsignedBigInteger('fee_category_id')->nullable()->change();
            });

            Schema::table('discount_students', function (Blueprint $table) {
                $table->foreign('assign_student_id')->references('id')->on('assign_students')->onDelete('cascade');
                $table->foreign('fee_category_id')->references('id')->on('fee_categories')->onDelete('cascade');
                $table->index(['assign_student_id', 'fee_category_id'], 'discount_students_assign_fee_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 8. TABLE : employee_sallary_logs (Historique des modifications salariales)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('employee_sallary_logs')) {
            Schema::table('employee_sallary_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('employee_id')->change();
            });

            Schema::table('employee_sallary_logs', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('employee_id');
            });
        }

        // --------------------------------------------------------------------------------------
        // 9. TABLE : employee_leaves (Gestion des congés du personnel)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                $table->unsignedBigInteger('employee_id')->change();
                $table->unsignedBigInteger('leave_purpose_id')->change();
            });

            Schema::table('employee_leaves', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('leave_purpose_id')->references('id')->on('leave_purposes')->onDelete('cascade');
                $table->index(['employee_id', 'start_date', 'end_date'], 'employee_leaves_emp_dates_idx');
            });
        }

        // --------------------------------------------------------------------------------------
        // 10. TABLE : employee_attendances (Feuille d'émargement et présences des employés)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('employee_attendances')) {
            Schema::table('employee_attendances', function (Blueprint $table) {
                $table->unsignedBigInteger('employee_id')->change();
            });

            Schema::table('employee_attendances', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');

                // Évite les doublons d'émargement pour le même employé à la même date
                $table->index(['employee_id', 'date'], 'employee_attendances_emp_date_idx');
            });
        }
    }

    /**
     * Annulation des modifications de Niveau 4.
     */
    public function down(): void
    {
        // Suppression sécurisée des contraintes de clés étrangères
        if (Schema::hasTable('employee_attendances')) {
            Schema::table('employee_attendances', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
                $table->dropIndex('employee_attendances_emp_date_idx');
            });
        }

        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
                $table->dropForeign(['leave_purpose_id']);
                $table->dropIndex('employee_leaves_emp_dates_idx');
            });
        }

        if (Schema::hasTable('employee_sallary_logs')) {
            Schema::table('employee_sallary_logs', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
            });
        }

        if (Schema::hasTable('discount_students')) {
            Schema::table('discount_students', function (Blueprint $table) {
                $table->dropForeign(['assign_student_id']);
                $table->dropForeign(['fee_category_id']);
            });
        }

        if (Schema::hasTable('account_employee_salaries')) {
            Schema::table('account_employee_salaries', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
                $table->dropIndex('employee_salaries_emp_date_idx');
            });
        }

        if (Schema::hasTable('account_student_fees')) {
            Schema::table('account_student_fees', function (Blueprint $table) {
                $table->dropForeign(['year_id']);
                $table->dropForeign(['class_id']);
                $table->dropForeign(['student_id']);
                $table->dropForeign(['fee_category_id']);
            });
        }

        if (Schema::hasTable('student_marks')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->dropForeign(['year_id']);
                $table->dropForeign(['class_id']);
                $table->dropForeign(['assign_subject_id']);
                $table->dropForeign(['exam_type_id']);
            });
        }

        if (Schema::hasTable('assign_subjects')) {
            Schema::table('assign_subjects', function (Blueprint $table) {
                $table->dropForeign(['class_id']);
                $table->dropForeign(['subject_id']);
            });
        }

        if (Schema::hasTable('fee_category_amounts')) {
            Schema::table('fee_category_amounts', function (Blueprint $table) {
                $table->dropForeign(['fee_category_id']);
                $table->dropForeign(['class_id']);
            });
        }

        if (Schema::hasTable('assign_students')) {
            Schema::table('assign_students', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->dropForeign(['class_id']);
                $table->dropForeign(['year_id']);
                $table->dropForeign(['group_id']);
                $table->dropForeign(['shift_id']);
            });
        }
    }
};
