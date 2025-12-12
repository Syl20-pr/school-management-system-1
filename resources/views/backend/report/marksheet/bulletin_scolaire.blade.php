<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire</title>
    <style>
        /* Page Styling */
        @page {
            size: A4;
            margin: 0.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 19cm;
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            box-sizing: border-box;
        }

        /* Header Styling */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .header-left,
        .header-right {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            text-align: left;
        }

        .header-left img {
            max-width: 100px;
            height: auto;
            text-align: center;
            display: block;
            margin: 0 auto;
        }

        /* Line Separator for <p> elements */
    .header-left p,
    .header-right p {
        border-bottom: 1px solid #ddd; /* Light gray line */
        padding-bottom: 4px; /* Spacing between text and line */
        margin-bottom: 4px; /* Spacing between paragraphs */
    }

    .header-left p:last-child,
    .header-right p:last-child {
        border-bottom: none; /* No line after the last paragraph */
    }

        .header-right {
            text-align: right;
        }

        .header-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .sub-header {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
        }

        .header-info p {
            margin: 2px 0;
            font-size: 14px;
        }

        .title-box {
            text-align: center;
            padding: 5px;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #000;
            background-color: #e0e0e0;
            margin: 20px auto;
            width: fit-content;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        /* Footer Styling */
        .footer {
            font-size: 12px;
            text-align: right;
            margin-top: 30px;
            color: #555;
        }
        
        .signature {
            text-align: center;
            margin-top: 30px;
        }

        .signature hr {
            width: 60%;
            margin: 10px auto;
            border: solid 1px #000;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path() . '/upload/giant-logo.png' }}" alt="Logo de l'École">
            <p class="sub-header">Lycée de NANEGBE</p>
            <p class="sub-header">22, boulevard de l'Emir AbdelKader - AGOÉ</p>
            <p class="sub-header">Tél. +228, Fax +228</p>
            <p class="sub-header">Premier et Deuxième Cycles de l'Enseignement Secondaire Générales</p>
        </div>
        <div class="header-right">
            <p class="header-title">République Togolaise</p>
            <p class="sub-header">Ministère de l'Enseignement Primaire et Secondaire</p>
            <p class="sub-header">Direction Régionale de l'Enseignement - Lomé</p>
            <p class="sub-header">Inspection</p>
            <div class="title-box">
                BULLETIN DE NOTES DU PREMIER TRIMESTRE<br>
                Année scolaire : 2024 - 2025
                </div>
        </div>
    </div>

    <!-- Title Box 
    <div class="title-box">
        BULLETIN DE NOTES DU TROISIEME TRIMESTRE<br>
        Année scolaire : 2011 - 2012
    </div>-->

    <!-- Student and Exam Information Tables -->
    <table class="details-table">
        <tr>
            <td>Matricule de l'élève</td>
            <td>{{ $studentMarks->first()->student->id_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Nom et prénom de l'élève</td>
            <td>{{ $studentMarks->first()->student->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Date et lieu de naissance</td>
            <td>Né le 12/06/1988 à Mazouna</td>
        </tr>
        <tr>
            <td>Classe/effectif</td>
            <td>{{ $studentMarks->first()->student_class->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Classe doublée</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Adresse</td>
            <td>Tamda Mazouna</td>
        </tr>
    </table>

    <!-- Marks Table -->
    <table class="marks-table">
        <thead>
            <tr>
                <th>SL</th>
                <th>Matières</th>
                <th>Notes</th>
                <th>Coef.</th>
                <th>Grade (Lettre)</th>
                <th>Grade (Point)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentMarks as $key => $mark)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $mark->subject_name }}</td>
                    <td>{{ $mark->marks ?? 'N/A' }}</td>
                    <td>{{ $mark->subjective_mark }}</td>
                    <td>{{ $mark->grade_name }}</td>
                    <td>{{ $mark->grade_point }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Table -->
    <table class="summary-table">
        <tr>
            <td colspan="3"><strong>Somme :</strong></td>
            <td colspan="3"><strong>{{ $totalMarks }}</strong></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Note en Point :</strong></td>
            <td colspan="3"><strong>{{ number_format($averagePoint, 2) }}</strong></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Point for Letter Grade :</strong></td>
            <td colspan="3"><strong>{{ $finalGrade->grade_name ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Appréciation :</strong></td>
            <td colspan="3"><strong>{{ $finalGrade->remarks ?? 'N/A' }}</strong></td>
        </tr>
    </table>

    <!-- Signature Section -->
    <div class="signature">
        <hr>
        <p>Signature du Directeur</p>
    </div>
</div>

</body>
</html>
