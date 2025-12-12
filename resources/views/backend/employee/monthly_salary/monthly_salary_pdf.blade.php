<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Salaire</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7fc;
            color: #333;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
            height: auto;
        }

        .header-info h2 {
            margin: 0;
            color: #4CAF50;
            font-size: 28px;
        }

        .header-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        /* Table styling */
        .details-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .details-table th, .details-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .details-table th {
            background-color: #4CAF50;
            color: white;
            text-align: left;
        }

        .details-table td {
            background-color: #f9f9f9;
        }

        .details-table tr:nth-child(even) td {
            background-color: #f0f7f0;
        }

        .details-table tr:hover td {
            background-color: #e8f5e9;
        }

        /* Footer */
        .footer {
            font-size: 12px;
            text-align: right;
            margin-top: 50px;
            color: #555;
        }

        .divider {
            border: dashed 2px #4CAF50;
            margin: 40px 0;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path() . '/upload/giant-logo.png' }}" alt="Logo de l'École">
        </div>
        <div class="header-info">
            <h2>ECOLE GRAPMULT</h2>
            <p>Adresse</p>
            <p>Téléphone : +228 9989 1236</p>
            <p>Email : support@grapmultlearning.com</p>
            <p><b>Salaire Mensuel</b></p>
        </div>
    </div>

    <!-- Salary Details -->
    @php 
    $date = date('Y-m', strtotime($details['0']->date));
    if ($date != '') {
        $where[] = ['date', 'like', $date . '%'];
    }

    $totalattend = App\Models\EmployeeAttendance::with(['user'])
                    ->where($where)
                    ->where('employee_id', $details['0']->employee_id)
                    ->get();

    $salary = (float)$details['0']['user']['salary'];
    $salaryperday = $salary / 30;
    $absentcount = count($totalattend->where('attend_status', 'Absent'));
    $totalsalaryminus = $absentcount * $salaryperday;
    $totalsalary = $salary - $totalsalaryminus;
    @endphp 

    <table class="details-table">
        <tr>
            <th width="10%">#</th>
            <th width="45%">Détails</th>
            <th width="45%">Données</th>
        </tr>
        <tr>
            <td>1</td>
            <td><b>Nom</b></td>
            <td>{{ $details['0']['user']['name'] }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><b>Salaire de Base</b></td>
            <td>{{ $details['0']['user']['salary'] }} FCFA</td>
        </tr>
        <tr>
            <td>3</td>
            <td><b>Absence Totale du Mois</b></td>
            <td>{{ $absentcount }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><b>Mois</b></td>
            <td>{{ date('M Y', strtotime($details['0']->date)) }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><b>Salaire Mensuel du Mois</b></td>
            <td>{{ $totalsalary }} FCFA</td>
        </tr>
    </table>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Duplicate Table for Employee Archive -->
    <table class="details-table">
        <tr>
            <th width="10%">#</th>
            <th width="45%">Détails</th>
            <th width="45%">Données</th>
        </tr>
        <tr>
            <td>1</td>
            <td><b>Nom</b></td>
            <td>{{ $details['0']['user']['name'] }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><b>Salaire de Base</b></td>
            <td>{{ $details['0']['user']['salary'] }} FCFA</td>
        </tr>
        <tr>
            <td>3</td>
            <td><b>Absence Totale du Mois</b></td>
            <td>{{ $absentcount }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><b>Mois</b></td>
            <td>{{ date('M Y', strtotime($details['0']->date)) }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><b>Salaire Mensuel du Mois</b></td>
            <td>{{ $totalsalary }} FCFA</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Fait à Lomé, le : {{ date("d M Y") }}.
    </div>

</div>

</body>
</html>
