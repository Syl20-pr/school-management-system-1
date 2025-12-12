<?php
// database/seeders/ClassroomsTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classroom;

class ClassroomsTableSeeder extends Seeder
{
    public function run()
    {
        $classrooms = [
            ['name' => 'Salle 101', 'capacity' => 30, 'features' => 'Projecteur, Tableau blanc'],
            ['name' => 'Salle 102', 'capacity' => 25, 'features' => 'Tableau interactif'],
            ['name' => 'Salle 103', 'capacity' => 35, 'features' => 'Ordinateurs'],
            ['name' => 'Salle 201', 'capacity' => 40, 'features' => 'Laboratoire de sciences'],
            ['name' => 'Salle 202', 'capacity' => 30, 'features' => 'Projecteur, Son'],
            ['name' => 'Salle 203', 'capacity' => 25, 'features' => 'Tableau vert'],
            ['name' => 'Salle 301', 'capacity' => 35, 'features' => 'Informatique'],
            ['name' => 'Salle 302', 'capacity' => 30, 'features' => 'Arts plastiques'],
            ['name' => 'Salle 303', 'capacity' => 20, 'features' => 'Musique'],
            ['name' => 'Salle 401', 'capacity' => 50, 'features' => 'Amphithéâtre']
        ];

        foreach ($classrooms as $classroom) {
            Classroom::create($classroom);
        }
    }
}