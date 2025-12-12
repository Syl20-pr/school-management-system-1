<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche de Notes - {{ $subject ?? 'Matière' }} - {{ $class->name }} - {{ $year->name }}</title>
    <style>
        @page {
            size: landscape;
            margin: 5mm;
        }
        body { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        .header { 
            text-align: center; 
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #333;
        }
        .school-name { 
            font-size: 12px; 
            font-weight: bold;
            margin-bottom: 3px;
        }
        .document-title { 
            font-size: 10px; 
            margin: 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .class-info { 
            font-size: 9px; 
            margin-bottom: 4px;
        }
        .subject-info {
            font-size: 10px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 3px;
            text-align: center;
            margin-bottom: 6px;
            border: 1px solid #ccc;
        }
        .statistics-line {
            text-align: center;
            margin-bottom: 8px;
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
            height: 16px;
        }
        th { 
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 8px;
        }
        .student-info { 
            text-align: left;
            min-width: 120px;
            max-width: 120px;
            padding-left: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 8px;
        }
        .roll-column {
            width: 25px;
            font-weight: bold;
        }
        .status-column {
            width: 20px;
            font-weight: bold;
        }
        .gender-column {
            width: 25px;
            font-weight: bold;
        }
        .evaluation-column {
            width: 22px;
        }
        .signature { 
            margin-top: 15px;
            font-size: 8px;
            page-break-inside: avoid;
        }
        .footer {
            margin-top: 10px;
            font-size: 7px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 3px;
        }
        .section-header {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        .teacher-instructions {
            margin-top: 6px;
            margin-bottom: 8px;
            padding: 4px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 2px;
            font-size: 7px;
        }
        /* Alternance de couleurs pour les lignes */
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        <div class="document-title">FICHE DE NOTES PAR MATIÈRE</div>
        <div class="class-info">Année scolaire: {{ $year->name }} | Classe: {{ $class->name }}</div>
        <div class="subject-info">Matière: {{ $subject ?? '_______________________' }} | Coef: __________</div>
        <div class="class-info">Période: _______________________ | Professeur: _______________________</div>
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

    <div class="teacher-instructions">
        <strong>Instructions:</strong> 
        Remplir les notes sur 20 points. 
        La moyenne des interrogations = (Int. 1 + Int. 2 + Int. 3) ÷ 3
        <br>Statut: N = Nouveau, D = Doublant | Genre: F = Fille, M = Garçon
    </div>

    <table>
        <thead>
            <tr>
                <th class="roll-column">N°</th>
                <th class="student-info">Nom et Prénom(s)</th>
                <th class="gender-column">Sexe</th>
                <th class="status-column">Statut</th>
                
                <!-- Interrogations -->
                <th colspan="3" class="section-header">INTERROGATIONS (/20)</th>
                
                <th class="section-header">MOYENNE<br>INTERROS</th>
                
                <!-- Devoir -->
                <th class="section-header">DEVOIR<br>(/20)</th>
                
                <!-- Composition -->
                <th class="section-header">COMPOSITION<br>(/20)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                
                <!-- Interrogations -->
                <th class="evaluation-column">Int. 1</th>
                <th class="evaluation-column">Int. 2</th>
                <th class="evaluation-column">Int. 3</th>
                
                <th class="evaluation-column">Moy. Int</th>
                
                <!-- Devoir -->
                <th class="evaluation-column">Devoir</th>
                
                <!-- Composition -->
                <th class="evaluation-column">Comp.</th>
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
                
                <!-- Interrogations -->
                <td class="evaluation-column"></td>
                <td class="evaluation-column"></td>
                <td class="evaluation-column"></td>
                
                <!-- Moyenne des interrogations -->
                <td class="evaluation-column" style="background-color: #f0f0f0;"></td>
                
                <!-- Devoir -->
                <td class="evaluation-column"></td>
                
                <!-- Composition -->
                <td class="evaluation-column"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; text-align: center;">
                    <p>Fait à ____________________, le ____/____/________</p>
                    <p>Le Professeur</p>
                    <p>Signature: _________________________</p>
                </td>
                <td style="width: 50%; border: none; text-align: center;">
                    <p>Vu par le Responsable Pédagogique(Directeur Pédagogique ou Censeur)</p>
                    <p>Signature: _________________________</p>
                    <p>Cachet de l'établissement</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Fiche de notes - {{ $subject ?? 'Matière' }} - {{ $class->name }} - {{ $year->name }}
        <br>Statut: N = Nouveau, D = Doublant | Genre: F = Fille, M = Garçon
    </div>
</body>
</html>