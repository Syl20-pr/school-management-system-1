<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire - {{ $bulletinData['class']['name'] }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.25cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.15;
        }

        .container {
            width: 100%;
            max-width: 19cm;
            margin: 0 auto;
            padding: 7px;
            background-color: white;
            page-break-after: always;
            position: relative;
        }

        /* Header compact */
        .header {
            position: relative;
            text-align: center;
            padding: 12px 15px;
            border-bottom: 2px solid #000;
            margin-bottom: 8px;
        }

        .header h4 {
            margin: 3px 0;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 900;
        }

        .header p {
            margin: 1px 0;
            font-size: 13px;
            font-weight: 600;
        }

        /* Position absolue pour textes latéraux */
        .header-left-text {
            position: absolute;
            top: 5px;
            left: 8px;
            font-size: 13px;
            text-align: left;
            font-weight: 600;
        }

        .header-right-text {
            position: absolute;
            top: 5px;
            right: 8px;
            font-size: 13px;
            text-align: right;
            font-weight: 600;
        }

        /* Tables compactes */
        .details-table {
            width: 100%;
            margin: 8px 0 6px 0;
            border: 1.1px solid #000;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 4px 5px;
            border: 1.1px solid #000;
            font-size: 13px;
            font-weight: 600;
        }

        .details-table td:nth-child(1) {
            background-color: #f8f8f8;
            width: 45%;
        }

        /* Tables des notes ultra compactes */
        .marks-table {
            width: 100%;
            margin: 5px 0;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 12px;
        }

        .marks-table th {
            background-color: #4CAF50;
            color: white;
            padding: 3px 2px;
            border: 0.8px solid #000;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }

        .marks-table td {
            padding: 3px 1px;
            border: 0.8px solid #000;
            text-align: center;
            vertical-align: middle;
            font-weight: 800; /* NOTES TRÈS GRAS */
            font-size: 12px;
        }

        .marks-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Totaux */
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: 800 !important;
            border-top: 1.5px solid #000 !important;
        }

        .grand-total-row {
            background-color: #d4edda !important;
            font-weight: 800 !important;
            border-top: 2px solid #000 !important;
        }

        /* Summary table compacte */
        .summary-table {
            width: 100%;
            margin: 6px 0;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 12px;
        }

        .summary-table td {
            padding: 4px;
            border: 0.8px solid #000;
            text-align: left;
            vertical-align: middle;
            font-weight: 600;
        }

        .summary-table tr td:first-child {
            font-weight: 700;
            width: 35%;
        }

        /* Titres des groupes */
        .section-title {
            text-align: center;
            font-weight: 700;
            font-size: 12px;
            margin: 6px 0 2px 0;
            background-color: #f0f0f0;
            padding: 3px;
            border: 1px solid #ddd;
        }

        /* Checkbox style */
        .check-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 0.8px solid #000;
            margin-right: 3px;
            vertical-align: middle;
        }

        /* Footer */
        .bottom-footer {
            position: fixed;
            bottom: 0.3cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7px;
            color: #333;
            background: white;
            z-index: 1000;
            padding: 3px 0;
            border-top: 0.5px solid #ddd;
        }

        /* Appréciation */
        .appreciation-box {
            border: 1.5px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: 700;
            font-size: 14px;
            margin: 6px 0;
            background-color: #f9f9f9;
        }

        /* Signatures compactes */
        .signature-section {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
        }

        .signature-box {
            height: 25px;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 3px auto;
            background-color: #f8f8f8;
            font-size: 2px;
            color: #666;
        }

        /* Styles optimisés pour l'impression */
        @media print {
            .container {
                padding: 6px;
            }
            body {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

@foreach($bulletinData['students'] as $student)
<div class="container" style="position: relative;">
    <!-- Filigrane léger -->
    <div style="
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-35deg);
        color: rgba(97, 91, 91, 0.15);
        font-size: 70px;
        font-weight: bold;
        white-space: nowrap;
        text-align: center;
        z-index: 0;
    ">
        LYCÉE DE NANEGBE
    </div>

    <div style="position: relative; z-index: 1;">
    
    <!-- Header Section -->
    <div class="header">
        <!-- Left Text -->
        <div class="header-left-text">
            <p>MINISTERE DE L'EDUCATION</p>
            <p>NATIONAL</p>
            <hr style="margin: 1px 0; border: 0.5px solid #000;">
            <p>DRE - GRAND LOME/IESG-AGOÉ NYIVE</p>
            <p>LYCÉE DE NANEGBE</p>
            <p>Tél:+228 90296872, AGOÉ NYIVE/TOGO</p>
        </div>

        <!-- Centered Content -->
        <img src="{{ public_path() . '/upload/Logo_Nanegbeoo.png' }}" alt="School Logo" style="max-width: 60px; margin: 2px auto;">
        <p><strong>L Y N A</strong></p>
        <p><b>Travail - Discipline - Succès</b></p>
        
        <h4>
            @php
                $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                                '5ème A', '5ème B', '5ème C', '5ème D', 
                                '4ème A', '4ème B', '4ème C', '4ème D', 
                                '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
                $isCycle1 = in_array($bulletinData['class']['name'], $cycle1Classes);
            @endphp

            @if($isCycle1)
                @if($bulletinData['termType']['name'] === 'Trimestre 1')
                    <div style="border: 1.5px solid #000; padding: 5px; display: inline-block; font-weight: 900; font-size: 13px;">
                    BULLETIN DU PREMIER TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Trimestre 2')
                    <div style="border: 1.5px solid #000; padding: 5px; display: inline-block; font-weight: 900; font-size: 13px;">
                    BULLETIN DU DEUXIÈME TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Trimestre 3')
                    <div style="border: 1.5px solid #000; padding: 5px; display: inline-block; font-weight: 900; font-size: 13px;">
                    BULLETIN DU TROISIÈME TRIMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @else
                    <u><b>BULLETIN SCOLAIRE</b></u>
                @endif
            @else
                @if($bulletinData['termType']['name'] === 'Semestre 1')
                    <div style="border: 1.5px solid #000; padding: 5px; display: inline-block; font-weight: 900; font-size: 13px;">
                    BULLETIN DU PREMIER SEMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @elseif($bulletinData['termType']['name'] === 'Semestre 2')
                    <div style="border: 1.5px solid #000; padding: 5px; display: inline-block; font-weight: 900; font-size: 13px;">
                    BULLETIN DU DEUXIÈME SEMESTRE | Classe: {{ $bulletinData['class']['name'] }} </div>
                @else
                    <u><b>BULLETIN SCOLAIRE</b></u>
                @endif
            @endif
        </h4>

        <!-- Right Text -->
        <div class="header-right-text">
            <p>RÉPUBLIQUE TOGOLAISE</p>
            <p>Travail - Liberté - Patrie</p>
            <hr style="margin: 1px 0; border: 0.5px solid #000;">
            <div style="border: 0.8px solid #000; padding: 3px; display: inline-block;">
                <h4 style="margin: 0; font-size: 13px; font-weight: 900;">Ann. Scol.: <b>{{ $bulletinData['year']['name'] }}</b></h4>
            </div> 
        </div>
    </div>

    <!-- Student Details Table -->
    <table class="details-table">
        <tr>
            <td>Nom & Prénom(s): <strong style="font-style: italic; font-weight: 800;">{{ $student['student_name'] }}</strong></td>
            <td>Sexe: <strong style="font-style: italic; font-weight: 800;">{{ $student['gender'] === 'Féminin' ? 'F' : 'M' }}</strong> | 
                Statut: <strong style="font-style: italic; font-weight: 800;">{{ $student['statusclass'] }}</strong> | 
                Effectif: <strong style="font-style: italic; font-weight: 800;">{{ $bulletinData['statistics']['number_of_students'] }}</strong></td>
        </tr> 
    </table>

    @php
    $scientificSubjects = $isCycle1 
        ? ['Mathématiques', 'Phys. Chimie Tech.', 'S.V.T']
        : ['Mathématiques', 'Phys. Chimie', 'S.V.T'];
    
    $specificSubjects = ['E.P.S'];

    $scientificGroup = array_filter($student['subjects'], function($subject) use ($scientificSubjects) {
        return in_array($subject['subject_name'], $scientificSubjects);
    });

    $literaryGroup = array_filter($student['subjects'], function($subject) use ($specificSubjects, $scientificSubjects) {
        return !in_array($subject['subject_name'], $scientificSubjects)
            && !in_array($subject['subject_name'], $specificSubjects);
    });

    $specificGroup = array_filter($student['subjects'], function($subject) use ($specificSubjects) {
        return in_array($subject['subject_name'], $specificSubjects);
    });
    @endphp

    <!-- Scientific Subjects Table -->
    <div class="section-title">MATIÈRES SCIENTIFIQUES</div>
    
    <table class="marks-table">
        <thead>
            <tr>
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
                
                if (is_numeric($subject['subjective_mark_coefficient'])) {
                    $totalCoefScientifique += $subject['subjective_mark_coefficient'];
                }
                if (is_numeric($subject['weighted_average'])) {
                    $totalMoyPondereeScientifique += $subject['weighted_average'];
                }
            @endphp 
                <tr>
                    <td style="text-align: left; font-weight: 600; padding-left: 3px;">{{ $subject['subject_name'] }}</td>
                    <td style="font-weight: 800;">
                        @if(!empty($subject['exam_marks']['notes_interros']) && is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if(!empty($subject['exam_marks']['notes_devoirs']) && is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">{{ $subject['subjective_mark_coefficient'] }}</td>
                    <td style="font-weight: 800;">
                        @if(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            {{ $subject['weighted_average'] }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
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
                        {{ $rank }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                        @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                            <span style="font-size:6px; color:#555;">ex</span>
                        @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-weight: 600;">{{ $subject['appreciation'] }}</td>
                    <td style="font-weight: 600;">{{ $surname_Teacher }}</td> 
                    <td></td>
                </tr>
            @endforeach
            
            <tr class="total-row">
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

    <!-- Literary Subjects Table -->
    <div class="section-title">MATIÈRES LITTÉRAIRES</div>
    <table class="marks-table">
        <thead>
            <tr>
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
                    
                    if (!$isInapte) {
                        if (is_numeric($subject['subjective_mark_coefficient'])) {
                            $totalCoefLitteraire += $subject['subjective_mark_coefficient'];
                        }
                        if (is_numeric($subject['weighted_average'])) {
                            $totalMoyPondereeLitteraire += $subject['weighted_average'];
                        }
                    }
                @endphp
                <tr>
                    <td style="text-align: left; font-weight: 600; padding-left: 3px;">{{ $subject['subject_name'] }}</td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_interros'])&& is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_devoirs'])&& is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            --
                        @else
                            {{ $subject['subjective_mark_coefficient'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            --
                        @elseif(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-weight: 800;">
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
                            {{ $subject['rank'] }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                            @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                                <span style="font-size:6px; color:#555;">ex</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-weight: 600;">
                        @if($isInapte)
                            Dispensé(e)
                        @else
                            {{ $subject['appreciation'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 600;">{{ $surname_Teacher }}</td>
                    <td></td>
                </tr>
            @endforeach
            
            <tr class="total-row">
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
        </tbody>
    </table>

    <!-- Specific Subjects Table -->
    <div class="section-title">MATIÈRES SPÉCIFIQUES</div>
    <table class="marks-table">
        <thead>
            <tr>
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
                    
                    if (!$isInapte) {
                        if (is_numeric($subject['subjective_mark_coefficient'])) {
                            $totalCoefSpecific += $subject['subjective_mark_coefficient'];
                        }
                        if (is_numeric($subject['weighted_average'])) {
                            $totalMoyPondereeSpecific += $subject['weighted_average'];
                        }
                    }
                @endphp
                <tr>
                    <td style="text-align: left; font-weight: 600; padding-left: 3px;">{{ $subject['subject_name'] }}</td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_interros'])&& is_numeric($subject['exam_marks']['notes_interros']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_interros']) }}
                        @else
                            {{ $subject['exam_marks']['notes_interros'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(!empty($subject['exam_marks']['notes_devoirs'])&& is_numeric($subject['exam_marks']['notes_devoirs']))
                            {{ sprintf('%02d', $subject['exam_marks']['notes_devoirs']) }}
                        @else
                            {{ $subject['exam_marks']['notes_devoirs'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_classe']))
                            {{ sprintf('%05.2f', $subject['moyenne_classe']) }}
                        @else
                            {{ $subject['moyenne_classe'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['moyenne_compo']))
                            {{ sprintf('%05.2f', $subject['moyenne_compo']) }}
                        @else
                            {{ $subject['moyenne_compo'] }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            Disp.
                        @elseif(is_numeric($subject['average_marks']))
                            {{ sprintf('%05.2f', $subject['average_marks']) }}
                        @else
                            {{ $subject['average_marks'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            --
                        @else
                            {{ $subject['subjective_mark_coefficient'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 800;">
                        @if($isInapte)
                            --
                        @elseif(is_numeric($subject['weighted_average']))
                            {{ sprintf('%05.2f', $subject['weighted_average']) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-weight: 800;">
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
                            {{ $subject['rank'] }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                            @if(!empty($subject['is_ex_aequo']) && $subject['is_ex_aequo'])
                                <span style="font-size:6px; color:#555;">ex</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="font-weight: 600;">
                        @if($isInapte)
                            Dispensé(e)
                        @else
                            {{ $subject['appreciation'] ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="font-weight: 600;">{{ $surname_Teacher }}</td>
                    <td></td>
                </tr>
            @endforeach
            
            <tr class="total-row">
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
            
            @php
                $totalCoefGeneral = $totalCoefScientifique + $totalCoefLitteraire + $totalCoefSpecific;
                $totalMoyPondereeGeneral = $totalMoyPondereeScientifique + $totalMoyPondereeLitteraire + $totalMoyPondereeSpecific;
            @endphp
            <tr class="grand-total-row">
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

    <!-- Summary Table -->
    @php
        $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', 
                        '5ème A', '5ème B', '5ème C', '5ème D', 
                        '4ème A', '4ème B', '4ème C', '4ème D', 
                        '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
        
        $isCycle1 = in_array($bulletinData['class']['name'], $cycle1Classes);
        $isFinalTerm = ($isCycle1 && $bulletinData['termType']['name'] == 'Trimestre 3') || 
                    (!$isCycle1 && $bulletinData['termType']['name'] == 'Semestre 2');
    @endphp

    <table class="summary-table">
        <tr>
            @if ($isFinalTerm)
                <td><strong>Moyenne: <u>{{ number_format($student['term_mean'], 2) }}</u></strong> | 
                    <u><strong style="font-style: italic;">
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
                            Rang: {{ $Trank }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                            @if(!empty($student['term_is_ex_aequo']) && $student['term_is_ex_aequo'])
                                <span style="font-size:6px; color:#555;">ex</span>
                            @endif
                        @endif
                    </strong></u>
                </td>
            @else
                <td><strong>Moyenne: {{ number_format($student['term_mean'], 2) }}</strong> | 
                    <strong style="font-style: italic; background-color: #333; color: #fff; padding: 2px;">
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
                            Rang: {{ $Trank }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                            @if(!empty($student['term_is_ex_aequo']) && $student['term_is_ex_aequo'])
                                <span style="font-size:6px; color:#fff;">ex</span>
                            @endif
                        @endif
                    </strong>
                </td>
            @endif
            
            <td>
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
                                            {{ $rank }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
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
            <td>Tableau d'honneur:</td>
            <td><span class="check-box"></span></td>
        </tr>
        
        <tr>
            @if ($isFinalTerm)
                <td>
                    En Lettres: <strong style="font-style: italic;"><u>{{ ucwords(strtolower($student['term_mean_words'])) }}</u></strong>
                </td>
            @else
                <td>
                    En Lettres: <strong style="font-style: italic; background-color: #333; color: #fff; padding: 2px;">
                        {{ ucwords(strtolower($student['term_mean_words'])) }}</strong>
                </td>
            @endif
            
            @if ($isFinalTerm)
                <td style="font-style: italic; background-color: #333; color: #fff; padding: 2px;">
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
                                {{ $rank }}<sup style="font-size: 6px;">{{ $rankSuffix }}</sup>
                            @endif
                        </u></strong> |  
                        En Lettres: <strong><u>{{ $student['annual_mean']['mean_words'] }}</u></strong>
                    @else
                        Moy. Ann.: <strong><u>N/A</u></strong>
                    @endif
                </td>
            @else
                <td></td>
            @endif
            <td>Félicitations:</td>
            <td><span class="check-box"></span></td>
        </tr>
        
        <tr>
            <td>
                +Forte Moy.: <strong><u>{{ number_format($bulletinData['statistics']['highest_term_mean'], 2) }}</u></strong> | 
                @if ($student['is_abandon'])
                    +Faible Moy.: <strong><u>N/A</u></strong>
                @else
                    +Faible Moy.: <strong><u>{{ number_format($bulletinData['statistics']['lowest_term_mean'], 2) }}</u></strong>
                @endif
            </td>
            
            @if ($isFinalTerm)
                <td style="font-style: italic; background-color: #333; color: #fff; padding: 2px;">
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
                <td></td>
            @endif
            <td>Encouragements:</td>
            <td><span class="check-box"></span></td>
        </tr>
        
        <tr>
            @if ($isFinalTerm)
                <td>
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                    Taux: <strong style="font-style: italic;">
                        <u>{{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %</u>
                    </strong>
                </td>
            @else
                <td>
                    Moy. Classe: <strong><u>{{ number_format($bulletinData['statistics']['class_term_mean'], 2) }}</u></strong> | 
                    Taux: 
                    <strong style="font-style: italic; background-color: #333; color: #fff; padding: 2px;">
                        {{ number_format($bulletinData['statistics']['percentage_above_10'], 2) }} %
                    </strong>
                </td>
            @endif
            
            {{-- <td>
                <u>Titulaire</u>: <strong>{{ $bulletinData['principalTeacher'] }}</strong> | Signa.: _____
            </td> --}}
            <td style="border: 0.8px solid #000;
                    height: 30px;
                    background: repeating-linear-gradient(
                        to right, 
                        #ccc, 
                        #ccc 2px, 
                        transparent 2px, 
                        transparent 4px
                    );">
                &nbsp;
            </td>



            <td>Exclusion:</td>
            <td><span class="check-box"></span></td>
        </tr>
    </table>

    <!-- Tableau des sanctions et absences -->
    <table style="margin-top: 4px; width: 100%; border-collapse: collapse; font-size: 12px; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 25%; text-align: center; border: 0.8px solid #000; padding: 1px;">
                    Absences & Retards
                </th>
                <th style="width: 37.5%; text-align: center; border: 0.8px solid #000; padding: 1px;">
                    Avertissements
                </th>
                <th style="width: 37.5%; text-align: center; border: 0.8px solid #000; padding: 1px;">
                    Blâmes
                </th>
            </tr>
        </thead>
        <tbody>
            <tr style="height: 16px;">
                <td style="border: 0.8px solid #000; text-align: center; font-size: 11px; padding: 1px;">
                    <strong>Absences:</strong>
                    <span style="display: inline-block; width: 25px; border-bottom: 0.5px dashed #555;">
                        @if(isset($student['absences']) && $student['absences'] > 0)
                            <strong>{{ $student['absences'] }} h</strong>
                        @else
                            0 h
                        @endif
                    </span>
                    &nbsp;&nbsp;
                    <strong>Retards:</strong>
                    <span style="display: inline-block; width: 25px; border-bottom: 0.5px dashed #555;"></span>
                </td>

                <td style="border: 0.8px solid #000; text-align: center; font-size: 11px; padding: 1px;">
                    <span class="check-box"></span>Travail
                    &nbsp;&nbsp;
                    <span class="check-box"></span>Discipline
                </td>

                <td style="border: 0.8px solid #000; text-align: center; font-size: 11px; padding: 1px;">
                    <span class="check-box"></span>Travail
                    &nbsp;&nbsp;
                    <span class="check-box"></span>Discipline
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Appréciation -->
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

        <div class="appreciation-box">
            <b>Appréciation: <u><strong style="font-style: italic;">{{ $student['annual_mean']['appreciation'] ?? 'N/A' }}</strong></u></b> | 
             
            @if ($annualMean >= 10 && !in_array($bulletinData['class']['name'], $excludedClasses))
                @if (str_starts_with($bulletinData['class']['name'], '6ème'))
                    DECISION: Passe en classe de 5ème
                @elseif (str_starts_with($bulletinData['class']['name'], '5ème'))
                    DECISION: Passe en classe de 4ème
                @elseif (str_starts_with($bulletinData['class']['name'], '4ème'))
                    DECISION: Passe en classe de 3ème
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
            
            @elseif ($annualMean >= 9 && $annualMean < 10 && !in_array($bulletinData['class']['name'], $excludedClasses))
                DECISION: _________________________
            
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
    @else
        <div class="appreciation-box">
            <b>Appréciation du Travail: <u><strong style="font-style: italic;">{{ $student['appreciation_term'] }}</strong></u></b> 
        </div>
    @endif

    <!-- Section signature -->
    <div class="signature-section">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <tr>
                <td style="width: 45%; text-align: left; vertical-align: top;">
                    <p style="margin: 0 0 2px 0; font-weight: 600;">Le Titulaire</p>
                    <div class="signature-box">
                        <span>Signature</span>
                    </div>
                    <p style="margin-top: 2px; font-weight: 600;">
                        {{ $bulletinData['principalTeacher'] }}
                    </p>
                </td>
                
                <td style="width: 10%;"></td>
                
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <p style="text-align: center; margin: 0 0 2px 0; font-weight: 600;">Le Proviseur</p>
                    <div class="signature-box" style="height: 35px;">
                        <span>Cachet et Signature</span>
                    </div>
                    <p style="text-align: center; margin-top: 2px; font-weight: 600;">
                        SODJA Kalaha
                    </p>
                </td>
            </tr>
        </table>

        <p style="text-align: right; margin: 4px 0 0 0; font-size: 11px;">
            Fait à Agoè Nyivé le, {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}.
        </p>
    </div>

    </div>
</div>

<div class="bottom-footer">
    <p><strong>Ce bulletin de notes est produit en un exemplaire; tout contrefaçon est puni par la loi en vigueur.</strong></p>
</div>
@endforeach
</body>
</html>