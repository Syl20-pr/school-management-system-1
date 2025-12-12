<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentYear;

class StudentYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            ['name' => '2023 - 2024', 'is_current' => false], 
            ['name' => '2024 - 2025', 'is_current' => true],// Année en cours
        ];

        foreach ($years as $year) {
            StudentYear::create($year);
        }
    }
}