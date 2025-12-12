<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Bilan Bulletins Scolaire</title>
    <style>
        @page {
            size: A4 landscape; 
            margin: 0.5cm;
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
            max-width: 28cm;
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            box-sizing: border-box;
        }

        .header-logo {
            width: 40%;
            text-align: left;
        }

        .header-logo img {
            max-width: 120px;
            height: auto;
            margin-left: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        th, td {
            padding: 8px;
            border: 2px solid #000;
            font-size: 10px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .footer {
            font-size: 12px;
            text-align: right;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path() . '/upload/giant-logo.png' }}" alt="Logo de l'École">
        </div>
        <div class="header-info">
            <h2>LYCÉE NANEGBE</h2>
        </div>
    </div>

    <div class="container">
        <h4 class="box-title">Rapport Bilan du {{ $termTypeName }} de la Classe de {{ $className }}. A/S: {{ $yearName }}</h4>

        @php
            // Define thresholds
            $abandonThreshold = 3.0;
            $passingGrade = 10.0;

            // Filter out "ABANDON" students
            $validStudents = collect($studentData)->filter(function ($data) use ($abandonThreshold) {
                return isset($data['term_mean']) && is_numeric($data['term_mean']) && $data['term_mean'] > $abandonThreshold;
            });

            // Calculate total number of valid students
            $totalValidStudents = $validStudents->count();

            // Calculate overall success percentage for valid students
            $successfulStudents = $validStudents->filter(function ($data) use ($passingGrade) {
                return $data['term_mean'] >= $passingGrade;
            })->count();

            $successPercentage = $totalValidStudents > 0 
                ? round(($successfulStudents / $totalValidStudents) * 100, 2)
                : 0;

            // Calculate success percentage per subject for valid students
            $successPercentagePerSubject = [];

            foreach ($subjects as $subject) {
                $subjectId = $subject->school_subject->id;

                $passedStudents = $validStudents->filter(function ($student) use ($subjectId, $passingGrade) {
                    foreach ($student['subjects'] as $studentSubject) {
                        if ($studentSubject['subject_id'] === $subjectId && $studentSubject['mean_mark'] >= $passingGrade) {
                            return true;
                        }
                    }
                    return false;
                });

                $successPercentagePerSubject[$subject->school_subject->name] = $totalValidStudents > 0 
                    ? (count($passedStudents) / $totalValidStudents) * 100
                    : 0;
            }

            // Calculate class statistics (mean, highest, and lowest for valid students)
            $termMeans = $validStudents->pluck('term_mean')->toArray();
            $highestTermMean = !empty($termMeans) ? max($termMeans) : 'N/A';
            $lowestTermMean = !empty($termMeans) ? min($termMeans) : 'N/A';
            $classTermMean = !empty($termMeans) ? round(array_sum($termMeans) / count($termMeans), 2) : 'N/A';
        @endphp

        <!-- Table for Student Results -->
        <table border="1">
            <thead>
                <tr>
                    <th>Élèves</th>
                    @foreach($subjects as $subject)
                        <th>{{ $subject->school_subject->name }} (Moyenne)</th>
                        <th>Rang</th>
                    @endforeach
                    <th>Moyenne de Semestre</th>
                    <th>Rang de Classe</th>
                </tr>
            </thead>
            <tbody> 
                @foreach($validStudents as $data)
                    <tr @if($data['term_mean'] >= 10) style="background-color: #d4edda; color: #155724;" @endif>
                        <td><strong>{{ $data['student_name'] }}</strong></td>
                        @foreach($data['subjects'] as $subject)
                            <td>{{ isset($subject['mean_mark']) && is_numeric($subject['mean_mark']) ? number_format($subject['mean_mark'], 2) : 'N/A' }}</td>
                            <td>{{ $subject['rank'] ?? 'N/A' }} <sup>e</sup></td>
                        @endforeach
                        <td><strong>{{ number_format($data['term_mean'], 2) }}</strong></td>
                        <td><strong>{{ $data['term_rank'] ?? 'N/A' }}<sup>e</sup></strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td><strong>Plus Forte Moyenne: {{ round(floatval($highestTermMean),2) }}</strong></td>
                <td><strong>Plus Faible Moyenne: {{ round(floatval($lowestTermMean),2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Moyenne de la Classe: {{ round(floatval($classTermMean),2) }}</strong></td>
                <td></td>
            </tr>
        </table>

        <!-- Success Percentage Section -->
        <h5>Pourcentage de Réussite Global: {{ $successPercentage }}%</h5>
        <h5>Pourcentage de Réussite par Matière:</h5>
        <ul>
            @foreach($successPercentagePerSubject as $subjectName => $percentage)
                <li>{{ $subjectName }}: {{ round($percentage, 2) }}%</li>
            @endforeach
        </ul>
      
    </div>
    <p><a href="{{ route('report.bulletin.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-primary">
                        Télécharger PDF
                        </a></p>
                        <p><a href="{{ route('report.bulletin.class_rank_management.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-primary">
                        Télécharger le Classement de la Classe
                        </a></p>
                        <p><a href="{{ route('report.bulletin.mean_statistics.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-primary">
                        Télécharger Statistiques des Moyennes
                        </a></p>
                        <p><a href="{{ route('report.bulletin.subject_mean_statistics.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-primary">
                        Télécharger Statistiques des Moyennes par Matieres
                        </a></p>
                        <p><a href="{{ route('report.bulletin.marksheets.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-secondary">
                    Generer Les Bulletins Individuel PDF
                    </a>
                    </p>
                    <p>
                    <a href="{{ route('report.class.marksheets.download', ['year_id' => $yearId, 'class_id' => $classId, 'term_type_id' => $termTypeId]) }}" class="btn btn-primary">
                    Generate Class Marksheets PDF
                    </a>
                    </p>


<!-- ./wrapper -->
    
     
    <!-- Vendor JS -->
    <script src="{{asset('backend/js/vendors.min.js')}}"></script>
    <script src="{{asset('../assets/icons/feather-icons/feather.min.js')}}"></script>   
    <script src="{{asset('../assets/vendor_components/easypiechart/dist/jquery.easypiechart.js')}}"></script>
    <script src="{{asset('../assets/vendor_components/apexcharts-bundle/irregular-data-series.js')}}"></script>
    <script src="{{asset('../assets/vendor_components/apexcharts-bundle/dist/apexcharts.js')}}"></script>

  <script src="{{asset('../assets/vendor_components/datatable/datatables.min.js')}}"></script>
  <script src="{{asset('backend/js/pages/data-table.js')}}"></script>
    
    <!-- Sunny Admin App -->
    <script src="{{asset('backend/js/template.js')}}"></script>
    <script src="{{asset('backend/js/pages/dashboard.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


  <script type="text/javascript">
  $(function(){
    $(document).on('click','#delete',function(e){
        e.preventDefault();
        var link = $(this).attr("href");

  
                  Swal.fire({
                    title: 'Êtes-Vous Certain ??',
                    text: "Supprimer Ces Données?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, Supprimez-Le !'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = link
                      Swal.fire(
                        'Suprimé!',
                        'Votre Fichier a Été Supprimé.',
                        'success'
                      )
                    }
                  }) 


    });

  });


</script> 


  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>

<!-- JavaScript to Hide Button and Generate PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script>
    document.getElementById('downloadBtn').addEventListener('click', function() {
        // Hide the button before generating PDF
        document.querySelector('.btn-primary').style.display = 'none';

        // Generate the PDF
        html2pdf().from(document.body).save();

        // Optionally restore the button visibility after PDF generation
        document.querySelector('.btn-primary').style.display = 'block';
    });
</script>

</div>
</body>
</html>
