<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ==============================================================================================
 * NIVEAU 6 : PRÉSERVATION DES DONNÉES HISTORIQUES & PROVISIONNEMENT DU PREMIER TENANT (ECOLE-GRAPMULT)
 * ==============================================================================================
 * 
 * Cette migration concrétise le principe fondamental énoncé dans :
 * "Multi-Tenant SaaS Modernization Roadmap" (Section 2 - Important Data Preservation Principle & Section 6).
 * 
 * Directive impérative : "Zero Data Loss (0% target data loss)"
 * Les données historiques existantes de la base de données (nanegbe_juil_26) ne doivent sous aucun
 * prétexte être supprimées ou altérées de manière destructive. L'ensemble des tables préexistantes
 * est rattaché intégralement et sans perte au premier locataire officiel de la plateforme :
 * "ECOLE-GRAPMULT" (ID = 1, slug = 'ecole-grapmult', code = 'EGM').
 * 
 * Actions exécutées à ce niveau :
 * 1. Provisionnement automatique de l'établissement initial ECOLE-GRAPMULT dans la table `schools`.
 * 2. Création de son domaine d'accès primaire dans `school_domains`.
 * 3. Rétro-remplissage sécurisé (backfill) de `school_id = 1` sur l'ensemble des enregistrements
 *    orphelins des 34 tables du domaine métier scolaire.
 * 4. Découplage effectif des identités :
 *    - Inscription de tous les comptes utilisateurs existants dans `school_user` avec leurs rôles respectifs.
 *    - Migration des données élèves vers la table spécialisée `student_profiles`.
 *    - Migration des données collaborateurs vers la table spécialisée `employee_profiles`.
 */
