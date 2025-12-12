<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Scolaire</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7fc;
            color: #333;
        }

        /* Container for each student card */
        .student-card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #4CAF50;
            border-radius: 8px;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            background-color: white;
            position: relative;
        }

        /* Header with school info and logo */
        .card-header {
            display: flex;
            align-items: flex-start; /* Align items at the top */
            border-bottom: 1px solid #4CAF50;
            
        }

        .school-logo {
            width: 30px; /* Adjusted logo size */
            height: auto;
            margin-right: 10px; /* Space between logo and info */
            margin-top: 5px; /* Uniform margin top */

            
        }

        .school-info {
            flex: 1; /* Allow info to take remaining space */
            text-align: left; /* Align text to the left */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center vertically within available space */
            margin-top: 5px; /* Uniform margin top */
        }

        .school-info h2 {
            margin: 0;
            color: #4CAF50;
            font-size: 12px;
        }

        .school-info p {
            margin: 2px 0;
            font-size: 8px;
        }

        /* Student photo and info */
        .card-body {
            display: flex;
            align-items: center;
            margin-top: 10px; /* Space above the card body */
        }

        .student-photo {
            width: 23mm;
            height: 28mm;
            background-color: #ddd;
            border: 1px solid #999;
            text-align: center;
            line-height: 25mm;
            color: #666;
            font-size: 10px;
            margin-right: 50px; /* Space between photo and info */
            float:left;
        }

        .student-info {
            font-size: 10px;
            display: flex;
            flex-direction: column;
            text-align: left; /* Align text to the left */
        }

        .student-info div {
            margin-bottom: 5px;
        }

        /* Footer with print date */
        .footer {
            font-size: 8px;
            text-align: right;
            position: absolute;
            bottom: 5px;
            right: 10px;
            color: #555;
        }

        /* Print-specific settings */
        @media print {
            .student-card {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

@foreach($allData as $value)
<div class="student-card">
    <!-- Card Header with School Info and Logo -->
    <div class="card-header">
        <!-- <div class="school-info">
            <h2>Grapmult École</h2>
            <p>Adresse : Lomé, Togo</p>
            <p>Téléphone : +228 9989 1236</p>
            <p>Email : support@grapmultlearning.com</p>
        </div>  -->
        <div class="school-logo">
            <img src="{{ public_path() . '/upload/banner_id_card.png' }}" alt="Logo de l'école" width="350" height="auto">
        </div>
         
    </div>

    <!-- Student Photo and Info -->
    <div class="card-body">
        <div class="student-photo">
            <!-- <img src="{{ !empty($value['student']['image']) ? asset('upload/student_images/'.$value['student']['image']) : asset('upload/no_image.jpg') }}" style="width: 60px; height: 60px;"> -->
            <!-- <p>Student Image URL: {{ (!empty($value['student']['image'])) ? url('upload/student_images/'.$value['student']['image']) : url('upload/no_image.jpg') }}</p> -->
            <!-- <img src="{{ (!empty($value['student']['image'])) ? url('upload/student_images/'.$value['student']['image']) : url('upload/no_image.jpg') }}" style="width: 60px; height: 60px;"> -->
            <img src="{{ (!empty($value['student']['image'])) ? public_path('upload/student_images/'.$value['student']['image']) : public_path('upload/no_image.jpg') }}" style="width: 60px; height: 60px;">

       
 
        </div>
        <div class="student-info">
            <div><b>Nom :</b> {{ $value['student']['name'] }}</div>
            <div><b>Date & Lieu :</b> {{ $value['student']['dob'] }} à {{ $value['student']['lob'] }}  </div>
            <div><b>Genre :</b> {{ $value['student']['gender'] }}</div>
            <div><b>Classe :</b> {{ $value['student_class']['name'] }}</div>
            <div><b>Année :</b> {{ $value['student_year']['name'] }}</div>
            <div><b>Contact :</b> {{ $value['student']['mobile'] }}</div>
            <div><b>Personne à Prévenir :</b> {{ $value['student']['f_no'] }}</div>
            <!-- <div><b>Tirage :</b> {{ $value->roll }}</div>
            <div><b>ID :</b> {{ $value['student']['id_no'] }}</div> -->
        </div>
    </div>

</div>
@endforeach

</body>
</html>
