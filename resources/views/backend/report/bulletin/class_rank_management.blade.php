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
    </style>
</head>
<body>
<div class="container">
    <!-- Header Section -->
    <div class="header">
        <h1>Classement des Élèves | {{ $termTypeName }}| Année Scolaire : {{ $yearName }}</h1>
        <p><strong>Classe : <strong>{{ $className }}</strong> | Effectif : <strong>{{ $numberOfStudents }}</strong> | Titulaire : <strong>{{ $principalTeacherName }}</strong></p>
        <p>Moyenne Classe: <strong>{{ $classTermMean }}</strong> | Pourcentage d'admis : <strong>{{ $percentageAbove10 }}</strong></p>
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
            // Sort the students by term mean in descending order
            $rankedStudents = collect($studentData)->sortByDesc('term_mean')->values();
        @endphp
        @foreach($rankedStudents as $index => $student) 
            @php
                // Calculate the rank
                $rank = $index + 1;

                // Split name into surname and first name
                $nameParts = explode(' ', $student['student_name'], 2);
                $surname = strtoupper($nameParts[0]);
                $firstName = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';

                // Determine the suffix based on rank and gender
                $gender = $student['gender'] === 'Féminin' ? 'F' : 'M';
                if ($rank === 1) {
                    $rankSuffix = $gender === 'F' ? 'ère' : 'er';
                } else {
                    $rankSuffix = 'ème';
                }

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

    <!-- Footer Section -->
    <div class="footer">
        <!-- <p><strong>Classement basé sur la moyenne du trimestre. Produit automatiquement par le système.</strong></p> -->
        <p><strong>Classement basé sur la moyenne du Semestre. Produit automatiquement par le système.</strong></p>
        <p>Date : {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
    </div>
</div>
</body>
</html>
