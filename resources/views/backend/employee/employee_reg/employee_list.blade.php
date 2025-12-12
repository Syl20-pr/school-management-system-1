<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Liste du Personnel Enseignant</title>
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
            font-size: 15px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .title-box {
            /*text-align: center;*/
            font-weight: bold;
            font-size: 12px;
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
            font-size: 12px;
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
    <!-- Header Section -->
    <div class="header">
        <!-- Left Text -->
        <div class="header-left-text">
            <p>LYCÉE DE NANEGBE</p>
            <p>Tél:+228 90296872, AGOÉ-NYIVE/TOGO</p>
        </div>

        <!-- Centered Content -->
        <img src="{{ public_path() . '/upload/Logo_Nanegbeoo.png' }}" alt="School Logo">
        <p><strong>L Y N A</strong></p>
        
        <h4> CODE</h4>
        

        <!-- Right Text -->
        <div class="header-right-text">
            <p>RÉPUBLIQUE TOGOLAISE</p>
            <p>Travail - Liberté - Patrie</p>
            <hr>
            <div style="border: 1px solid #000; padding: 10px; display: inline-block; "><h4>A/S: <b>2024-2025</b></h4></div> 
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>N<sup>o</sup>Ordre</th>
                <th>Nom & Prenoms</th>
                <th>Sexe</th>
                <th>Email</th>
                <th>Code Secret</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->gender === 'Féminin' ? 'F' : 'M' }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
