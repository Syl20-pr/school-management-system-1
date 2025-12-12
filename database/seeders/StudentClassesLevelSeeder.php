<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassesLevelSeeder extends Seeder
{
    public function run()
    {
        $classesLevels = [
            // Classes de 6ème
            ['6ème A', 6, '6ème'],
            ['6ème B', 6, '6ème'],
            ['6ème C', 6, '6ème'],
            ['6ème D', 6, '6ème'],
            
            // Classes de 5ème
            ['5ème A', 5, '5ème'],
            ['5ème B', 5, '5ème'],
            ['5ème C', 5, '5ème'],
            ['5ème D', 5, '5ème'],
            
            // Classes de 4ème
            ['4ème A', 4, '4ème'],
            ['4ème B', 4, '4ème'],
            ['4ème C', 4, '4ème'],
            ['4ème D', 4, '4ème'],
            
            // Classes de 3ème
            ['3ème A', 3, '3ème'],
            ['3ème B', 3, '3ème'],
            ['3ème C', 3, '3ème'],
            ['3ème D', 3, '3ème'],
            ['3ème E', 3, '3ème'],
            
            // Classes de 2nd
            ['2nd A4-1', 2, '2nd'],
            ['2nd A4-2', 2, '2nd'],
            ['2nd CD', 2, '2nd'],
            
            // Classes de 1ère
            ['1ère A4-1', 1, '1ère'],
            ['1ère A4-2', 1, '1ère'],
            ['1ere D4', 1, '1ère'],
            
            // Classes de Terminale
            ['Tle A4-1', 0, 'Terminale'],
            ['Tle A4-2', 0, 'Terminale'],
            ['Tle C4', 0, 'Terminale'],
            ['Tle D4-1', 0, 'Terminale'],
            ['Tle D4-2', 0, 'Terminale'],
        ];

        foreach ($classesLevels as $classInfo) {
            DB::table('student_classes')
                ->where('name', $classInfo[0])
                ->update([
                    'level' => $classInfo[1],
                    'level_name' => $classInfo[2]
                ]);
        }

        // Pour la classe test qui n'a pas de niveau spécifique
        DB::table('student_classes')
            ->where('name', 'CLASS_TEST')
            ->update([
                'level' => null,
                'level_name' => null
            ]);
    }
}