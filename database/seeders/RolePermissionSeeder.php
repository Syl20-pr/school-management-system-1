<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions
        $permissions = [
            'gestion promotions',
            'annuler promotions',
            'gestion élèves',
            'gestion enseignants',
            'gestion classes',
            'gestion années scolaires',
            'voir statistiques'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        $operatorRole = Role::create(['name' => 'operator']);
        $operatorRole->givePermissionTo([
            'gestion promotions',
            'gestion élèves',
            'gestion classes',
            'voir statistiques'
        ]);

        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'gestion élèves',
            'voir statistiques'
        ]);

        // Rôle pour les élèves (sans permissions spécifiques)
        Role::create(['name' => 'student']);
    }
}