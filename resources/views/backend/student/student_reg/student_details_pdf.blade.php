<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Élève</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 15px;
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

        /* Footer styles */
        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: right;
            color: #555;
        }

        /* Subtle hover effect */
        .details-table tr:hover td {
            background-color: #e8f5e9;
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
        </div>
    </div>

    <!-- Details Table -->
    <table class="details-table">
        <tr>
            <th width="5%">#</th>
            <th width="40%">Informations</th>
            <th width="55%">Détails</th>
        </tr>
        <tr>
            <td>1</td>
            <td><strong>Nom</strong></td>
            <td>{{ $details['student']['name'] }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td><strong>Numéro ID</strong></td>
            <td>{{ $details['student']['id_no'] }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td><strong>Rôle</strong></td>
            <td>{{ $details->roll }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td><strong>Nom du Père</strong></td>
            <td>{{ $details['student']['fname'] }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td><strong>Nom de la Mère</strong></td>
            <td>{{ $details['student']['mname'] }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td><strong>Contact</strong></td>
            <td>{{ $details['student']['mobile'] }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td><strong>Adresse</strong></td>
            <td>{{ $details['student']['address'] }}</td>
        </tr>
        <tr>
            <td>8</td>
            <td><strong>Genre</strong></td>
            <td>{{ $details['student']['gender'] }}</td>
        </tr>
        <tr>
            <td>9</td>
            <td><strong>Religion</strong></td>
            <td>{{ $details['student']['religion'] }}</td>
        </tr>
        <tr>
            <td>10</td>
            <td><strong>Date de Naissance</strong></td>
            <td>{{ $details['student']['dob'] }}</td>
        </tr>
        <tr>
            <td>11</td>
            <td><strong>Réduction</strong></td>
            <td>{{ $details['discount']['discount'] }}%</td>
        </tr>
        <tr>
            <td>12</td>
            <td><strong>Année Scolaire</strong></td>
            <td>{{ $details['student_year']['name'] }}</td>
        </tr>
        <tr>
            <td>13</td>
            <td><strong>Classe</strong></td>
            <td>{{ $details['student_class']['name'] }}</td>
        </tr>
        <tr>
            <td>14</td>
            <td><strong>Groupe</strong></td>
            <td>{{ $details['group']['name'] }}</td>
        </tr>
        <tr>
            <td>15</td>
            <td><strong>Passage</strong></td>
            <td>{{ $details['shift']['name'] }}</td>
        </tr>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        Fait à Lomé, le : {{ date("d M Y") }}.
    </div>
</div>

</body>
</html>
