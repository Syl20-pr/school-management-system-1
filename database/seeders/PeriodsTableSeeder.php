<?php
// database/seeders/PeriodsTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use Carbon\Carbon;

class PeriodsTableSeeder extends Seeder
{
    public function run()
    {
        $periods = [
            [
                'name' => 'P1',
                'start_time' => '08:00:00',
                'end_time' => '08:45:00',
                'order' => 1,
                'is_break' => false
            ],
            [
                'name' => 'P2',
                'start_time' => '08:45:00',
                'end_time' => '09:30:00',
                'order' => 2,
                'is_break' => false
            ],
            [
                'name' => 'Récréation',
                'start_time' => '09:30:00',
                'end_time' => '09:45:00',
                'order' => 3,
                'is_break' => true
            ],
            [
                'name' => 'P3',
                'start_time' => '09:45:00',
                'end_time' => '10:30:00',
                'order' => 4,
                'is_break' => false
            ],
            [
                'name' => 'P4',
                'start_time' => '10:30:00',
                'end_time' => '11:15:00',
                'order' => 5,
                'is_break' => false
            ],
            [
                'name' => 'P5',
                'start_time' => '11:15:00',
                'end_time' => '12:00:00',
                'order' => 6,
                'is_break' => false
            ],
            [
                'name' => 'Pause Midi',
                'start_time' => '12:00:00',
                'end_time' => '13:30:00',
                'order' => 7,
                'is_break' => true
            ],
            [
                'name' => 'P6',
                'start_time' => '13:30:00',
                'end_time' => '14:15:00',
                'order' => 8,
                'is_break' => false
            ],
            [
                'name' => 'P7',
                'start_time' => '14:15:00',
                'end_time' => '15:00:00',
                'order' => 9,
                'is_break' => false
            ],
            [
                'name' => 'P8',
                'start_time' => '15:00:00',
                'end_time' => '15:45:00',
                'order' => 10,
                'is_break' => false
            ],
            [
                'name' => 'P9',
                'start_time' => '15:45:00',
                'end_time' => '16:30:00',
                'order' => 11,
                'is_break' => false
            ]
        ];

        foreach ($periods as $period) {
            Period::create($period);
        }
    }
}