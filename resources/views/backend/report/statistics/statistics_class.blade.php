<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement des Élèves - {{ $className }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

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
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }

        .header p {
            font-size: 12px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 2px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #4CAF50;
            color: white;
            font-size: 12px;
        }

        table td {
            font-size: 12px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #555;
        }

        .statistics {
            margin-top: 20px;
            font-size: 12px;
        }

        .statistics h2 {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .statistics table {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header Section -->
    <div class="header">
    <h1>Classement des Élèves | {{ $termTypeName }}| Année Scolaire : {{ $yearName }}</h1>
        <p><strong>Classe : <strong>{{ $className }}</strong> | Effectif : <strong>{{ $numberOfStudents }}</strong> | Titulaire : <strong>{{ $principalTeacherName }}</strong></p>
        <p>Moyenne Classe: <strong>{{ $classTermMean }}</strong></p>
    </div>

    <!-- Ranked Students Table -->
    <table>
        <thead>
        <tr>
            <th>Rang</th>
            <th>Nom & Prénom(s)</th>
            <th>Sexe | Statut</th>
            <th>Moyenne</th>
        </tr>
        </thead>
        <tbody>
        @php
            // Sort students by term mean in descending order
            $rankedStudents = collect($studentData)->sortByDesc('term_mean')->values();
        @endphp
        @foreach($rankedStudents as $index => $student)
            @php
                $rank = $index + 1;
                $nameParts = explode(' ', $student['student_name'], 2);
                $surname = strtoupper($nameParts[0]);
                $firstName = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';
                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                $rankSuffix = $rank === 1 ? ($gender === 'F' ? 'ère' : 'er') : 'ème';
                $formattedRank = $rank . '<sup>' . $rankSuffix . '</sup>';
            @endphp
            <tr>
                <td><strong>{!! $formattedRank !!}</strong></td>
                <td><strong>{{ $surname }} {{ $firstName }}</strong></td>
                <td><strong>{{ $gender }} | {{ $student['statusclass'] }}</strong></td>
                <td @if($student['term_mean'] >= 10) style="background-color: #d4edda; color: #155724;" @endif><strong>{{ number_format($student['term_mean'], 2) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Statistics Section -->
    <div class="statistics">
        <h2>Effectif de la Classe</h2>

        @php
            // Group data by gender
            $maleStudents = $rankedStudents->where('gender', 'Masculin');
            $femaleStudents = $rankedStudents->where('gender', 'Féminin');

            // Helper functions
            $calculateMean = fn($students) => $students->avg('term_mean');
            $calculatePercentage = fn($students, $threshold) => $students->where('term_mean', '>=', $threshold)->count() / $students->count() * 100;

            // Gender statistics
            $stats = [
                'male' => [
                    'count' => $maleStudents->count(),
                    'highest_mean' => $maleStudents->max('term_mean'),
                    'lowest_mean' => $maleStudents->min('term_mean'),
                    'mean' => $calculateMean($maleStudents),
                    'success_percentage' => $calculatePercentage($maleStudents, 10),
                ],
                'female' => [
                    'count' => $femaleStudents->count(),
                    'highest_mean' => $femaleStudents->max('term_mean'),
                    'lowest_mean' => $femaleStudents->min('term_mean'),
                    'mean' => $calculateMean($femaleStudents),
                    'success_percentage' => $calculatePercentage($femaleStudents, 10),
                ],
            ];

            // Ranges (3 to 4, 4 to 5, etc.)
            $ranges = collect([
                [0, 3], [3, 4], [4, 5], [5, 6], [6, 7], [7, 8], [8, 9], [9, 10],
                [10, 11], [11, 12], [12, 13], [13, 14], [14, 15], [15, 17], [17, 18], [18, 19], [19, 20],
            ]);

            $rangeCounts = $ranges->mapWithKeys(function ($range) use ($maleStudents, $femaleStudents) {
                [$min, $max] = $range;
                return [
                    "{$min}-{$max}" => [
                        'male' => $maleStudents->whereBetween('term_mean', [$min, $max])->count(),
                        'female' => $femaleStudents->whereBetween('term_mean', [$min, $max])->count(),
                    ],
                ];
            });
        @endphp

        <!-- Display Gender Statistics -->
        <table>
            <thead>
            <tr>
                <th>Genre</th>
                <th>Nombre</th>
                <th>TOTAL</th>
                
            </tr>
            </thead>
            <tbody>
            @foreach(['male' => 'Garçons', 'female' => 'Filles'] as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $stats[$key]['count'] }}</td>
                    <td>{{ $numberOfStudents }}</td>
                    
                </tr>
            @endforeach
            </tbody>
        </table>

<!-- Statistics Section -->
    <div class="statistics">
        <h2>Statistiques des Élèves ayant Composés</h2>

        @php
            // Filter out ABANDONS (term mean between 0 and 3)
            $remainingStudents = $rankedStudents->reject(fn($student) => $student['term_mean'] >= 0 && $student['term_mean'] < 3);

            $remainingMaleStudents = $remainingStudents->where('gender', 'Masculin');
            $remainingFemaleStudents = $remainingStudents->where('gender', 'Féminin');

            // Calculate statistics for remaining students
            $remainingStats = [
                'male' => [
                    'count' => $remainingMaleStudents->count(),
                    'highest_mean' => $remainingMaleStudents->max('term_mean'),
                    'lowest_mean' => $remainingMaleStudents->min('term_mean'),
                    'mean' => $remainingMaleStudents->avg('term_mean'),
                    'success_percentage' => $remainingMaleStudents->where('term_mean', '>=', 10)->count() / max(1, $remainingMaleStudents->count()) * 100,
                ],
                'female' => [
                    'count' => $remainingFemaleStudents->count(),
                    'highest_mean' => $remainingFemaleStudents->max('term_mean'),
                    'lowest_mean' => $remainingFemaleStudents->min('term_mean'),
                    'mean' => $remainingFemaleStudents->avg('term_mean'),
                    'success_percentage' => $remainingFemaleStudents->where('term_mean', '>=', 10)->count() / max(1, $remainingFemaleStudents->count()) * 100,
                ],
            ];

            // Calculate the overall mean for the "Remaining Students" table
            $overallMean = [
                'count' => ($remainingStats['male']['count'] + $remainingStats['female']['count']),
                'highest_mean' => ($remainingStats['male']['highest_mean'] + $remainingStats['female']['highest_mean']) / 2,
                'lowest_mean' => ($remainingStats['male']['lowest_mean'] + $remainingStats['female']['lowest_mean']) / 2,
                'mean' => ($remainingStats['male']['mean'] + $remainingStats['female']['mean']) / 2,
                'success_percentage' => ($remainingStats['male']['success_percentage'] + $remainingStats['female']['success_percentage']) / 2,
            ];

             // Filter students who have a term mean >= 10
            $admittedMaleStudents = $remainingMaleStudents->where('term_mean', '>=', 10);
            $admittedFemaleStudents = $remainingFemaleStudents->where('term_mean', '>=', 10);

            // Admitted students statistics
            $admittedStats = [
                'male' => $admittedMaleStudents->count(),
                'female' => $admittedFemaleStudents->count(),
                'total' => $admittedMaleStudents->count() + $admittedFemaleStudents->count(),
            ];
        @endphp

        <!-- Display Remaining Students Statistics -->
        <table>
            <thead>
            <tr>
                <th>Genre</th>
                <th>Effectifs</th>
                <th>Admis (>= 10)</th>
                <th>Moyenne la Plus Haute</th>
                <th>Moyenne la Plus Basse</th>
                <th>Moyenne Générale</th> 
                <th>% de Réussite (>= 10)</th>
            </tr>
            </thead>
            <tbody>
            @foreach(['male' => 'Garçons', 'female' => 'Filles'] as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $remainingStats[$key]['count'] }}</td>
                    <td>{{ $admittedStats[$key] }}</td>
                    <td>{{ number_format($remainingStats[$key]['highest_mean'], 2) }}</td>
                    <td>{{ number_format($remainingStats[$key]['lowest_mean'], 2) }}</td>
                    <td>{{ number_format($remainingStats[$key]['mean'], 2) }}</td>
                    <td>{{ number_format($remainingStats[$key]['success_percentage'], 2) }}%</td>
                </tr>
            @endforeach
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td>TOTAL</td>
                <td>{{ number_format($overallMean['count'], 0) }}</td>
                <td>{{ $admittedStats['total'] }}</td>
                <td>{{ number_format($overallMean['highest_mean'], 2) }}</td>
                <td>{{ number_format($overallMean['lowest_mean'], 2) }}</td>
                <td>{{ number_format($overallMean['mean'], 2) }}</td>
                <td>{{ number_format($overallMean['success_percentage'], 2) }}%</td>
            </tr>
            </tbody>
        </table>
    </div>

        <!-- Display Range Statistics -->
        <h2>Distribution par Tranche</h2>
        <table>
            <thead>
            <tr>
                <th>Tranche</th>
                <th>Garçons</th>
                <th>Filles</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rangeCounts as $range => $counts)
                <tr>
                    <td>@if($range === '0-3')
                <strong>ABANDONS</strong>
            @else
                {{ $range }}
            @endif</td>
                    <td>{{ $counts['male'] }}</td>
                    <td>{{ $counts['female'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p><strong>Classement basé sur la moyenne du trimestre. Produit automatiquement par le système.</strong></p>
        <p>Date : {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
    </div>
</div>
</body>
</html>
