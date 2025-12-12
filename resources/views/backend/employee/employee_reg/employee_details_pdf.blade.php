<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement de l'Employé(e)</title>
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

        /* Header styling */
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
            <p><b>Page d'Enregistrement de l'Employé(e)</b></p>
        </div>
    </div>

    <!-- Employee Registration Details -->
    <table class="details-table">
        <tr>
            <th width="10%">#</th>
            <th width="45%">Détails de l'Employé(e)</th>
            <th width="45%">Données</th>
        </tr>
        <tr>
            <td>1</td>
            <td><b>Nom Complet</b></td>
            <td>{{ $details->name }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><b>ID No</b></td>
            <td>{{ $details->id_no }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td><b>Personne à Prévenir 1</b></td>
            <td>{{ $details->fname }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><b>Personne à Prévenir 2</b></td>
            <td>{{ $details->mname }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><b>Contact</b></td>
            <td>{{ $details->mobile }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td><b>Adresse</b></td>
            <td>{{ $details->address }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td><b>Genre</b></td>
            <td>{{ $details->gender }}</td>
        </tr>
        <tr>
            <td>8</td>
            <td><b>Religion</b></td>
            <td>{{ $details->religion }}</td>
        </tr>
        <tr>
            <td>9</td>
            <td><b>Date de Naissance</b></td>
            <td>{{ date('d-m-Y', strtotime($details->dob)) }}</td>
        </tr>
        <tr>
            <td>10</td>
            <td><b>Désignation</b></td>
            <td>{{ $details['designation']['name'] }}</td>
        </tr>
        <tr>
            <td>11</td>
            <td><b>Date du Contrat</b></td>
            <td>{{ date('d-m-Y', strtotime($details->join_date)) }}</td>
        </tr>
        <tr>
            <td>12</td>
            <td><b>Salaire</b></td>
            <td>{{ $details->salary }} FCFA</td>
        </tr>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        Fait à Lomé, le : {{ date("d M Y") }}.
    </div>

</div>

</body>
</html>
