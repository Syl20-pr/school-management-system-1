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
				  <h3 class="box-title">Liste des Employés</h3> <a href="{{ route('employee.registration.list') }}" style="float: center;" class="btn btn-rounded btn-warning mb-5"> Telecharger La Liste en PDF</a>
	<a href="{{ route('employee.registration.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Ajouter Un(e) Employé(e)</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Nom</th> 
				<!-- <th>NO ID</th> -->
				<th>Contact</th>
				<th>Genre</th>
				{{-- <th>Date de Début de Contrat</th> --}}
				<!-- <th>Salaire</th> -->
				<th>Rôle</th>
				@if(Auth::user()->role == "Admin")
				<th>Code</th>
				 @endif
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $employee )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $employee->name }}</td>	
				<!-- <td> {{ $employee->id_no }}</td>	 -->
				<td> {{ $employee->mobile }}</td>	
				<td> {{ $employee->gender }}</td>	
				{{-- <td> {{ $employee->join_date }}</td> --}}	
				<!-- <td> {{ $employee->salary }}</td> -->
				<td> {{ $employee->role }}</td>
				@if(Auth::user()->role == "Admin")	
				<td> {{ $employee->code }}</td>	
				 @endif			 
				<td>
<a href="{{ route('employee.registration.edit',$employee->id) }}" class="btn btn-info">Modifier</a>
<a target="_blank" href="{{ route('employee.registration.details',$employee->id) }}" class="btn btn-danger">Détails</a>

				</td>
				 
			</tr>
			@endforeach
							 
						</tbody>
						<tfoot>
							 
						</tfoot>
					  </table>
					  <a href="{{ route('employee.registration.list') }}" class="btn btn-rounded btn-warning mb-5">Télécharger la Liste en PDF</a>
					</div>
				</div>
				<!-- /.box-body -->
			  </div>
			  <!-- /.box -->

			       
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>





@endsection