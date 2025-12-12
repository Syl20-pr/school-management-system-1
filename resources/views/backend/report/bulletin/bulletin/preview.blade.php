<!DOCTYPE html>
<!-- resources/views/backend/report/bulletin/preview.blade.php -->
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Bulletin - {{ $className }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .break-after {
                page-break-after: always;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 10.5px;
                margin: 0;
                padding: 0;
                background-color: white !important;
            }
            .bulletin-container {
                width: 100%;
                margin: 0;
                padding: 10px;
                box-shadow: none;
            }
        }
        
        .bulletin-container {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            width: 100%;
            background-color: white;
            margin-bottom: 20px;
        }
        
        .bulletin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .bulletin-table th, .bulletin-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
        }
        
        .bulletin-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        
        .student-row.abandoned {
            background-color: #ffe6e6;
        }
        
        .student-row.success {
            background-color: #e6ffe6;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .summary-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        
        .summary-table td:first-child {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .header-left, .header-right {
            width: 25%;
        }
        
        .header-center {
            width: 50%;
            text-align: center;
        }
        
        .page-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header de navigation (seulement en mode affichage) -->
    <div class="no-print">
        <div class="bg-white shadow-md p-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Aperçu des Bulletins</h1>
                    <p class="text-gray-600">{{ $className }} - {{ $termTypeName }} - {{ $yearName }}</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <i class="fas fa-print mr-2"></i> Imprimer
                    </button>
                    <a href="{{ route('reports.bulletins.download', $filters) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <i class="fas fa-download mr-2"></i> PDF Bulletins
                    </a>
                    <a href="{{ route('reports.bulletins.download-statistics', $filters) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i> PDF Statistiques
                    </a>
                    <a href="{{ route('reports.bulletins.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>
            
            <!-- Informations de statistiques -->
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-blue-50 p-3 rounded">
                    <p class="font-semibold">Effectif</p>
                    <p class="text-xl">{{ $classStatistics['numberOfStudents'] }} élèves</p>
                </div>
                <div class="bg-green-50 p-3 rounded">
                    <p class="font-semibold">Moyenne de classe</p>
                    <p class="text-xl">{{ number_format($classStatistics['classTermMean'], 2) }}/20</p>
                </div>
                <div class="bg-yellow-50 p-3 rounded">
                    <p class="font-semibold">Plus forte moyenne</p>
                    <p class="text-xl">{{ number_format($classStatistics['highestTermMean'], 2) }}/20</p>
                </div>
                <div class="bg-red-50 p-3 rounded">
                    <p class="font-semibold">Plus faible moyenne</p>
                    <p class="text-xl">{{ number_format($classStatistics['lowestTermMean'], 2) }}/20</p>
                </div>
            </div>

            <!-- Pagination (si applicable) -->
            @if(isset($pagination))
            <div class="mt-4 flex justify-center">
                <div class="bg-white p-3 rounded shadow">
                    <p class="text-sm text-gray-600">
                        Page {{ $pagination['current'] }} - 
                        Affichage de {{ ($pagination['current'] - 1) * $pagination['perPage'] + 1 }} 
                        à {{ min($pagination['current'] * $pagination['perPage'], count($studentData)) }} 
                        sur {{ count($studentData) }} étudiants
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Bulletins des étudiants -->
    @foreach($studentData as $index => $student)
    <div class="bulletin-container break-after">
        <!-- En-tête du bulletin -->
        <div class="flex justify-between items-start mb-4">
            <!-- Left Section -->
            <div class="header-left text-xs">
                <p class="font-bold">MINISTERE DE L'ENSEIGNEMENT</p>
                <p>PRIMAIRE ET SECONDAIRE</p>
                <hr class="my-1 border-gray-400">
                <p>DRE - GRAND LOME/IESG-AGOÉ NYIVE</p>
                <p>LYCÉE DE NANEGBE</p>
                <p>Tél: +228 90296872, AGOÉ NYIVE/TOGO</p>
            </div>

            <!-- Center Section -->
            <div class="header-center text-center">
                <p class="font-bold text-lg">L Y N A</p>
                <p class="text-sm"><b>Travail - Discipline - Succès</b></p>
                
                @php
                    $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', '5ème A', '5ème B', '5ème C', '5ème D', '4ème A', '4ème B', '4ème C', '4ème D', '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
                    $isCycle1 = in_array($className, $cycle1Classes);
                @endphp

                <div class="mt-2 font-bold border-2 border-black px-4 py-1 inline-block">
                    @if($isCycle1)
                        @if($termTypeName === 'Trimestre 1')
                            BULLETIN DU PREMIER TRIMESTRE
                        @elseif($termTypeName === 'Trimestre 2')
                            BULLETIN DU DEUXIÈME TRIMESTRE
                        @elseif($termTypeName === 'Trimestre 3')
                            BULLETIN DU TROISIÈME TRIMESTRE
                        @else
                            BULLETIN SCOLAIRE
                        @endif
                    @else
                        @if($termTypeName === 'Semestre 1')
                            BULLETIN DU PREMIER SEMESTRE
                        @elseif($termTypeName === 'Semestre 2')
                            BULLETIN DU DEUXIÈME SEMESTRE
                        @else
                            BULLETIN SCOLAIRE
                        @endif
                    @endif
                    <br>Classe: {{ $className }}
                </div>
            </div>

            <!-- Right Section -->
            <div class="header-right text-xs text-right">
                <p class="font-bold">RÉPUBLIQUE TOGOLAISE</p>
                <p>Travail - Liberté - Patrie</p>
                <hr class="my-1 border-gray-400">
                <div class="border border-black px-2 py-1 inline-block">
                    <p class="m-0 font-bold">A/S: {{ $yearName }}</p>
                </div> 
            </div>
        </div>
        
        <!-- Informations de l'étudiant -->
        <table class="bulletin-table">
            <tr>
                <td class="text-left font-semibold" style="width: 60%">
                    Nom & Prénom(s): <span class="underline">{{ $student['student_name'] }}</span>
                </td>
                <td class="text-left" style="width: 40%">
                    Sexe: {{ $student['gender'] === 'Féminin' ? 'F' : 'M' }} | 
                    Statut: {{ $student['statusclass'] }} | 
                    Effectif: {{ $classStatistics['numberOfStudents'] }}
                </td>
            </tr>
        </table>
        
        <!-- Tableau des matières scientifiques -->
        <p class="text-center font-semibold my-2 bg-gray-200 py-1">MATIÈRES SCIENTIFIQUES</p>
        <table class="bulletin-table">
            <thead>
                <tr>
                    <th style="width: 15%">Matières</th>
                    <th style="width: 8%">Interro.</th>
                    <th style="width: 8%">Devoir</th>
                    <th style="width: 8%">Compo.</th>
                    <th style="width: 6%">Coef.</th>
                    <th style="width: 8%">Moy.</th>
                    <th style="width: 10%">Coef. x Moy.</th>
                    <th style="width: 8%">Rang</th>
                    <th style="width: 12%">Mention</th>
                    <th style="width: 10%">Prof.</th>
                    <th style="width: 7%">Sign.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $scientificSubjects = ['Mathématiques', 'Phys. Chimie Tech.', 'S.V.T', 'Phys. Chimie'];
                    $scientificSubjectsData = [];
                    foreach ($student['subjects'] as $subject) {
                        if (in_array($subject['subject_name'], $scientificSubjects)) {
                            $scientificSubjectsData[] = $subject;
                        }
                    }
                @endphp
                
                @foreach($scientificSubjectsData as $subject)
                    <tr class="{{ $subject['is_inapte'] ? 'bg-gray-100' : '' }}">
                        <td class="text-left">{{ $subject['subject_name'] }}</td>
                        <td>{{ $subject['exam_marks'][0] ?? '-' }}</td>
                        <td>{{ $subject['exam_marks'][1] ?? '-' }}</td>
                        <td>{{ $subject['exam_marks'][2] ?? '-' }}</td>
                        <td>{{ $subject['is_inapte'] ? '--' : $subject['subjective_mark_coefficient'] }}</td>
                        <td class="{{ is_numeric($subject['average_marks']) && $subject['average_marks'] >= 10 ? 'text-green-600 font-bold' : '' }}">
                            {{ $subject['is_inapte'] ? 'Disp.' : (is_numeric($subject['average_marks']) ? number_format($subject['average_marks'], 1) : $subject['average_marks']) }}
                        </td>
                        <td>{{ $subject['is_inapte'] ? '--' : (is_numeric($subject['weighted_average']) ? number_format($subject['weighted_average'], 1) : $subject['weighted_average']) }}</td>
                        <td>
                            @if($subject['is_inapte'])
                                --
                            @elseif($student['is_abandon'])
                                N/A
                            @else
                                {{ $subject['rank'] ?? '-' }}
                            @endif
                        </td>
                        <td class="text-xs">{{ $subject['appreciation'] }}</td>
                        <td class="text-left text-xs">{{ \Illuminate\Support\Str::limit($subject['assigned_teacher'], 15) }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Tableau des matières littéraires -->
        <p class="text-center font-semibold my-2 bg-gray-200 py-1">MATIÈRES LITTÉRAIRES & SPÉCIFIQUES</p>
        <table class="bulletin-table">
            <thead>
                <tr>
                    <th style="width: 15%">Matières</th>
                    <th style="width: 8%">Interro.</th>
                    <th style="width: 8%">Devoir</th>
                    <th style="width: 8%">Compo.</th>
                    <th style="width: 6%">Coef.</th>
                    <th style="width: 8%">Moy.</th>
                    <th style="width: 10%">Coef. x Moy.</th>
                    <th style="width: 8%">Rang</th>
                    <th style="width: 12%">Mention</th>
                    <th style="width: 10%">Prof.</th>
                    <th style="width: 7%">Sign.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $otherSubjectsData = [];
                    foreach ($student['subjects'] as $subject) {
                        if (!in_array($subject['subject_name'], $scientificSubjects)) {
                            $otherSubjectsData[] = $subject;
                        }
                    }
                @endphp
                
                @foreach($otherSubjectsData as $subject)
                    <tr class="{{ $subject['is_inapte'] ? 'bg-gray-100' : '' }}">
                        <td class="text-left">{{ $subject['subject_name'] }}</td>
                        <td>{{ $subject['exam_marks'][0] ?? '-' }}</td>
                        <td>{{ $subject['exam_marks'][1] ?? '-' }}</td>
                        <td>{{ $subject['exam_marks'][2] ?? '-' }}</td>
                        <td>{{ $subject['is_inapte'] ? '--' : $subject['subjective_mark_coefficient'] }}</td>
                        <td class="{{ is_numeric($subject['average_marks']) && $subject['average_marks'] >= 10 ? 'text-green-600 font-bold' : '' }}">
                            {{ $subject['is_inapte'] ? 'Disp.' : (is_numeric($subject['average_marks']) ? number_format($subject['average_marks'], 1) : $subject['average_marks']) }}
                        </td>
                        <td>{{ $subject['is_inapte'] ? '--' : (is_numeric($subject['weighted_average']) ? number_format($subject['weighted_average'], 1) : $subject['weighted_average']) }}</td>
                        <td>
                            @if($subject['is_inapte'])
                                --
                            @elseif($student['is_abandon'])
                                N/A
                            @else
                                {{ $subject['rank'] ?? '-' }}
                            @endif
                        </td>
                        <td class="text-xs">{{ $subject['appreciation'] }}</td>
                        <td class="text-left text-xs">{{ \Illuminate\Support\Str::limit($subject['assigned_teacher'], 15) }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Résumé et statistiques -->
        <table class="summary-table">
            <tr>
                <td style="width: 25%">Moyenne: {{ number_format($student['term_mean'], 2) }}</td>
                <td style="width: 25%">
                    @if($student['is_abandon'])
                        Rang: N/A
                    @else
                        Rang: {{ $student['term_rank'] }}<sup>e</sup>
                    @endif
                </td>
                <td style="width: 25%">Tableau d'Honneur: __</td>
                <td style="width: 25%">Taux de réussite: {{ number_format($classStatistics['percentageAbove10'], 1) }}%</td>
            </tr>
            <tr>
                <td>En Lettres: {{ $student['term_mean_words'] }}</td>
                <td>Moy. Classe: {{ number_format($classStatistics['classTermMean'], 2) }}</td>
                <td>Félicitations: __</td>
                <td>Titulaire: {{ $principalTeacherName }}</td>
            </tr>
            <tr>
                <td>+ Forte Moy.: {{ number_format($classStatistics['highestTermMean'], 2) }}</td>
                <td>+ Faible Moy.: {{ number_format($classStatistics['lowestTermMean'], 2) }}</td>
                <td>Encouragements: __</td>
                <td>Signature: _____</td>
            </tr>
        </table>
        
        <!-- Appréciation -->
        <div class="text-center mt-4 border-2 border-black p-3">
            <h4 class="font-bold mb-1">Appréciation du Travail:</h4>
            <p class="text-lg">{{ $student['appreciation_term'] }}</p>
        </div>
        
        <!-- Signature et date -->
        <div class="text-right mt-6 text-xs">
            <p>Fait à Agoè Nyivé le, {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
            <p class="font-bold mt-4">Le Proviseur</p>
            <p class="font-bold">SODJA Kalaha</p>
        </div>

        <!-- Info de pagination en mode impression -->
        <div class="page-info no-print">
            Page {{ $index + 1 }} sur {{ count($studentData) }}
        </div>
    </div>
    @endforeach

    <!-- Bouton de retour en haut (seulement en mode affichage) -->
    <div class="no-print fixed bottom-4 right-4">
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 transition">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>

    <script>
        // Masquer les infos de page lors du défilement
        let pageInfo = document.querySelector('.page-info');
        if (pageInfo) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    pageInfo.style.opacity = '0.6';
                } else {
                    pageInfo.style.opacity = '1';
                }
            });
        }
    </script>
</body>
</html>