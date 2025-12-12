<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Listes des Classes</title>
    <style>
        /* Page Styling */
        @page {
            size: A4;
            margin: 0.4cm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 19cm; /* Ensures content stays within A4 width */
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            box-sizing: border-box;
        }

        /* Header Styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-info {
            width: 60%;
        }

        .header-info h2 {
            margin: 0;
            color: #4CAF50;
            font-size: 28px;
        }

        .header-info p, .header-info h4, .header-info h6 {
            margin: 5px 0;
            font-size: 14px;
        }

        .header-logo img {
            max-width: 120px;
            height: auto;
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
            border: 1.5px solid #000;
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
    <div class="header-info">
            <h2>LYCEE DE NANEGBE</h2>
            <p>Année Scolaire {{ $students->first()->student_year->name ?? '' }} | <b>Classe {{ $students->first()->student_class->name ?? '' }}</b> | <b>Matière:</b></p>
            <!-- <p></p> -->
            <p>Nombre de Filles: {{ $femaleCount }} | Nombre de Doublant(e)s: {{ $statusclassCount }} | <b>Nom du Professeur:</b> </p>
            <!-- <p></p> -->
            <p><b>Liste de Classe | Fiche de Notes</b></p>
            
        </div>
    <table>
        <thead>
            <tr>
                <th>N<sup>o</sup>Ordre</th>
                <th>Nom & Prenoms</th>
                <th>Sexe</th>
                <th>Statut</th>
                <th>Note 1</th>
                <th>Devoir</th>
                <th>Compo.</th>
                <th>Mention</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as  $student)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $student->student->name }}</td>
                <td>{{ $student->student->gender === 'Féminin' ? 'F' : 'M' }}</td>
                <td>{{ $student->student->statusclass }}</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
