<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire - {{ $bulletinData['class']['name'] }}</title>
    <style>
        /* Page Setup */
        @page {
            size: A4;
            margin: 0.3cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            position: relative;
        }

        /* Filigrane texte */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            opacity: 0.1;
        }

        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: bold;
            color: #cccccc;
            text-align: center;
            white-space: nowrap;
        }

        .watermark-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 100px,
                rgba(0,0,0,0.03) 100px,
                rgba(0,0,0,0.03) 200px
            );
        }

        .container {
            width: 100%;
            max-width: 19cm;
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: always;
            position: relative;
            z-index: 1;
        }

        .container:last-child {
            page-break-after: avoid;
        }

        /* Header Styling */
        .header {
            position: relative;
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #000;
        }

        .header img {
            max-width: 80px;
            height: auto;
        }

        .header h4 {
            margin: 5px 0;
            font-size: 11px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .title-box {
            font-weight: bold;
            font-size: 10px;
            padding: 8px;
            border: 1px solid #000;
            background-color: #e0e0e0;
            margin-top: 20px;
            display: inline-block;
        }

        /* Left and Right Additional Header Text */
        .header-left-text,
        .header-right-text {
            position: absolute;
            top: 0;
            font-size: 11px;
            color: #333;
            padding: 10px;
        }

        .header-left-text {
            left: 0;
            text-align: left;
        }

        .header-right-text {
            right: 0;
            text-align: right;
        }

        /* Student Details Table */
        .details-table {
            width: 100%;
            margin-top: 15px;
            border: 1.1px solid #000;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .details-table td {
            padding: 5px;
            border: 1.1px solid #000;
            font-size: 11px;
        }

        .details-table td:nth-child(1) {
            background-color: #f8f8f8;
        }

        /* Marks Table */
        .marks-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
            border: 1.1px solid #000;
        }

        .marks-table th,
        .marks-table td {
            padding: 4px;
            border: 1.1px solid #000;
            text-align: left;
            font-size: 8px;
        }

        .marks-table th {
            background-color: #4CAF50;
            color: white;
        }

        .marks-table td {
            font-weight: bold;
            text-align: center;
        }

        /* Summary Table */
        .summary-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
            border: 1.2px solid #000;
        }

        .summary-table td {
            padding: 8px;
            border: 1.2px solid #000;
            font-size: 10px;
            text-align: center;
        }

        .summary-table td:first-child {
            font-weight: bold;
            text-align: left;
        }

        /* Footer Styling */
        .footer {
            margin-top: 15px;
            font-size: 10px;
            color: #555;
            text-align: center;
        }

        /* Signature Section */
        .signature {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
        }

        .signature hr {
            width: 60%;
            margin: 10px auto;
            border: solid 1px #000;
        }

        .bottom-footer {
            position: fixed;
            bottom: 0.5cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #333;
            background: white;
            z-index: 1000;
            padding: 5px 0;
        }

        /* Styles for rank display */
        .rank-suffix {
            font-size: 7px;
            vertical-align: super;
        }

        h4 {
            position: relative;
            text-align: center;
            margin: 5px 0;
            font-size: 11px;
            text-transform: uppercase;
        }

        .marks-table tr:last-child {
            background-color: #d4edda !important;
            border-top: 2px solid #000;
        }

        .marks-table tr:nth-last-child(2) {
            background-color: #f0f0f0;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>

    <!-- Filigrane texte -->
    {{-- <div class="watermark">
        <div class="watermark-text">LYCEE DE NANEGBE</div>
        <div class="watermark-pattern"></div>
    </div>
 --}}
@foreach($bulletinData['students'] as $student)
<div class="container" style="position: relative;">
    <!-- ✅ Filigrane à l'intérieur du container -->
    <div style="
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-35deg);
        color: rgba(97, 91, 91, 0.25);
        font-size: 80px;
        font-weight: bold;
        white-space: nowrap;
        text-align: center;
        z-index: 0;
    ">
        LYCÉE DE NANEGBE
    </div>

    <!-- ✅ Ton contenu habituel du bulletin ici -->
    <div style="position: relative; z-index: 1;">
    
    <!-- Header Section -->
    <div class="header">
        <!-- Left Text -->
        <div class="header-left-text" style="font-size: 9px; padding: 5px;">
            <p>MINISTERE DE L'EDUCATION</p>
            <p>NATIONAL</p>
            <hr style="margin: 2px 0;">
            <p>DRE - GRAND LOME/IESG-AGOÉ NYIVE</p>
            <p>LYCÉE DE NANEGBE</p>
            <p>Tél:+228 90296872, AGOÉ NYIVE/TOGO</p>
        </div>

        <!-- Centered Content -->
        <img src="{{ public_path() . '/upload/Logo_Nanegbeoo.png' }}" alt="School Logo" style="max-width: 60px;">
        <p><strong>L Y N A</strong></p>
        <p> <b>Travail - Discipline - Succès</b> </p>
        <h4 style="margin: 3px 0; font-size: 10px;">
            @php
                $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                                '5ème A', '5ème B', '5ème C', '5ème D', 
                                '4ème A', '4ème B', '4ème C', '4ème D', 
                                '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
                $isCycle1 = in_array($bulletinData['class']['name'], $cycle1Classes);
            @endphp

            @if($isCycle1)
                @if($bulletinData['termType']['name'] === 'Trimestre 1')
                    <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                    BULLETIN DU PREMIER TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Trimestre 2')
                    <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                    BULLETIN DU DEUXIÈME TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Trimestre 3')
                    <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                    BULLETIN DU TROISIÈME TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @else
                    <u><b>BULLETIN SCOLAIRE</b></u>
                @endif
            @else
                @if($bulletinData['termType']['name'] === 'Semestre 1')
                    <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                    BULLETIN DU PREMIER SEMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Semestre 2')
                    <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                    BULLETIN DU DEUXIÈME SEMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @else
                    <u><b>BULLETIN SCOLAIRE</b></u>
                @endif
            @endif
        </h4>

        <!-- Right Text -->
        <div class="header-right-text" style="font-size: 9px; padding: 5px;">
            <p>RÉPUBLIQUE TOGOLAISE</p>
            <p>Travail - Liberté - Patrie</p>
            <hr>
            <div style="border: 1px solid #000; padding: 10px; display: inline-block; "><h4>A/S: <b>{{ $bulletinData['year']['name'] }}</b></h4></div> 
        </div>
    </div>

    <!-- Student Details Table -->
    <table class="details-table">
        <tr style="font-size: 9px">
            <td>Nom & Prénom(s): <strong style="font-style: italic;  padding: 10px;">{{ $student['student_name'] }}</strong></td>
            <td>Sexe: <strong style="font-style: italic;  padding: 10px;">{{ $student['gender'] === 'Féminin' ? 'F' : 'M' }}</strong> | Statut: <strong style="font-style: italic;  padding: 10px;">{{ $student['statusclass'] }}</strong> | Effectif: <strong style="font-style: italic;  padding: 10px;">{{ $bulletinData['statistics']['number_of_students'] }}</strong></td>
        </tr> 
    </table>

    @php
    // Subjects to include in the first table
    $scientificSubjects = $isCycle1 
        ? ['Mathématiques', 'Phys. Chimie Tech.', 'S.V.T']
        : ['Mathématiques', 'Phys. Chimie', 'S.V.T'];
    
    $specificSubjects = ['E.P.S']; // can extend later if needed (e.g., 'Informatique', 'Art Plastique')

    // Filter Scientific grouped subjects
    $scientificGroup = array_filter($student['subjects'], function($subject) use ($scientificSubjects) {
        return in_array($subject['subject_name'], $scientificSubjects);
    });

    // Filter remaining subjects should be litteracy
    /* $otherSubjects = array_filter($student['subjects'], function($subject) use ($scientificSubjects) {
        return !in_array($subject['subject_name'], $scientificSubjects);
    }); */
    $literaryGroup = array_filter($student['subjects'], function($subject) use ($specificSubjects, $scientificSubjects) {
        return !in_array($subject['subject_name'], $scientificSubjects)
            && !in_array($subject['subject_name'], $specificSubjects);
    });

    // Filter Speficic grouped subjects
    $specificGroup = array_filter($student['subjects'], function($subject) use ($specificSubjects) {
        return in_array($subject['subject_name'], $specificSubjects);
    });
    @endphp

    <p style="text-align: center;">MATIÈRES SCIENTIFIQUES</p>
    
    <!-- Scientific Grouped Subjects Table -->
    <!-- Grouped Subjects Table -->
    <table class="marks-table">
        <thead>
            <tr style="font-size:8px;">
                <th>Matières</th>
                <th>Interro.(a)</th>
                <th>Devoir(b)</th>
                <th>Moy. Clas.[c=(a+b)/2]</th>
                <th>Compo.(d)</th>
                <th>Moy.(c+d)/2</th>
                <th>Coef.</th>
                <th>Coef. x Moy.</th>
                <th>Rang</th>
                <th>Mention</th>
                <th>Nom Prof.</th>
                <th>Sign.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCoefScientifique = 0;
                $totalMoyPondereeScientifique = 0;
            @endphp
            
            @foreach($scientificGroup as $subject)
            @php
                $nameParts = explode(' ', $subject['assigned_teacher'], 2);
                
                if ($subject['assigned_teacher'] === 'de SOUZA Edinho Kossi') {
                    $surname_Teacher = 'de SOUZA';
                    $firstName_Teacher = 'Edinho Kossi';
                } else {
                    $surname_Teacher = strtoupper($nameParts[0]);
                    $firstName_Teacher = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';
                }
                
                // Calcul des totaux
                if (is_numeric($subject['subjective_mark_coefficient'])) {
                    $totalCoefScientifique += $subject['subjective_mark_coefficient'];
                }
                if (is_numeric($subject['weighted_average'])) {
                    $totalMoyPondereeScientifique += $subject['weighted_average'];
                }
            @endphp 
                <tr style="font-size:10px;">
                    <td>{{ $subject['subject_name'] }}</td>
                    <td>
                        {{-- AFFICHAGE NOTE INTERRO --}}
                        @if(!empty($subject['exam_marks']['notes_interros']) && is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if(!empty($subject['exam_marks']['notes_devoirs']) && is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td>
                        @if(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>{{ $subject['subjective_mark_coefficient'] }}</td>
                    <td>
                        @if(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            {{ $subject['weighted_average'] }}
                        @endif
                    </td>
                    <td>
                        @if ($student['is_abandon'])
                            N/A
                        @elseif (is_numeric($subject['rank']))
                        @php
                            $rank = (int) $subject['rank'];
                            $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                            $rankSuffix = ($rank == 1)
                                ? ($gender === 'F' ? 'ère' : 'er')
                                : 'ème';
                        @endphp
                        {{ $rank }}<sup>{{ $rankSuffix }}</sup>
                        @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                            <span style="font-size:7px; color:#555;">ex</span>
                        @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-size:9px;">{{ $subject['appreciation'] }}</td>
                    <td style="font-size:9px;">{{ $surname_Teacher }}</td> 
                    <td></td>
                </tr>
            @endforeach
            
            <!-- Ligne de total pour les matières scientifiques -->
            <tr style="font-size:9px; font-weight: bold; background-color: #f0f0f0;">
                <td colspan="6" style="text-align: right;">TOTAL SCIENTIFIQUE:</td>
                <td>{{ $totalCoefScientifique }}</td>
                <td>
                    @if(is_numeric($totalMoyPondereeScientifique))
                        {{ sprintf('%05.2f', $totalMoyPondereeScientifique) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <!-- Other Subjects = Litteracy Table -->
    <!-- Other Subjects Table -->
    <p style="text-align: center;">MATIÈRES LITTÉRAIRES</p>
    <table class="marks-table">
        <thead>
            <tr style="font-size:8px;">
                <th>Matières</th>
                <th>Interro.(a)</th>
                <th>Devoir(b)</th>
                <th>Moy. Clas.[c=(a+b)/2]</th>
                <th>Compo.(d)</th>
                <th>Moy.(c+d)/2</th>
                <th>Coef.</th>
                <th>Coef. x Moy.</th>
                <th>Rang</th>
                <th>Mention</th>
                <th>Nom Prof.</th>
                <th>Sign.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCoefLitteraire = 0;
                $totalMoyPondereeLitteraire = 0;
            @endphp
            
            @foreach($literaryGroup as $subject)
                @php
                    $nameParts = explode(' ', $subject['assigned_teacher'], 2);
                    $surname_Teacher = strtoupper($nameParts[0]);
                    $firstName_Teacher = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';

                    $isInapte = $subject['average_marks'] === 'Disp.' || 
                                (isset($subject['is_inapte']) && $subject['is_inapte']);
                    
                    // Calcul des totaux (uniquement si pas inapte)
                    if (!$isInapte) {
                        if (is_numeric($subject['subjective_mark_coefficient'])) {
                            $totalCoefLitteraire += $subject['subjective_mark_coefficient'];
                        }
                        if (is_numeric($subject['weighted_average'])) {
                            $totalMoyPondereeLitteraire += $subject['weighted_average'];
                        }
                    }
                @endphp
                <tr style="font-size:9px;">
                    <td>{{ $subject['subject_name'] }}</td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_interros'])&& is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_devoirs'])&& is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @else
                            {{ $subject['subjective_mark_coefficient'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @elseif(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @elseif ($student['is_abandon'])
                            N/A
                        @elseif (is_numeric($subject['rank']))
                            @php
                                $rankSuffix = $subject['rank'] == 1 
                                    ? ($student['gender'] === 'Féminin' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp
                            {{ $subject['rank'] }}<sup>{{ $rankSuffix }}</sup>
                            @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                                <span style="font-size:7px; color:#555;">ex</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Dispensé(e)
                        @else
                            {{ $subject['appreciation'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-size:9px;">{{ $surname_Teacher }}</td>
                    <td></td>
                </tr>
            @endforeach
            
            <!-- Ligne de total pour les matières littéraires -->
            <tr style="font-size:9px; font-weight: bold; background-color: #f0f0f0;">
                <td colspan="6" style="text-align: right;">TOTAL LITTÉRAIRE:</td>
                <td>{{ $totalCoefLitteraire }}</td>
                <td>
                    @if(is_numeric($totalMoyPondereeLitteraire))
                        {{ sprintf('%05.2f', $totalMoyPondereeLitteraire) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="4"></td>
            </tr>
            
            <!-- Ligne de GRAND TOTAL -->
            {{-- @php
                $totalCoefGeneral = $totalCoefScientifique + $totalCoefLitteraire;
                $totalMoyPondereeGeneral = $totalMoyPondereeScientifique + $totalMoyPondereeLitteraire;
            @endphp
            <tr style="font-size:9px; font-weight: bold; background-color: #d4edda;">
                <td colspan="6" style="text-align: right;">GRAND TOTAL:</td>
                <td>{{ $totalCoefGeneral }}</td>
                <td>
                    @if(is_numeric($totalMoyPondereeGeneral))
                        {{ sprintf('%05.2f', $totalMoyPondereeGeneral) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="4"></td>
            </tr> --}}
        </tbody>
    </table>

    <!-- Other Subjects Table -->
    <p style="text-align: center;">MATIÈRES SPÉCIFIQUES</p>
    <table class="marks-table">
        <thead>
            <tr style="font-size:8px;">
                <th>Matières</th>
                <th>Interro.(a)</th>
                <th>Devoir(b)</th>
                <th>Moy. Clas.[c=(a+b)/2]</th>
                <th>Compo.(d)</th>
                <th>Moy.(c+d)/2</th>
                <th>Coef.</th>
                <th>Coef. x Moy.</th>
                <th>Rang</th>
                <th>Mention</th>
                <th>Nom Prof.</th>
                <th>Sign.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCoefSpecific = 0;
                $totalMoyPondereeSpecific = 0;
            @endphp
            
            @foreach($specificGroup as $subject)
                @php
                    $nameParts = explode(' ', $subject['assigned_teacher'], 2);
                    $surname_Teacher = strtoupper($nameParts[0]);
                    $firstName_Teacher = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';

                    $isInapte = $subject['average_marks'] === 'Disp.' || 
                                (isset($subject['is_inapte']) && $subject['is_inapte']);
                    
                    // Calcul des totaux (uniquement si pas inapte)
                    if (!$isInapte) {
                        if (is_numeric($subject['subjective_mark_coefficient'])) {
                            $totalCoefSpecific += $subject['subjective_mark_coefficient'];
                        }
                        if (is_numeric($subject['weighted_average'])) {
                            $totalMoyPondereeSpecific += $subject['weighted_average'];
                        }
                    }
                @endphp
                <tr style="font-size:9px;">
                    <td>{{ $subject['subject_name'] }}</td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_interros'])&& is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_devoirs'])&& is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @else
                            {{ $subject['subjective_mark_coefficient'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @elseif(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            --
                        @elseif ($student['is_abandon'])
                            N/A
                        @elseif (is_numeric($subject['rank']))
                            @php
                                $rankSuffix = $subject['rank'] == 1 
                                    ? ($student['gender'] === 'Féminin' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp
                            {{ $subject['rank'] }}<sup>{{ $rankSuffix }}</sup>
                            @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                                <span style="font-size:7px; color:#555;">ex</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($isInapte)
                            Dispensé(e)
                        @else
                            {{ $subject['appreciation'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-size:9px;">{{ $surname_Teacher }}</td>
                    <td></td>
                </tr>
            @endforeach
            
            <!-- Ligne de total pour les matières littéraires -->
            <tr style="font-size:9px; font-weight: bold; background-color: #f0f0f0;">
                <td colspan="6" style="text-align: right;">TOTAL SPÉCIFIQUE:</td>
                <td>{{ $totalCoefSpecific }}</td>
                <td>
                    @if(is_numeric($totalMoyPondereeSpecific))
                        {{ sprintf('%05.2f', $totalMoyPondereeSpecific) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="4"></td>
            </tr>
            
            <!-- Ligne de GRAND TOTAL -->
            @php
                $totalCoefGeneral = $totalCoefScientifique + $totalCoefLitteraire + $totalCoefSpecific;
                $totalMoyPondereeGeneral = $totalMoyPondereeScientifique + $totalMoyPondereeLitteraire + $totalMoyPondereeSpecific;
            @endphp
            <tr style="font-size:9px; font-weight: bold; background-color: #d4edda;">
                <td colspan="6" style="text-align: right;">GRAND TOTAL:</td>
                <td>{{ $totalCoefGeneral }}</td>
                <td>
                    @if(is_numeric($totalMoyPondereeGeneral))
                        {{ sprintf('%05.2f', $totalMoyPondereeGeneral) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Table Mises a jour de la Table sommaire-->
    {{-- <table class="summary-table">
        <tr>
            @if (($bulletinData['termType']['name'] == 'Trimestre 3') || ($bulletinData['termType']['name'] == 'Semestre 2'))
                <td><strong>Moyenne: <u>{{ number_format($student['term_mean'], 2) }}</u></strong> | <u><strong style="font-style: italic; padding: 5px;">
                        @if ($student['is_abandon'])
                            Rang: N/A
                        @else
                            @php
                                $Trank = $student['term_rank'];
                                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                $rankSuffix = ($Trank == 1)
                                    ? ($gender === 'F' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp

                            Rang: {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                        @endif
                    </strong></u>
                </td>
            @else
                <td><strong>Moyenne: {{ number_format($student['term_mean'], 2) }}</strong> | <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                        @if ($student['is_abandon'])
                            Rang: N/A
                        @else
                            @php
                                $Trank = $student['term_rank'];
                                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                $rankSuffix = ($Trank == 1)
                                    ? ($gender === 'F' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp

                            Rang: {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                        @endif
                    </strong>
                </td>
            @endif
            <td style="background-color:#ddd">
                    @if(!empty($student['previous_terms']))
                        <div>
                            @foreach($student['previous_terms'] as $termName => $termData)
                                <div>
                                    <u><strong>{{ $termName }}:</strong></u>
                                    @if(is_numeric($termData['mean']))
                                        Moy. <strong>{{ number_format($termData['mean'], 2) }}</strong> | 
                                        Rang <strong>@if($termData['rank'] === 'N/A')
                                                        N/A
                                                    @else
                                                        @php
                                                            $rank = $termData['rank'];
                                                            $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                                            $rankSuffix = ($rank == 1)
                                                                ? ($gender === 'F' ? 'ère' : 'er')
                                                                : 'ème';
                                                        @endphp
                                                        {{ $rank }}<sup>{{ $rankSuffix }}</sup>
                                                    @endif</strong>
                                    @else
                                        {{ $termData['mean'] }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
            </td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Tabl. Hon.:__</td>
        </tr>
        <tr>
            @if (($bulletinData['termType']['name'] == 'Trimestre 3') || ($bulletinData['termType']['name'] == 'Semestre 2'))
                <td style="fontsize: 9px;">
                    En Lettres: <strong style="font-style: italic; padding: 5px;"><u>{{ ucwords(strtolower($student['term_mean_words'])) }}</u></strong>
                </td>
            @else
                <td>
                    En Lettres: <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px; font-size: 9px">{{ ucwords(strtolower($student['term_mean_words'])) }}</strong>
                </td>
            @endif
            @if (($bulletinData['termType']['name'] == 'Trimestre 3') || ($bulletinData['termType']['name'] == 'Semestre 2'))
                <td style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                    
                        @if(isset($student['annual_mean']) && $student['annual_mean']['mean'] !== 'N/A')
                            Moy. Ann.: <strong><u>{{ number_format($student['annual_mean']['mean'], 2) }}</u></strong> | 
                            Rang: 
                            <strong><u>
                                @if($student['annual_mean']['rank'] === 'N/A')
                                    N/A
                                @else
                                    @php
                                        $rank = $student['annual_mean']['rank'];
                                        $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                        $rankSuffix = ($rank == 1)
                                            ? ($gender === 'F' ? 'ère' : 'er')
                                            : 'ème';
                                    @endphp
                                    {{ $rank }}<sup>{{ $rankSuffix }}</sup>
                                @endif
                            </u></strong> |  
                            En Lettres: <strong><u>{{ $student['annual_mean']['mean_words'] }}</u></strong>
                        @else
                            Moy. Ann.: <strong><u>N/A</u></strong>
                        @endif
                    
                </td>
            @else
                <td style="font-style: italic; background-color: #ddd; fontsize: 9px;"></td>
            @endif
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Féléc.:__</td>
        </tr>
        <tr>
            <td style="fontsize: 9px;">+Forte Moy.: <strong><u>{{ number_format($bulletinData['statistics']['highest_term_mean'], 2) }}</u></strong> | 
                @if ($student['is_abandon'])
                    +Faible Moy.: <strong><u>N/A</u></strong>
                @else
                +Faible Moy.: <strong><u>{{ number_format($bulletinData['statistics']['lowest_term_mean'], 2) }}</u></strong>
                @endif
            </td>
            @if (($bulletinData['termType']['name'] == 'Trimestre 3') || ($bulletinData['termType']['name'] == 'Semestre 2'))
                <td style="font-style: italic; background-color: #333; color: #fff; padding: 5px;">
                    
                        <u>Moy. Classe</u>: {{ number_format($bulletinData['statistics']['class_term_mean'], 2) }} | <u>+Forte moy.</u>: {{ number_format($bulletinData['statistics']['highest_term_mean'], 2) }}| 
                        <u>+Faible moy.</u>: {{ number_format($bulletinData['statistics']['lowest_term_mean'], 2) }} | <u>Taux ann.</u>: {{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }}% 
                    
                </td>
            @else
                <td style="font-style: italic; background-color: #ddd; fontsize: 9px;"></td>
            @endif
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Encourag.:__</td>
        </tr>
        <tr>
            @if (($bulletinData['termType']['name'] == 'Trimestre 3') || ($bulletinData['termType']['name'] == 'Semestre 2'))
                <td style="fontsize: 9px;">
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                        Taux: <strong style="font-style: italic; padding: 5px;">
                                <u>{{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %</u>
                            </strong>
                </td>
            @else
                <td>
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                    
                        Taux: 
                        <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                            {{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %
                        </strong>
                </td>
            @endif
            <td style="font-size: 10px"><u>Titulaire</u>: <strong>{{ $bulletinData['principalTeacher'] }}</strong> | Signa.: _____ </td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Abscences: __ Hrs</td>
        </tr>
    </table> --}}
    @php
        $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                        '5ème A', '5ème B', '5ème C', '5ème D', 
                        '4ème A', '4ème B', '4ème C', '4ème D', 
                        '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
        
        $isCycle1 = in_array($bulletinData['class']['name'], $cycle1Classes);
        $isFinalTerm = ($isCycle1 && $bulletinData['termType']['name'] == 'Trimestre 3') || 
                    (!$isCycle1 && $bulletinData['termType']['name'] == 'Semestre 2');
    @endphp

    <!-- Summary Table -->
            <style>
                .check-box {
                    display: inline-block;
                    width: 10px;
                    height: 10px;
                    border: 0.8px solid #000;
                    margin-right: 4px;
                }
            </style>
    <table class="summary-table">
        <tr>
            @if ($isFinalTerm)
                <td><strong>Moyenne: <u>{{ number_format($student['term_mean'], 2) }}</u></strong> | <u><strong style="font-style: italic; padding: 5px;">
                        @if ($student['is_abandon'])
                            Rang: N/A
                        @else
                            @php
                                $Trank = $student['term_rank'];
                                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                $rankSuffix = ($Trank == 1)
                                    ? ($gender === 'F' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp
                            Rang: {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                            {{-- 🔥 AJOUT de la gestion ex-æquo --}}
                            @if(!empty($student['term_is_ex_aequo']) && $student['term_is_ex_aequo'])
                                <span style="font-size:7px; color:#555;">ex</span>
                            @endif

                        @endif
                    </strong></u>
                </td>
            @else
                <td><strong>Moyenne: {{ number_format($student['term_mean'], 2) }}</strong> | <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                        @if ($student['is_abandon'])
                            Rang: N/A
                        @else
                            @php
                                $Trank = $student['term_rank'];
                                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                $rankSuffix = ($Trank == 1)
                                    ? ($gender === 'F' ? 'ère' : 'er')
                                    : 'ème';
                            @endphp
                            Rang: {{ $Trank }}<sup>{{ $rankSuffix }}</sup>
                            {{-- 🔥 AJOUT de la gestion ex-æquo --}}
                            @if(!empty($student['term_is_ex_aequo']) && $student['term_is_ex_aequo'])
                                <span style="font-size:7px; color:#fff;">ex</span>
                            @endif

                        @endif
                    </strong>
                </td>
            @endif
            
            <td style="background-color:#ddd">
                @if(!empty($student['previous_terms']))
                    <div>
                        @foreach($student['previous_terms'] as $termName => $termData)
                            <div>
                                <u><strong>{{ $termName }}:</strong></u>
                                @if(is_numeric($termData['mean']))
                                    Moy. <strong>{{ number_format($termData['mean'], 2) }}</strong> | 
                                    Rang <strong>
                                        @if($termData['rank'] === 'N/A')
                                            N/A
                                        @else
                                            @php
                                                $rank = $termData['rank'];
                                                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                                $rankSuffix = ($rank == 1)
                                                    ? ($gender === 'F' ? 'ère' : 'er')
                                                    : 'ème';
                                            @endphp
                                            {{ $rank }}<sup>{{ $rankSuffix }}</sup>
                                            {{-- 🔥 AJOUT pour les termes précédents si vous stockez l'info --}}
                                            @if(!empty($termData['is_ex_aequo']) && $termData['is_ex_aequo'])
                                                <span style="font-size:6px; color:#555;">ex æquo</span>
                                            @endif

                                        @endif
                                    </strong>
                                @else
                                    {{ $termData['mean'] }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Tableau d'honneur:</td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;"> <span class="check-box"></span></td>
        </tr>
        
        <tr>
            @if ($isFinalTerm)
                <td style="fontsize: 9px;">
                    En Lettres: <strong style="font-style: italic; padding: 5px;"><u>{{ ucwords(strtolower($student['term_mean_words'])) }}</u></strong>
                </td>
            @else
                <td>
                    En Lettres: <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px; font-size: 9px">{{ ucwords(strtolower($student['term_mean_words'])) }}</strong>
                </td>
            @endif
            
            @if ($isFinalTerm)
                <td style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                    @if(isset($student['annual_mean']) && $student['annual_mean']['mean'] !== 'N/A')
                        Moy. Ann.: <strong><u>{{ number_format($student['annual_mean']['mean'], 2) }}</u></strong> | 
                        Rang: 
                        <strong><u>
                            @if($student['annual_mean']['rank'] === 'N/A')
                                N/A
                            @else
                                @php
                                    $rank = $student['annual_mean']['rank'];
                                    $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                                    $rankSuffix = ($rank == 1)
                                        ? ($gender === 'F' ? 'ère' : 'er')
                                        : 'ème';
                                @endphp
                                {{ $rank }}<sup>{{ $rankSuffix }}</sup>
                            @endif
                        </u></strong> |  
                        En Lettres: <strong><u>{{ $student['annual_mean']['mean_words'] }}</u></strong>
                    @else
                        Moy. Ann.: <strong><u>N/A</u></strong>
                    @endif
                </td>
            @else
                <td style="font-style: italic; background-color: #ddd; fontsize: 9px;"></td>
            @endif
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Félicitations:</td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;"> <span class="check-box"></span></td>
        </tr>
        
        <tr>
            <td style="fontsize: 9px;">
                +Forte Moy.: <strong><u>{{ number_format($bulletinData['statistics']['highest_term_mean'], 2) }}</u></strong> | 
                @if ($student['is_abandon'])
                    +Faible Moy.: <strong><u>N/A</u></strong>
                @else
                    +Faible Moy.: <strong><u>{{ number_format($bulletinData['statistics']['lowest_term_mean'], 2) }}</u></strong>
                @endif
            </td>
            
            @if ($isFinalTerm)
        <td style="font-style: italic; background-color: #333; color: #fff; padding: 5px;">
            <u>Moy. Classe</u>: 
            @if(is_numeric($bulletinData['statistics']['class_annual_mean']))
                {{ number_format($bulletinData['statistics']['class_annual_mean'], 2) }}
            @else
                {{ $bulletinData['statistics']['class_annual_mean'] }}
            @endif
            | 
            
            <u>+Forte moy.</u>: 
            @if(is_numeric($bulletinData['statistics']['highest_annual_mean']))
                {{ number_format($bulletinData['statistics']['highest_annual_mean'], 2) }}
            @else
                {{ $bulletinData['statistics']['highest_annual_mean'] }}
            @endif
            | 
            
            <u>+Faible moy.</u>: 
            @if(is_numeric($bulletinData['statistics']['lowest_annual_mean']))
                {{ number_format($bulletinData['statistics']['lowest_annual_mean'], 2) }}
            @else
                {{ $bulletinData['statistics']['lowest_annual_mean'] }}
            @endif
            | 
            
            <u>Taux ann.</u>: 
            @if(is_numeric($bulletinData['statistics']['annual_percentage_above_10']))
                {{ number_format($bulletinData['statistics']['annual_percentage_above_10'], 2) }}%
            @else
                {{ $bulletinData['statistics']['annual_percentage_above_10'] }}
            @endif
        </td>
        @else
            <td style="font-style: italic; background-color: #ddd; fontsize: 9px;"></td>
        @endif

            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Encouragements:</td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;"> <span class="check-box"></span></td>
        </tr>
        
        <tr>
            @if ($isFinalTerm)
                <td style="fontsize: 9px;">
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                    Taux: <strong style="font-style: italic; padding: 5px;">
                        <u>{{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %</u>
                    </strong>
                </td>
            @else
                <td>
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                    Taux: 
                    <strong style="font-style: italic; background-color: #333; color: #fff; padding: 10px;">
                        {{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %
                    </strong>
                </td>
            @endif
            
            <td style="font-size: 9px">
                <u>------------</u> 
            </td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;">Exclusion:</td>
            <td style="font-style: italic; background-color: #ddd; fontsize: 7px;"> <span class="check-box"></span></td>
        </tr>
    </table>
    <!-- Summary Table -->
    <!-- Tableau des sanctions et absences -->
    <style>
        .check-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 0.7px solid #000;
            margin-right: 3px;
            vertical-align: middle;
        }
    </style>

    <table class="summary-table" style="margin-top: 5px; width: 100%; border-collapse: collapse; font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 25%; text-align: center; border: 1px solid #000; padding: 2px;">
                    Absences & Retards
                </th>
                <th style="width: 37.5%; text-align: center; border: 1px solid #000; padding: 2px;">
                    Avertissements
                </th>
                <th style="width: 37.5%; text-align: center; border: 1px solid #000; padding: 2px;">
                    Blâmes
                </th>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 22px;">
                <!-- Absences & Retards côte à côte -->
                <td style="border: 1px solid #000; text-align: center; font-size: 8px; padding: 2px;">
                    <strong>Absences:</strong>
                    <span style="display: inline-block; width: 35px; border-bottom: 0.5px dashed #555;">
                        @if(isset($student['absences']) && $student['absences'] > 0)
                            <strong>{{ $student['absences'] }} h</strong>
                        @else
                            0 h
                        @endif
                    </span>
                    &nbsp;&nbsp;
                    <strong>Retards:</strong>
                    <span style="display: inline-block; width: 35px; border-bottom: 0.5px dashed #555;"></span>
                </td>

                <!-- Avertissements -->
                <td style="border: 1px solid #000; text-align: center; font-size: 8px; padding: 2px;">
                    <span class="check-box"></span>Travail
                    &nbsp;&nbsp;&nbsp;
                    <span class="check-box"></span>Discipline
                </td>

                <!-- Blâmes -->
                <td style="border: 1px solid #000; text-align: center; font-size: 8px; padding: 2px;">
                    <span class="check-box"></span>Travail
                    &nbsp;&nbsp;&nbsp;
                    <span class="check-box"></span>Discipline
                </td>
            </tr>
        </tbody>
    </table>


    <!--Fin de mise a jour de la table sommaire-->

    <hr>
    @if (($bulletinData['termType']['name'] == 'Trimestre 3' || $bulletinData['termType']['name'] == 'Semestre 2'))
        @php
            $excludedClasses = ['3ème A', '3ème B', '3ème C', '3ème D', '3ème E', 
                            '1ère A4-1', '1ère A4-2', '1ere D4', 
                            'Tle D4-1', 'Tle D4-2', 'Tle A4-1', 'Tle A4-2'];
            
            $annualMean = $student['annual_mean']['mean'] ?? 0;
            $mathAverage = 0;
            $physicsAverage = 0;
            
            foreach ($student['subjects'] as $subject) {
                if ($subject['subject_name'] == 'Mathématiques') {
                    $mathAverage = is_numeric($subject['average_marks']) ? $subject['average_marks'] : 0;
                }
                if ($subject['subject_name'] == 'Phys. Chimie Tech.' || $subject['subject_name'] == 'Phys. Chimie') {
                    $physicsAverage = is_numeric($subject['average_marks']) ? $subject['average_marks'] : 0;
                }
            }
        @endphp

    
        <h4> 
            <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                <b>Appréciation: <u><strong style="font-style: italic; background-color: #ddd; font-size: 9px;">{{ $student['annual_mean']['appreciation'] ?? 'N/A' }}</strong></u></b> | 
                 
                @if ($annualMean >= 10 && !in_array($bulletinData['class']['name'], $excludedClasses))
                    {{-- Regular progression cases --}}
                    @if (str_starts_with($bulletinData['class']['name'], '6ème'))
                        DECISION: Passe en classe de 5ème
                    @elseif (str_starts_with($bulletinData['class']['name'], '5ème'))
                        DECISION: Passe en classe de 4ème
                    @elseif (str_starts_with($bulletinData['class']['name'], '4ème'))
                        DECISION: Passe en classe de 3ème
                    
                    {{-- Special cases for secondary classes --}}
                    @elseif (in_array($bulletinData['class']['name'], ['2nd A4-1', '2nd A4-2']))
                        DECISION: Passe en classe de 1ère A4
                    @elseif ($bulletinData['class']['name'] == '2nd CD')
                        @if ($annualMean >= 12 && $mathAverage >= 12 && $physicsAverage >= 12)
                            DECISION: Passe en 1ère SCIENTIFIQUE
                        @elseif($annualMean == 'N/A')
                            DECISION: Abandon
                        @else
                            DECISION: Passe en 1ère SCIENTIFIQUE
                        @endif
                    @else
                        N/A
                    @endif
                
                {{-- Borderline case --}}
                @elseif ($annualMean >= 9 && $annualMean < 10 && !in_array($bulletinData['class']['name'], $excludedClasses))
                    DECISION: _________________________
                
                {{-- Failing case --}}
                @elseif($annualMean >= 8 && $annualMean < 9 && !in_array($bulletinData['class']['name'], $excludedClasses))
                    DECISION: Redouble sa classe
                @elseif( $annualMean < 8 && !in_array($bulletinData['class']['name'], $excludedClasses))
                    DECISION: Exclu pour Infuffisance de Travail
                @elseif( in_array($bulletinData['class']['name'], $excludedClasses))
                    @if(in_array($bulletinData['class']['name'], ['3ème A', '3ème B', '3ème C', '3ème D', '3ème E']))
                        DECISION BEPC: ___________________
                    @elseif(in_array($bulletinData['class']['name'], ['1ère A4-1', '1ère A4-2', '1ere D4']))
                        DECISION BAC 1: ___________________
                    @elseif(in_array($bulletinData['class']['name'], ['Tle D4-1', 'Tle D4-2', 'Tle A4-1', 'Tle A4-2']))
                        DECISION BAC 2: ___________________
                    @endif
                @endif
            </div>
        </h4>
    @else
        {{-- For non-final terms --}}
        <h4> 
            <div style="border: 2px solid #000; padding: 10px; display: inline-block; font-weight: bold;">
                <b>Appréciation du Travail: <u><strong style="font-style: italic; background-color: #ddd; font-size: 9px;">{{ $student['appreciation_term'] }}</strong></u></b> 
            </div>
        </h4>
    @endif







    <!-- Section signature élargie -->
    <!-- Section signature avec table (solution la plus fiable) -->
    {{-- <div class="signature-section" style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 45%; text-align: left; vertical-align: top;">
                    <p style="text-align: center; font-size: 9px; margin: 0 0 5px 0;">Le Proviseur</p>
                    <div style="height: 75px; border: 1px dashed #ece8e8; width: 90%; 
                            display: flex; align-items: center; justify-content: center;">
                        <span style="text-align: center; font-size: 8px; color: #373535;">Cachet et Signature</span>
                    </div>
                    <p style="margin-bottom: 5px; text-align: center; margin-top: 10px;">
                        <h4 style="margin: 0;"><strong>SODJA Kalaha</strong></h4>
                    </p>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <p style="font-size: 9px; margin: 0 0 5px 0;">Le Titulaire</p>
                    <div style="height: 30px; border: 1px dashed #b5adad; width: 90%; margin-left: auto;
                            display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 8px; color: #666;">Signature</span>
                    </div>
                    <strong>{{ $bulletinData['principalTeacher'] }}</strong>
                </td>
            </tr>
        </table>

        <p style="text-align: right; margin-bottom: 5px; font-size: 9px; margin-top: 10px;">
            Fait à Agoè Nyivé le, {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}.
        </p>
        
    </div> --}}
    <!-- Section signature avec positions inversées -->
        <div class="signature-section" style="margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <!-- ✅ TITULAIRE À GAUCHE -->
                    <td style="width: 45%; text-align: left; vertical-align: top;">
                        <p style="font-size: 9px; margin: 0 0 5px 0;">Le Titulaire</p>
                        <div style="height: 30px; border: 1px dashed #b5adad; width: 90%;
                                display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 8px; color: #ffffff;">Signature</span>
                        </div>
                        <p style="margin-top: 5px; font-size: 9px;">
                            <strong>{{ $bulletinData['principalTeacher'] }}</strong>
                        </p>
                    </td>
                    
                    <td style="width: 10%;"></td>
                    
                    <!-- ✅ PROVISEUR À DROITE -->
                    <td style="width: 45%; text-align: right; vertical-align: top;">
                        <p style="text-align: center; font-size: 9px; margin: 0 0 5px 0;">Le Proviseur</p>
                        <div style="height: 75px; border: 1px dashed #ece8e8; width: 90%; margin-left: auto;
                                display: flex; align-items: center; justify-content: center;">
                            <span style="text-align: center; font-size: 8px; color: #ffffff;">Cachet et Signature</span>
                        </div>
                        <p style="margin-bottom: 5px; text-align: center; margin-top: 10px;">
                            <h4 style="margin: 0;"><strong>SODJA Kalaha</strong></h4>
                        </p>
                    </td>
                </tr>
            </table>

            <p style="text-align: right; margin-bottom: 5px; font-size: 9px; margin-top: 10px;">
                Fait à Agoè Nyivé le, {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}.
            </p>
        </div>





    {{-- <p style="text-align: right; margin-bottom: 10px">Fait à Agoè Nyivé le, {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}.</p>
    <p><h4><strong>SODJA Kalaha</strong></h4></p> --}}
    </div>
</div>

<div class="bottom-footer">
    <p><strong>Ce bulletin de notes est produit en un exemplaire; tout contrefaçon est puni par la loi en vigueur.</strong></p>
</div>
@endforeach
</body>
</html>