<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Matières - {{ $className }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .container {
            width: 100%;
            max-width: 19cm;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1.5px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table td {
            font-size: 11px;
        }

        .subject-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Statistiques par Matières</h2>
        <p style="text-align: center; font-size: 14px; font-weight: bold;">
            Classe: {{ $className }} | Année Scolaire: {{ $yearName }} | Type de Trimestre: {{ $termTypeName }} | Titulaire : <strong>{{ $principalTeacherName }}</strong>
        </p>
        

        @foreach($subjects as $subject)

            @php
                // Filter all students for the current subject
                $studentsInSubject = collect($studentData)->map(function ($student) use ($subject) {
                    $subjectData = collect($student['subjects'])->firstWhere('subject_id', $subject->subject_id);
                    if ($subjectData) {

                        // Ensure average_marks is numeric, otherwise set to null
                        $averageMarks = is_numeric($subjectData['average_marks']) ? 
                       (float)$subjectData['average_marks'] : 
                       null;

                        return [
                            'gender' => $student['gender'],
                            'average_marks' => $subjectData['average_marks'],
                        ];
                    }
                    return null;
                })->filter(function($item) {
                    // Remove null entries AND entries with null average_marks
                    return $item !== null && $item['average_marks'] !== null;
                }); // Remove null entries if the subject does not exist for a student

                // Exclude students with "abandon" (average marks between 0 and 3)
                $remainingStudents = $studentsInSubject->reject(fn($student) => $student['average_marks'] >= 0 && $student['average_marks'] < 3);

                // Separate male and female students
                $maleStudents = $remainingStudents
                    ->where('gender', 'Masculin')
                    ->filter(function($student) {
                        return is_numeric($student['average_marks']);
                    });

                $femaleStudents = $remainingStudents
                    ->where('gender', 'Féminin')
                    ->filter(function($student) {
                        return is_numeric($student['average_marks']);
                    });

                // Calculate statistics
                $subjectStats = [
                    'male' => [
                        'count' => $maleStudents->count(),
                        'highest_mean' => $maleStudents->where('average_marks', '!==', null)->max('average_marks') ?? 0,
                        'lowest_mean' => $maleStudents->where('average_marks', '!==', null)->min('average_marks') ?? 0,
                        'mean' => $maleStudents->where('average_marks', '!==', null)->avg('average_marks') ?? 0,
                        'success_percentage' => $maleStudents->where('average_marks', '>=', 10)->count() / max(1, $maleStudents->count()) * 100,
                    ],
                    'female' => [
                        'count' => $femaleStudents->count(),
                        'highest_mean' => $femaleStudents->where('average_marks', '!==', null)->max('average_marks') ?? 0,
                        'lowest_mean' => $femaleStudents->where('average_marks', '!==', null)->min('average_marks') ?? 0,
                        'mean' => $femaleStudents->where('average_marks', '!==', null)->avg('average_marks') ?? 0,
                        'success_percentage' => $femaleStudents->where('average_marks', '>=', 10)->count() / max(1, $femaleStudents->count()) * 100,
                    ],
                ];

                // Calculate overall stats for the subject
                $overallStats = [
                    'count' => $subjectStats['male']['count'] + $subjectStats['female']['count'],
                    'highest_mean' => max($subjectStats['male']['highest_mean'], $subjectStats['female']['highest_mean']),
                    'lowest_mean' => min($subjectStats['male']['lowest_mean'], $subjectStats['female']['lowest_mean']),
                    'mean' => ($subjectStats['male']['mean'] + $subjectStats['female']['mean']) / 2,
                    'success_percentage' => ($subjectStats['male']['success_percentage'] + $subjectStats['female']['success_percentage']) / 2,
                ];

                
                 // Convert all average marks for students in this subject into a collection
                 // Get only numeric marks
                $numericMarks = $studentsInSubject->pluck('average_marks')->filter(function($mark) {
                    return is_numeric($mark);
                });

                // Compute statistical metrics only if we have data
                if ($numericMarks->isNotEmpty()) {
                    $mode = collect($numericMarks->mode());
                    $median = $numericMarks->median();
                    $mean = $numericMarks->avg();
                    
                    $variance = $numericMarks->map(function ($mark) use ($mean) {
                        return pow($mark - $mean, 2);
                    })->avg();
                    
                    $standardDeviation = sqrt($variance);
                } else {
                    $mode = collect();
                    $median = 0;
                    $variance = 0;
                    $standardDeviation = 0;
                }

                // Define range intervals and calculate counts for each range
                $ranges = collect([
                    [0, 3], [3, 4], [4, 5], [5, 6], [6, 7], [7, 8], [8, 9], [9, 10],
                    [10, 11], [11, 12], [12, 13], [13, 14], [14, 15], [15, 17], [17, 18], [18, 19], [19, 20],
                ]);

                $rangeCounts = $ranges->mapWithKeys(function ($range) use ($studentsInSubject) {
                    [$min, $max] = $range;
                    $rangeKey = "{$min}-{$max}";
                    $maleCount = $studentsInSubject
                        ->where('gender', 'Masculin')
                        ->filter(function($student) use ($min, $max) {
                        return is_numeric($student['average_marks']) && 
                            $student['average_marks'] >= $min && 
                            $student['average_marks'] < $max;
                        })
                        ->count();

                    $femaleCount = $studentsInSubject
                        ->where('gender', 'Féminin')
                        ->filter(function($student) use ($min, $max) {
                        return is_numeric($student['average_marks']) && 
                            $student['average_marks'] >= $min && 
                            $student['average_marks'] < $max;
                        })
                        ->count();

                    return [$rangeKey => ['male' => $maleCount, 'female' => $femaleCount]];
                });

            @endphp

            <!-- Display statistics for the subject -->
            <div class="subject-title">{{ $subject->school_subject->name }}</div>
            <table>
    <thead>
        <tr>
            <th>Genre</th>
            <th>Effectifs</th>
            <th>Moyenne la Plus Haute</th>
            <th>Moyenne la Plus Basse</th>
            <th>Moyenne Générale</th>
            <th>% de Réussite (>= 10)</th>
            <th>Le Mode</th>
            <th>La Variance</th>
            <th>L'Écart Type</th>
            <th>La Médiane</th>
        </tr>
    </thead>
    <tbody>
        @foreach(['male' => 'Garçons', 'female' => 'Filles'] as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $subjectStats[$key]['count'] }}</td>
                <td>{{ number_format($subjectStats[$key]['highest_mean'], 2) }}</td>
                <td>{{ number_format($subjectStats[$key]['lowest_mean'], 2) }}</td>
                <td>{{ number_format($subjectStats[$key]['mean'], 2) }}</td>
                <td>{{ number_format($subjectStats[$key]['success_percentage'], 2) }}%</td>
                <td>
                    @if($mode->isNotEmpty())
                        {{ $mode->join(', ') }} <!-- Display all modes separated by a comma -->
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ number_format($variance, 2) }}</td>
                <td>{{ number_format($standardDeviation, 2) }}</td>
                <td>{{ number_format($median, 2) }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td>TOTAL</td>
            <td>{{ $overallStats['count'] }}</td>
            <td>{{ number_format($overallStats['highest_mean'], 2) }}</td>
            <td>{{ number_format($overallStats['lowest_mean'], 2) }}</td>
            <td>{{ number_format($overallStats['mean'], 2) }}</td>
            <td>{{ number_format($overallStats['success_percentage'], 2) }}%</td>
            <td>
                @if($mode->isNotEmpty())
                    {{ $mode->join(', ') }}
                @else
                    N/A
                @endif
            </td>
            <td>{{ number_format($variance, 2) }}</td>
            <td>{{ number_format($standardDeviation, 2) }}</td>
            <td>{{ number_format($median, 2) }}</td>
        </tr>
    </tbody>
</table>

        <!-- Présentation des Résultats par Note Obtenue -->
<!-- Présentation des Résultats par Note Obtenue -->
<h3>Présentation des Résultats par Note Obtenue</h3>
<table>
    <thead>
        <tr>
            <th>Intervalle de Notes</th>
            <th>Garçons (Nombre)</th>
            <th>Filles (Nombre)</th>
            <th>TOTAL (Nombre)</th>
            <th>Note Min</th>
            <th>Note Max</th>
            <th>Moyenne Générale</th>
            <th>% Garçons</th>
            <th>% Filles</th>
            <th>% TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Define the ranges and calculate counts for each range
            $rangesWithStats = collect([
                [0, 6], [6, 10], [10, 15], [15, 20],
            ])->mapWithKeys(function ($range) use ($studentsInSubject, $maleStudents, $femaleStudents) {
                [$min, $max] = $range;
                $rangeKey = "{$min}=<N<{$max}";

                $maleCount = $maleStudents->whereBetween('average_marks', [$min, $max])->count();
                $femaleCount = $femaleStudents->whereBetween('average_marks', [$min, $max])->count();
                $totalCount = $maleCount + $femaleCount;

                // Calculate percentages
                $totalStudents = max(1, $studentsInSubject->count()); // Avoid division by zero
                $malePercentage = $maleCount / $totalStudents * 100;
                $femalePercentage = $femaleCount / $totalStudents * 100;
                $totalPercentage = $totalCount / $totalStudents * 100;

                return [
                    $rangeKey => [
                        'male' => $maleCount,
                        'female' => $femaleCount,
                        'total' => $totalCount,
                        'male_percentage' => $malePercentage,
                        'female_percentage' => $femalePercentage,
                        'total_percentage' => $totalPercentage,
                    ],
                ];
            });

            // Calculate overall statistics for the subject
            $overallStats = [
                 'male_count' => $maleStudents->count(),
                'female_count' => $femaleStudents->count(),
                'total_count' => $maleStudents->count() + $femaleStudents->count(),
                'min' => $studentsInSubject->pluck('average_marks')->filter()->min(), // Lowest mark in the subject
                'max' => $studentsInSubject->pluck('average_marks')->filter()->max(), // Highest mark in the subject
                'mean' => $studentsInSubject->pluck('average_marks')->filter()->avg(), // Mean mark in the subject
            ]; 

            // Collect all exam marks for this subject across all students
$allMarks = collect($studentData)->flatMap(function ($student) use ($subject) {
    $subjectData = collect($student['subjects'])->firstWhere('subject_id', $subject->subject_id);
    return $subjectData ? $subjectData['exam_marks'] : []; // Flatten all marks for the subject
})->filter(); // Remove null or empty values

// Calculate global statistics for the subject
$subjectStats = [
    'male_count' => $maleStudents->count(),
    'female_count' => $femaleStudents->count(),
    'total_count' => $maleStudents->count() + $femaleStudents->count(),
    'min' => $allMarks->min(), // The actual lowest mark in the subject
    'max' => $allMarks->max(), // The actual highest mark in the subject
    'mean' => $allMarks->avg(), // The mean mark across all students
];

        @endphp

        <!-- Display statistics for each range -->
        @foreach($rangesWithStats as $range => $stats)
            <tr>
                <td>{{ $range }}</td>
                <td>{{ $stats['male'] }}</td>
                <td>{{ $stats['female'] }}</td>
                <td>{{ $stats['total'] }}</td>
                <td>{{ number_format($subjectStats['min'], 2) }}</td> <!-- Note Min -->
                <td>{{ number_format($subjectStats['max'], 2) }}</td> <!-- Note Max -->
                <td>{{ number_format($subjectStats['mean'], 2) }}</td> <!-- Moyenne Générale -->
                <td>{{ number_format($stats['male_percentage'], 2) }}%</td>
                <td>{{ number_format($stats['female_percentage'], 2) }}%</td>
                <td>{{ number_format($stats['total_percentage'], 2) }}%</td>
            </tr>
        @endforeach

        <!-- Summary row for the entire subject -->
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td>TOTAL</td>
            <td>{{ $subjectStats['male_count'] }}</td>
            <td>{{ $subjectStats['female_count'] }}</td>
            <td>{{ $subjectStats['total_count'] }}</td>
            <td>{{ number_format($subjectStats['min'], 2) }}</td> <!-- Note Min -->
            <td>{{ number_format($subjectStats['max'], 2) }}</td> <!-- Note Max -->
            <td>{{ number_format($subjectStats['mean'], 2) }}</td> <!-- Moyenne Générale -->
            <td>100%</td>
            <td>100%</td>
            <td>100%</td>
        </tr>
    </tbody>
</table>


    @endforeach
    </div>
</body>
</html>