return new class extends Migration {
    /**
     * Liste des tables du domaine métier à rétro-remplir avec school_id = 1.
     */
    protected array $domainTables = [
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
        'exam_types',
        'term_types',
        'assign_exam_types',
        'student_marks',
        'marks_grades',
        'student_inaptitudes',
        'assign_students',
        'discount_students',
        'student_absences',
        'student_promotion_histories',
        'student_status_history',
        'fee_categories',
        'fee_category_amounts',
        'account_student_fees',
        'account_other_costs',
        'account_employee_salaries',
        'designations',
        'assign_designations',
        'employee_sallary_logs',
        'leave_purposes',
        'employee_leaves',
        'employee_attendances',
    ];

    /**
     * Exécution de la migration de données Niveau 6.
     */
    public function up(): void
    {
        // --------------------------------------------------------------------------------------
        // ÉTAPE 1 : Création du premier Tenant "ECOLE-GRAPMULT" dans la table schools
        // --------------------------------------------------------------------------------------
        $now = now();
        $schoolId = 1;

        if (Schema::hasTable('schools')) {
            $existingSchool = DB::table('schools')->where('id', $schoolId)->orWhere('slug', 'ecole-grapmult')->first();

            if (!$existingSchool) {
                DB::table('schools')->insert([
                    'id' => $schoolId,
                    'uuid' => (string) Str::uuid(),
                    'name' => 'ECOLE-GRAPMULT',
                    'slug' => 'ecole-grapmult',
                    'code' => 'EGM',
                    'email' => 'administration@ecole-grapmult.com',
                    'phone' => '+229 00 00 00 00',
                    'address' => 'Siège social de l\'établissement ECOLE-GRAPMULT',
                    'status' => 'active',
                    'timezone' => 'Africa/Porto-Novo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $schoolId = $existingSchool->id;
            }
        }

        // --------------------------------------------------------------------------------------
        // ÉTAPE 2 : Configuration du domaine principal dans school_domains
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('school_domains')) {
            $domainExists = DB::table('school_domains')->where('school_id', $schoolId)->where('is_primary', true)->exists();
            if (!$domainExists) {
                DB::table('school_domains')->insert([
                    'school_id' => $schoolId,
                    'domain' => 'ecole-grapmult.plateforme.local',
                    'is_primary' => true,
                    'is_verified' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // --------------------------------------------------------------------------------------
        // ÉTAPE 3 : Rétro-remplissage (Backfill) de school_id = 1 sur toutes les entités métiers
        // --------------------------------------------------------------------------------------
        // Garantit qu'aucun enregistrement existant (notes, inscriptions, salaires, classes) ne devienne
        // orphelin lors de l'activation des Global Scopes multi-tenant.
        // --------------------------------------------------------------------------------------
        foreach ($this->domainTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                DB::table($table)
                    ->whereNull('school_id')
                    ->update(['school_id' => $schoolId]);
            }
        }

        // --------------------------------------------------------------------------------------
        // ÉTAPE 4 : Rattachement de l'ensemble des utilisateurs existants dans school_user
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('users') && Schema::hasTable('school_user')) {
            $users = DB::table('users')->get();

            foreach ($users as $user) {
                // Détermination du rôle normalisé
                $role = 'student';
                $usertype = strtolower($user->usertype ?? '');
                $userRole = strtolower($user->role ?? '');

                if ($usertype === 'admin' || $userRole === 'admin') {
                    $role = 'admin';
                } elseif ($usertype === 'employee' || $userRole === 'operator' || $userRole === 'user') {
                    $role = 'teacher';
                } elseif ($usertype === 'student') {
                    $role = 'student';
                }

                $alreadyMember = DB::table('school_user')
                    ->where('school_id', $schoolId)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$alreadyMember) {
                    DB::table('school_user')->insert([
                        'school_id' => $schoolId,
                        'user_id' => $user->id,
                        'role' => $role,
                        'status' => 'active',
                        'joined_at' => $user->created_at ?? $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // --------------------------------------------------------------------------------------
        // ÉTAPE 5 : Remplissage des profils découplés (student_profiles et employee_profiles)
        // --------------------------------------------------------------------------------------
        if (Schema::hasTable('users')) {
            // 5.1 Extraction des profils élèves
            if (Schema::hasTable('student_profiles')) {
                // Élèves identifiés par usertype = 'Student' ou inscrits dans assign_students
                $studentUserIds = DB::table('users')
                    ->where('usertype', 'Student')
                    ->pluck('id')
                    ->toArray();

                if (Schema::hasTable('assign_students')) {
                    $assignedIds = DB::table('assign_students')->pluck('student_id')->toArray();
                    $studentUserIds = array_unique(array_merge($studentUserIds, $assignedIds));
                }

                foreach ($studentUserIds as $userId) {
                    $user = DB::table('users')->where('id', $userId)->first();
                    if (!$user) {
                        continue;
                    }

                    $profileExists = DB::table('student_profiles')
                        ->where('school_id', $schoolId)
                        ->where('user_id', $user->id)
                        ->exists();

                    if (!$profileExists) {
                        DB::table('student_profiles')->insert([
                            'school_id' => $schoolId,
                            'user_id' => $user->id,
                            'id_no' => $user->id_no ?? null,
                            'fname' => $user->fname ?? null,
                            'mname' => $user->mname ?? null,
                            'f_no' => $user->f_no ?? null,
                            'dob' => $user->dob ?? null,
                            'lob' => $user->lob ?? null,
                            'gender' => $user->gender ?? null,
                            'religion' => $user->religion ?? null,
                            'address' => $user->address ?? null,
                            'mobile' => $user->mobile ?? null,
                            'image' => $user->image ?? null,
                            'created_at' => $user->created_at ?? $now,
                            'updated_at' => $user->updated_at ?? $now,
                        ]);
                    }
                }
            }

            // 5.2 Extraction des profils employés
            if (Schema::hasTable('employee_profiles')) {
                $employees = DB::table('users')
                    ->where('usertype', 'Employee')
                    ->orWhereNotNull('designation_id')
                    ->orWhereNotNull('salary')
                    ->orWhereNotNull('join_date')
                    ->get();

                foreach ($employees as $employee) {
                    $empProfileExists = DB::table('employee_profiles')
                        ->where('school_id', $schoolId)
                        ->where('user_id', $employee->id)
                        ->exists();

                    if (!$empProfileExists) {
                        DB::table('employee_profiles')->insert([
                            'school_id' => $schoolId,
                            'user_id' => $employee->id,
                            'id_no' => $employee->id_no ?? $employee->code ?? null,
                            'designation_id' => $employee->designation_id ?? null,
                            'join_date' => $employee->join_date ?? null,
                            'salary' => $employee->salary ?? null,
                            'gender' => $employee->gender ?? null,
                            'dob' => $employee->dob ?? null,
                            'lob' => $employee->lob ?? null,
                            'religion' => $employee->religion ?? null,
                            'address' => $employee->address ?? null,
                            'mobile' => $employee->mobile ?? null,
                            'image' => $employee->image ?? null,
                            'status' => $employee->status ?? 1,
                            'created_at' => $employee->created_at ?? $now,
                            'updated_at' => $employee->updated_at ?? $now,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Annulation de la migration de données.
     */
    public function down(): void
    {
        // En mode production, les données historiques ne sont pas supprimées brutalement
        // pour préserver la règle "0% data loss".
    }
};
