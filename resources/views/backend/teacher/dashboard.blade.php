@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
		 

		<!-- Main content -->
		<section class="content">
		  <div class="row">

		  	<div class="col-12">

			 <div class="box">
				<div class="box-header with-border">





<h1>Bienvenue, {{ $teacher->name }}</h1>

    <h2>Voici vos Matières Enseignées en fonction des Classes Tenus</h2>
    </div>
    <!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">

    @foreach($assignedSubjects as $assigned)
        <div>
            <h3>Matière: {{ $assigned->subject->name }} - Classe: {{ $assigned->class->name }}</h3>

            <h4>Liste des Élèves:</h4>
            <ul>
                @foreach($assigned->students as $studentAssignment)
                    <li>{{ $studentAssignment->student->name }} (Roll: {{ $studentAssignment->roll }})</li>
                @endforeach
            </ul>

        </div>

   	@endforeach

   				</div>
   			</div>
			  <!-- /.box -->

			       
			</div>
			<!-- /.col -->
		  </div>
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>










@endsection