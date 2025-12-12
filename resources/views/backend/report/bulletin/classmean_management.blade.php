<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Trimestrielles</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>STATISTIQUES DU {{ strtoupper($termName) }} DE LA {{ $className }}</h1>
        <p>Année Scolaire : {{ $yearName }}</p>
    </div>

    <!-- Statistics Table -->
    <table>
        <thead>
        <tr>
            <th>Statistiques</th>
            <th>Filles</th>
            <th>Garçons</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Effectif Inscrit</td>
            <td>{{ $girls }}</td>
            <td>{{ $boys }}</td>
            <td>{{ $girls + $boys }}</td>
        </tr>
        <tr>
            <td>Effectif Composant</td>
            <td>{{ $girlsPresent }}</td>
            <td>{{ $boysPresent }}</td>
            <td>{{ $girlsPresent + $boysPresent }}</td>
        </tr>
        <tr>
            <td>Admis</td>
            <td>{{ $girlsPass }}</td>
            <td>{{ $boysPass }}</td>
            <td>{{ $girlsPass + $boysPass }}</td>
        </tr>
        <tr>
            <td>Forte Moyenne</td>
            <td>{{ number_format($girlsMax, 2) }}</td>
            <td>{{ number_format($boysMax, 2) }}</td>
            <td>{{ number_format(max($girlsMax, $boysMax), 2) }}</td>
        </tr>
        <tr>
            <td>Faible Moyenne</td>
            <td>{{ number_format($girlsMin, 2) }}</td>
            <td>{{ number_format($boysMin, 2) }}</td>
            <td>{{ number_format(min($girlsMin, $boysMin), 2) }}</td>
        </tr>
        <tr>
            <td>Moyenne Générale</td>
            <td>{{ number_format($girlsAverage, 2) }}</td>
            <td>{{ number_format($boysAverage, 2) }}</td>
            <td>{{ number_format(($girlsAverage + $boysAverage) / 2, 2) }}</td>
        </tr>
        <tr>
            <td>Taux de Réussite</td>
            <td>{{ number_format(($girlsPass / $girlsPresent) * 100, 2) }}%</td>
            <td>{{ number_format(($boysPass / $boysPresent) * 100, 2) }}%</td>
            <td>{{ number_format((($girlsPass + $boysPass) / ($girlsPresent + $boysPresent)) * 100, 2) }}%</td>
        </tr>
        </tbody>
    </table>

    <!-- Grade Ranges -->
    <table>
        <thead>
        <tr>
            <th>Tranche de Moyennes</th>
            <th>Filles</th>
            <th>Garçons</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($gradeRanges as $range => $data)
            <tr>
                <td>{{ $range }}</td>
                <td>{{ $data['girls'] }}</td>
                <td>{{ $data['boys'] }}</td>
                <td>{{ $data['girls'] + $data['boys'] }}</td>
            </tr>
        @endforeach
        </tbody> 
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Fait à {{ $location }}, le {{ $date }}</p>
        <p><strong>Produit automatiquement par le système</strong></p>
    </div>
</div>
</body>
</html>
