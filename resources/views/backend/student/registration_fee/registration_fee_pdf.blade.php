<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frais d'Inscription de l'Élève</title>
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
            margin: 2px 0;
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
            <p>Adresse de l'École</p>
            <p>Téléphone : +228 9989 1236</p>
            <p>Email : support@grapmultlearning.com</p>
            <p><b> Frais d'Inscription de l'Élève </b></p>
        </div>
    </div>

    <!-- Details Table Section -->
    @php 
    $registrationfee = App\Models\FeeCategoryAmount::where('fee_category_id','7')
                        ->where('class_id',$details->class_id)
                        ->first();
    $originalfee = $registrationfee->amount;
    $discount = $details['discount']['discount'];
    $discounttablefee = $discount / 100 * $originalfee;
    $finalfee = (float)$originalfee - (float)$discounttablefee;
    @endphp 

    <table class="details-table">
        <tr>
            <th width="10%">#</th>
            <th width="45%">Détails de l'Élève</th>
            <th width="45%">Données de l'Élève</th>
        </tr>
        <tr>
            <td>1</td>
            <td><b>No ID de l'Élève</b></td>
            <td>{{ $details['student']['id_no'] }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><b>No D'Ordre</b></td>
            <td>{{ $details->roll }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td><b>Nom de l'Élève</b></td>
            <td>{{ $details['student']['name'] }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><b>Nom du Père</b></td>
            <td>{{ $details['student']['fname'] }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><b>Session</b></td>
            <td>{{ $details['student_year']['name'] }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td><b>Classe</b></td>
            <td>{{ $details['student_class']['name'] }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td><b>Frais d'Inscription</b></td>
            <td>{{ $originalfee }} FCFA</td>
        </tr>
        <tr>
            <td>8</td>
            <td><b>Frais de Réduction</b></td>
            <td>{{ $discount }} %</td>
        </tr>
        <tr>
            <td>9</td>
            <td><b>Frais Total de l'Élève</b></td>
            <td>{{ $finalfee }} FCFA</td>
        </tr>
    </table>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Duplicate Table for Receipt or Archive -->
    <table class="details-table">
        <tr>
            <th width="10%">#</th>
            <th width="45%">Détails de l'Élève</th>
            <th width="45%">Données de l'Élève</th>
        </tr>
        <tr>
            <td>1</td>
            <td><b>No ID de l'Élève</b></td>
            <td>{{ $details['student']['id_no'] }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><b>No D'Ordre</b></td>
            <td>{{ $details->roll }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td><b>Nom de l'Élève</b></td>
            <td>{{ $details['student']['name'] }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><b>Nom du Père</b></td>
            <td>{{ $details['student']['fname'] }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><b>Session</b></td>
            <td>{{ $details['student_year']['name'] }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td><b>Classe</b></td>
            <td>{{ $details['student_class']['name'] }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td><b>Frais d'Inscription</b></td>
            <td>{{ $originalfee }} FCFA</td>
        </tr>
        <tr>
            <td>8</td>
            <td><b>Frais de Réduction</b></td>
            <td>{{ $discount }} %</td>
        </tr>
        <tr>
            <td>9</td>
            <td><b>Frais Total de l'Élève</b></td>
            <td>{{ $finalfee }} FCFA</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Fait à Lomé, le : {{ date("d M Y") }}.
    </div>

</div>

</body>
</html>
