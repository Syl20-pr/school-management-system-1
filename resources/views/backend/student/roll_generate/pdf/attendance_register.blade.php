<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registre de Présence - {{ $class->name }} - {{ $year->name }}</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        body { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .school-name { 
            font-size: 14px; 
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title { 
            font-size: 12px; 
            margin: 3px 0;
            font-weight: bold;
        }
        .class-info { 
            font-size: 10px; 
            margin-bottom: 8px;
        }
        .statistics-line {
            text-align: center;
            margin-bottom: 10px;
            padding: 6px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .stat-item {
            display: inline-block;
            margin: 0 8px;
        }
        .stat-separator {
            display: inline-block;
            color: #999;
            margin: 0 5px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
            page-break-inside: auto;
        }
        th, td { 
            border: 1px solid #000; 
            padding: 2px;
            text-align: center;
            height: 15px;
        }
        th { 
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .student-info { 
            text-align: left;
            min-width: 120px;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .roll-column {
            width: 25px;
        }
        .gender-column {
            width: 25px;
            font-weight: bold;
        }
        .status-column {
            width: 20px;
            font-weight: bold;
        }
        .day-column { 
            width: 11px;
        }
        .signature { 
            margin-top: 20px;
            font-size: 9px;
            page-break-inside: avoid;
        }
        .month-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .footer {
            margin-top: 10px;
            font-size: 8px;
            text-align: center;
            color: #666;
        }
        .status-N {
            background-color: #e8f5e8; /* Vert clair pour Nouveau */
        }
        .status-D {
            background-color: #f5e8e8; /* Rouge clair pour Doublant */
        }
        .gender-F {
            background-color: #fce4ec; /* Rose clair pour Fille */
        }
        .gender-M {
            background-color: #e3f2fd; /* Bleu clair pour Garçon */
        }
    </style>
</head>
<body>
    @php
        // Calcul des statistiques
        $totalStudents = count($students);
        $girlsCount = 0;
        $boysCount = 0;
        $newStudents = 0;
        $repeatingStudents = 0;
        
        foreach ($students as $student) {
            $gender = $student->student->gender ?? '';
            $status = $student->student->statusclass ?? 'N';
            
            // Détection du genre
            if (strtolower($gender) === 'féminin' || strtolower($gender) === 'f' || strtolower($gender) === 'femme' || strtolower($gender) === 'fille') {
                $girlsCount++;
            } else {
                $boysCount++;
            }
            
            // Détection du statut
            if ($status === 'D') {
                $repeatingStudents++;
            } else {
                $newStudents++;
            }
        }
    @endphp

    <div class="header">
        <div class="school-name">LYCÉE NANEGBE</div>
        <div class="document-title">REGISTRE DE PRÉSENCE</div>
        <div class="class-info">Classe: {{ $class->name }} | Année scolaire: {{ $year->name }}</div>
        <div class="class-info">Mois: _______________________</div>
    </div>

    <!-- Statistiques de la classe - TOUT SUR UNE SEULE LIGNE -->
    <div class="statistics-line">
        <span class="stat-item">Effectif Total: {{ $totalStudents }}</span>
        <span class="stat-separator">|</span>
        <span class="stat-item">Filles: {{ $girlsCount }}</span>
        <span class="stat-separator">|</span>
        <span class="stat-item">Garçons: {{ $boysCount }}</span>
        <span class="stat-separator">|</span>
        <span class="stat-item">Nouveaux: {{ $newStudents }}</span>
        <span class="stat-separator">|</span>
        <span class="stat-item">Doublants: {{ $repeatingStudents }}</span>
    </div>

    <table>
        <thead>
            <tr class="month-header">
                <th class="roll-column" rowspan="2">N°</th>
                <th rowspan="2" style="min-width: 140px;">Nom et Prénom(s)</th>
                <th class="gender-column" rowspan="2">Sexe</th>
                <th class="status-column" rowspan="2">Statut</th>
                <th colspan="31">JOURS DU MOIS</th>
                <th rowspan="2" style="width: 35px;">Total<br>Absences</th>
            </tr>
            <tr class="month-header">
                @for($i = 1; $i <= 31; $i++)
                    <th class="day-column">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            @php
                $status = $student->student->statusclass ?? 'N';
                $statusClass = 'status-' . $status;
                
                $gender = $student->student->gender ?? '';
                $genderCode = 'M'; // Par défaut Garçon
                $genderClass = 'gender-M';
                
                if (strtolower($gender) === 'féminin' || strtolower($gender) === 'f' || strtolower($gender) === 'femme' || strtolower($gender) === 'fille') {
                    $genderCode = 'F';
                    $genderClass = 'gender-F';
                }
            @endphp
            <tr>
                <td class="roll-column">{{ $student->roll ?? $loop->iteration }}</td>
                <td class="student-info">{{ $student->student->name }}</td>
                <td class="gender-column {{ $genderClass }}">{{ $genderCode }}</td>
                <td class="status-column {{ $statusClass }}">{{ $status }}</td>
                @for($i = 1; $i <= 31; $i++)
                    <td class="day-column"></td>
                @endfor
                <td style="font-weight: bold;"></td>
            </tr>
            @endforeach
            
            <!-- Ligne pour le total des absences par jour -->
            <tr style="background-color: #f8f8f8;">
                <td colspan="4" style="text-align: right; font-weight: bold;">Total par jour:</td>
                @for($i = 1; $i <= 31; $i++)
                    <td class="day-column" style="font-weight: bold;"></td>
                @endfor
                <td style="font-weight: bold;"></td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <table style="width: 100%; border: none; margin-top: 20px;">
            <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <p>Fait à ____________________, le ____/____/________</p>
                    <p>Le Professeur Principal</p>
                    <p>Signature: _________________________</p>
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <p>Vu par le Proviseur(Directeur)</p>
                    <p>Signature: _________________________</p>
                    <p>Cachet de l'établissement</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Registre de présence - {{ $class->name }} - {{ $year->name }} - Page {{ $loop->iteration ?? 1 }}
        <br>Statut: N = Nouveau, D = Doublant | Genre: F = Fille, M = Garçon
    </div>
</body>
</html>