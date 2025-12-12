<!DOCTYPE html>
<html>
<head>
<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
</head>
<body>


<table id="customers">
  <tr>
    <td><h2>
  <?php $image_path = '/upload/easyschool.png'; ?>
  <img src="{{ public_path() . $image_path }}" width="200" height="100">

    </h2></td>
    <td><h2>GRAPMULT ECOLE</h2>
<p>Adress</p>
<p>Phone : +22899891236</p>
<p>Email : support@grapmultlearning.com</p>
<p> <b>Rapport Résultat Des Élèves</b> </p>

    </td> 
  </tr>
  
   
</table>
 <br> <br>
 <strong>Résultat de : </strong> {{ $allData['0']['exam_type']['name'] }} 
 <br> <br>
<table id="customers">
   
  <tr>    
    <td width="50%"> <h4>Année Scolaire / Session : </h4> {{ $allData['0']['year']['name'] }} </td>
    <td width="50%"> <h4> Classe :  </h4>{{ $allData['0']['student_class']['name'] }} </td>
  </tr>

  
   
   
</table>
<br> <br>
  <i style="font-size: 10px; float: right;">Fait à Lomé, le : {{ date("d M Y") }}</i>

<hr style="border: dashed 2px; width: 95%; color: #000000; margin-bottom: 50px">

 
 

</body>
</html>