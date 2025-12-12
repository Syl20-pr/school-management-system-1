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
				  <h3 class="box-title">Liste des Utilisateurs</h3>
	<a href="{{ route('users.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Ajouter un Utilisateur</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>
				<th>Role</th>
				<th>Nom</th>
				<th>E-mail</th>
				 <th>Code</th>
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $user )
			<tr>
				<td>{{ $key+1 }}</td>
				<!--<td> {{ $user->usertype }}</td>-->
				<td> {{ $user->role }}</td>
				<td>{{ $user->name }}</td>
				<td>{{ $user->email }}</td>
				<td>{{ $user->code }}</td>
				<td>
<a href="{{ route('users.edit',$user->id) }}" class="btn btn-info">Modifier</a>
<a href="{{ route('users.delete',$user->id) }}" class="btn btn-danger" id="delete">Supprimer</a>

				</td>
				 
			</tr>
			@endforeach
							 
						</tbody>
						<tfoot>
							 
						</tfoot>
					  </table>
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