<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistique Annuelle des Élèves - {{ $className }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
    <h1>STATISTIQUES ANNUELLES - {{ strtoupper($className) }} | Année Scolaire : {{ $yearName }}</h1>
        <p>
            Année Scolaire: <strong>{{ $yearName }}</strong> | 
            Période: <strong>{{ $termTypeName }}</strong> | 
            Titulaire: <strong>{{ $principalTeacherName }}</strong>
        </p>
        <p>
            Effectif: <strong>{{ $numberOfStudents }}</strong> | 
            Moy. Classe: <strong>{{ $classAnnualMean }}</strong> | 
            Plus Forte: <strong>{{ $highestAnnualMean }}</strong> | 
            Plus Faible: <strong>{{ $lowestAnnualMean }}</strong> | 
            Taux Réussite: <strong>{{ $annualPercentageAbove10 }}%</strong>
        </p>
    </div>

    <!-- Ranked Students Table -->
    <!-- Student Ranking Table -->
    <table>
    <thead>
        <tr>
            <th width="5%">Rang</th>
            <th width="20%">Élève</th>
            <th width="8%">Sexe</th>
            <th width="8%">Statut</th>
            @if (($termTypeName == 'Trimestre 3') )
                <th width="15%">Trimestre 1</th>
                <th width="15%">Trimestre 2</th>
                <th width="15%">Trimestre 3</th>
            @elseif(($termTypeName == 'Semestre 2'))
                <th width="15%">Semestre 1</th>
                <th width="15%">Semestre 2</th>
            @endif
            <th width="10%">Moy. Ann.</th>
        </tr>
    </thead>
    <tbody>
    {{-- @php
        $rankedStudents = collect($studentData)
            ->sortByDesc(function ($student) {
                // Sort by annual mean (numeric values first, then N/A)
                return is_numeric($student['annual_mean']['mean'] ?? null) 
                    ? $student['annual_mean']['mean'] 
                    : -INF;
            })
            ->values()
            ->map(function ($student, $index) {
                $student['rank'] = $index + 1;
                // Update the annual_mean rank if it exists
                if (isset($student['annual_mean']) && is_array($student['annual_mean'])) {
                    $student['annual_mean']['rank'] = $index + 1;
                }
                return $student;
            });
    @endphp --}}
@php
    $withMean = [];
    $withoutMean = [];

    // Séparer étudiants avec et sans moyenne
    foreach ($studentData as $student) {
        if (isset($student['annual_mean']['mean']) && is_numeric($student['annual_mean']['mean'])) {
            $student['annual_mean']['mean'] = floatval($student['annual_mean']['mean']); // 🔑 Forcer float
            $withMean[] = $student;
        } else {
            $withoutMean[] = $student;
        }
    }

    // Trier par moyenne décroissante
    usort($withMean, function($a, $b) {
        return $b['annual_mean']['mean'] <=> $a['annual_mean']['mean'];
    });

    // Classement avec ex-aequo
    $rankedStudents = [];
    $currentRank = 1;
    $previousScore = null;
    //$studentsAtSameRank = 0;
    $sameRankCount = 0;

    foreach ($withMean as $student) {
        $currentScore = $student['annual_mean']['mean'];

        if ($previousScore !== null && abs($currentScore - $previousScore) <= 0.001) {
            // Même moyenne → même rang
            //$student['rank'] = $currentRank;
            //$student['annual_mean']['rank'] = $currentRank;
            //$studentsAtSameRank++;
            //$currentRank += $sameRankCount;
            //$sameRankCount = 1;
            // Même moyenne → même rang, incrémenter le compteur
            $sameRankCount++;
        } else {
            // Nouvelle moyenne → on avance le rang en tenant compte des ex-aequo précédents
            //$currentRank = $currentRank + $studentsAtSameRank;
            //$student['rank'] = $currentRank;
            //$student['annual_mean']['rank'] = $currentRank;
            //$studentsAtSameRank = 1;
            //$sameRankCount++;
            // Nouvelle moyenne → avancer le rang
            $currentRank += $sameRankCount;
            $sameRankCount = 1;
        }

        $student['rank'] = $currentRank;
        $student['annual_mean']['rank'] = $currentRank;
        $student['is_ex_aequo'] = ($sameRankCount > 1); // 🔥 Marquer les ex-æquo

        $rankedStudents[] = $student;
        $previousScore = $currentScore;
    }

    // Ajouter les étudiants sans moyenne
    foreach ($withoutMean as $student) {
        $student['rank'] = 'N/A';
        $student['annual_mean']['rank'] = 'N/A';
        $student['is_ex_aequo'] = false;
        $rankedStudents[] = $student;
    }

    // Collection pour les where(), filter(), etc.
    $rankedStudents = collect($rankedStudents);
@endphp


{{-- @php
    // Solution simple sans conversion de type
    $rankedStudents = [];
    
    // Séparer les étudiants avec et sans moyenne valide
    $withMean = [];
    $withoutMean = [];
    
    foreach ($studentData as $student) {
        if (isset($student['annual_mean']['mean']) && is_numeric($student['annual_mean']['mean'])) {
            $withMean[] = $student;
        } else {
            $withoutMean[] = $student;
        }
    }
    
    // Trier par moyenne décroissante
    usort($withMean, function($a, $b) {
        return $b['annual_mean']['mean'] <=> $a['annual_mean']['mean'];
    });
    
    // Calcul des rangs avec ex-aequo
    $currentRank = 1;
    $previousScore = null;
    $countSameRank = 1;
    
    foreach ($withMean as $index => $student) {
        $currentScore = $student['annual_mean']['mean'];
        
        if ($previousScore !== null && abs($currentScore - $previousScore) > 0.001) {
            $currentRank += $countSameRank;
            $countSameRank = 1;
        }
        
        $student['annual_mean']['rank'] = $currentRank;
        $student['rank'] = $currentRank;
        $rankedStudents[] = $student;
        
        $previousScore = $currentScore;
        $countSameRank++;
    }
    
    // Ajouter les étudiants sans moyenne
    foreach ($withoutMean as $student) {
        $student['annual_mean']['rank'] = 'N/A';
        $student['rank'] = 'N/A';
        $rankedStudents[] = $student;
    }
@endphp --}}
    
    @foreach($rankedStudents as $student)
        @php
            $nameParts = explode(' ', $student['student_name'], 2);
            $surname = strtoupper($nameParts[0]);
            $firstName = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';
            $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
            
            $rowClass = '';
            if ($student['is_abandon']) {
                $rowClass = 'abandon';
            } elseif (isset($student['annual_mean']['mean']) && $student['annual_mean']['mean'] >= 10) {
                $rowClass = 'success';
            }
            
            $annualMean = $student['annual_mean']['mean'] ?? 'N/A';
            $annualRank = $student['annual_mean']['rank'] ?? 'N/A';
        @endphp
        <tr class="{{ $rowClass }}">
            <td>
                @if($annualRank === 'N/A')
                    N/A
                @else
                    @php
                        $rankSuffix = ($annualRank == 1)
                            ? ($gender === 'F' ? 'ère' : 'er')
                            : 'ème';
                    @endphp
                    {{ $annualRank }}<sup>{{ $rankSuffix }}</sup>
                    {{-- 🔥 AJOUT de l'affichage ex-æquo --}}
                    @if(!empty($student['is_ex_aequo']) && $student['is_ex_aequo'])
                        <span style="font-size:7px; color:#555;">ex</span>
                    @endif

                @endif
            </td>
            <td class="text-bold">{{ $surname }} {{ $firstName }}</td>
            <td>{{ $gender }}</td>
            <td>{{ $student['statusclass'] }}</td>
            
            @if($termTypeName == 'Trimestre 3')
                @foreach(['Trimestre 1', 'Trimestre 2'] as $term)
                    <td>
                        @if(isset($student['previous_terms'][$term]) && is_numeric($student['previous_terms'][$term]['mean']))
                            {{ number_format((float)$student['previous_terms'][$term]['mean'], 2) }} 
                            ({{ $student['previous_terms'][$term]['rank'] }}<sup>ème</sup>)
                        @else
                            N/A
                        @endif
                    </td>
                @endforeach
                <td>
                    {{ number_format($student['term_mean'], 2) }}(
                    @if ($student['is_abandon'])
                        N/A
                    @else
                        @php
                            $Trank = $student['term_rank'];
                            $rankSuffix = ($Trank == 1)
                                ? ($gender === 'F' ? 'ère' : 'er')
                                : 'ème';
                        @endphp
                        {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                    @endif
                    )
                </td>
            @elseif($termTypeName == 'Semestre 2')
                @foreach(['Semestre 1'] as $term)
                    <td>
                        @if(isset($student['previous_terms'][$term]) && is_numeric($student['previous_terms'][$term]['mean']))
                            {{ number_format((float)$student['previous_terms'][$term]['mean'], 2) }} 
                            ({{ $student['previous_terms'][$term]['rank'] }}<sup>ème</sup>)
                        @else
                            N/A
                        @endif
                    </td>
                @endforeach
                <td>
                    {{ number_format($student['term_mean'], 2) }}(
                    @if ($student['is_abandon'])
                        N/A
                    @else
                        @php
                            $Trank = $student['term_rank'];
                            $rankSuffix = ($Trank == 1)
                                ? ($gender === 'F' ? 'ère' : 'er')
                                : 'ème';
                        @endphp
                        {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                    @endif
                    )
                </td>
            @endif            
            <td class="text-bold">
                @if(is_numeric($annualMean))
                    {{ number_format($annualMean, 2) }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

    <!-- Statistics Section -->
    <!-- Statistics Section -->
    <h2>STATISTIQUES PAR GENRE</h2>
    
    <!-- /////////////////////////////////////////////SUMMARY TABLE///////////////////////////////////// -->
    @php
    // Calculate gender statistics with proper numeric checks
    $validStudents = collect($studentData)->filter(function($student) {
        return isset($student['annual_mean']['mean']) && is_numeric($student['annual_mean']['mean']);
    });

    $maleStudents = $validStudents->where('gender', 'Masculin');
    $femaleStudents = $validStudents->where('gender', 'Féminin');

    $genderStats = [
        'male' => [
            'count' => $maleStudents->count(),
            'admitted' => $maleStudents->where('annual_mean.mean', '>=', 10)->count(),
            'highest' => $maleStudents->max('annual_mean.mean') ?? 'N/A',
            'lowest' => $maleStudents->min('annual_mean.mean') ?? 'N/A',
            'mean' => $maleStudents->avg('annual_mean.mean') ?? 'N/A',
        ],
        'female' => [
            'count' => $femaleStudents->count(),
            'admitted' => $femaleStudents->where('annual_mean.mean', '>=', 10)->count(),
            'highest' => $femaleStudents->max('annual_mean.mean') ?? 'N/A',
            'lowest' => $femaleStudents->min('annual_mean.mean') ?? 'N/A',
            'mean' => $femaleStudents->avg('annual_mean.mean') ?? 'N/A',
        ],
    ];

    // Calculate class totals
    $classTotals = [
        'count' => $validStudents->count(),
        'admitted' => $validStudents->where('annual_mean.mean', '>=', 10)->count(),
        'highest' => $validStudents->max('annual_mean.mean') ?? 'N/A',
        'lowest' => $validStudents->min('annual_mean.mean') ?? 'N/A',
        'mean' => $validStudents->avg('annual_mean.mean') ?? 'N/A',
    ];
@endphp

<table>
    <thead>
        <tr>
            <th>Genre</th>
            <th>Effectif</th>
            <th>Admis (>=10)</th>
            <th>% Réussite</th>
            <th>Moy. Max</th>
            <th>Moy. Min</th>
            <th>Moy. Générale</th>
        </tr>
    </thead>
    <tbody>
        @foreach(['male' => 'Garçons', 'female' => 'Filles'] as $key => $label)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ $genderStats[$key]['count'] }}</td>
            <td>{{ $genderStats[$key]['admitted'] }}</td>
            <td>
                @if($genderStats[$key]['count'] > 0)
                    {{ number_format(($genderStats[$key]['admitted'] / $genderStats[$key]['count']) * 100, 2) }}%
                @else
                    0%
                @endif
            </td>
            <td>
                @if(is_numeric($genderStats[$key]['highest']))
                    {{ number_format($genderStats[$key]['highest'], 2) }}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(is_numeric($genderStats[$key]['lowest']))
                    {{ number_format($genderStats[$key]['lowest'], 2) }}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(is_numeric($genderStats[$key]['mean']))
                    {{ number_format($genderStats[$key]['mean'], 2) }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        @endforeach
        
        <!-- Class Totals Row -->
        <tr style="font-weight: bold; background-color: #f5f5f5;">
            <td>Classe</td>
            <td>{{ $classTotals['count'] }}</td>
            <td>{{ $classTotals['admitted'] }}</td>
            <td>
                @if($classTotals['count'] > 0)
                    {{ number_format(($classTotals['admitted'] / $classTotals['count']) * 100, 2) }}%
                @else
                    0%
                @endif
            </td>
            <td>
                @if(is_numeric($classTotals['highest']))
                    {{ number_format($classTotals['highest'], 2) }}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(is_numeric($classTotals['lowest']))
                    {{ number_format($classTotals['lowest'], 2) }}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(is_numeric($classTotals['mean']))
                    {{ number_format($classTotals['mean'], 2) }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </tbody>
</table>

    @php
    // Get all students (including those with N/A annual means)
    $allStudents = $rankedStudents;

    // Separate male and female students
    $maleStudents = $allStudents->where('gender', 'Masculin');
    $femaleStudents = $allStudents->where('gender', 'Féminin');

    // Define the ranges
    $ranges = [
        'ABANDONS' => null,  // Special case for N/A and 0-3
        '3-5' => [3, 5],
        '5-7' => [5, 7],
        '7-9' => [7, 9],
        '9-10' => [9, 10],
        '10-12' => [10, 12],
        '12-14' => [12, 14],
        '14-16' => [14, 16],
        '16-18' => [16, 18],
        '18-20' => [18, 20]
    ];
    
    $rangeCounts = [];
    $totalCount = 0;
    
    foreach ($ranges as $label => $range) {
        if ($label === 'ABANDONS') {
            // Count students with N/A or mean between 0-3 as ABANDONS
            $maleCount = $maleStudents->filter(function($student) {
                return !isset($student['annual_mean']['mean']) || 
                       $student['annual_mean']['mean'] === 'N/A' ||
                       (is_numeric($student['annual_mean']['mean']) && $student['annual_mean']['mean'] < 3);
            })->count();
            
            $femaleCount = $femaleStudents->filter(function($student) {
                return !isset($student['annual_mean']['mean']) || 
                       $student['annual_mean']['mean'] === 'N/A' ||
                       (is_numeric($student['annual_mean']['mean']) && $student['annual_mean']['mean'] < 3);
            })->count();
        } else {
            // Count students in normal ranges
            [$min, $max] = $range;
            $maleCount = $maleStudents->filter(function($student) use ($min, $max) {
                return isset($student['annual_mean']['mean']) && 
                       is_numeric($student['annual_mean']['mean']) && 
                       $student['annual_mean']['mean'] >= $min && 
                       ($student['annual_mean']['mean'] < $max || ($max === 20 && $student['annual_mean']['mean'] == 20));
            })->count();
            
            $femaleCount = $femaleStudents->filter(function($student) use ($min, $max) {
                return isset($student['annual_mean']['mean']) && 
                       is_numeric($student['annual_mean']['mean']) && 
                       $student['annual_mean']['mean'] >= $min && 
                       ($student['annual_mean']['mean'] < $max || ($max === 20 && $student['annual_mean']['mean'] == 20));
            })->count();
        }
        
        $total = $maleCount + $femaleCount;
        $totalCount += $total;
        
        $rangeCounts[$label] = [
            'male' => $maleCount,
            'female' => $femaleCount,
            'total' => $total,
            'percentage' => $allStudents->count() > 0 ? ($total / $allStudents->count() * 100) : 0
        ];
    }
@endphp

<!-- Range Distribution Table -->
<h2>RÉPARTITION PAR TRANCHES DE MOYENNES ANNUELLES</h2>
<table>
    <thead>
        <tr>
            <th>Tranche</th>
            <th>Garçons</th>
            <th>Filles</th>
            <th>Total</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rangeCounts as $label => $counts)
        <tr style="@if($label === 'ABANDONS') background-color: #ffe6e6; @elseif($label === '10-12' || $label === '12-14' || $label === '14-16' || $label === '16-18' || $label === '18-20') background-color: #e6ffe6; @endif">
            <td>
                @if($label === 'ABANDONS')
                    <strong>ABANDONS (N/A ou 0-3)</strong>
                @else
                    {{ $label }}
                @endif
            </td>
            <td>{{ $counts['male'] }}</td>
            <td>{{ $counts['female'] }}</td>
            <td>{{ $counts['total'] }}</td>
            <td>{{ number_format($counts['percentage'], 1) }}%</td>
        </tr>
        @endforeach
        <tr style="font-weight: bold; background-color: #f5f5f5;">
            <td>TOTAL</td>
            <td>{{ $maleStudents->count() }}</td>
            <td>{{ $femaleStudents->count() }}</td>
            <td>{{ $allStudents->count() }}</td>
            <td>100%</td>
        </tr>
    </tbody>
</table>

<style>
    .abandon {
        background-color: #ffe6e6;  /* Light red for abandons */
    }
    .success {
        background-color: #e6ffe6;  /* Light green for successful ranges */
    }
</style>

    <!-- Footer Section -->
    <div class="footer">
        <p><strong>Généré le {{ now()->locale('fr')->isoFormat('LL') }} - Système de Gestion Scolaire</strong></p>
        <!-- <p>Date : {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</p> -->
    </div>
</div>
</body>
</html>
