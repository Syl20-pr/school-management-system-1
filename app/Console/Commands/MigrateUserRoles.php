<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MigrateUserRoles extends Command
{
    protected $signature = 'roles:migrate-users';
    protected $description = 'Migrer les utilisateurs existants vers le système de rôles Spatie';

    public function handle()
    {
        $this->info('Migration des rôles utilisateurs...');

        // Migrer les administrateurs
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            $user->assignRole('admin');
            $this->line("Admin: {$user->name}");
        }

        // Migrer les opérateurs
        $operatorUsers = User::where('role', 'operator')->get();
        foreach ($operatorUsers as $user) {
            $user->assignRole('operator');
            $this->line("Operator: {$user->name}");
        }

        // Migrer les enseignants
        $teacherUsers = User::where('usertype', 'Teacher')->get();
        foreach ($teacherUsers as $user) {
            $user->assignRole('teacher');
            $this->line("Teacher: {$user->name}");
        }

        // Migrer les élèves
        $studentUsers = User::where('usertype', 'Student')->get();
        foreach ($studentUsers as $user) {
            $user->assignRole('student');
            $this->line("Student: {$user->name}");
        }

        $this->info('Migration terminée !');
    }
}