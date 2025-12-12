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
				  <h3 class="box-title">Liste des Trimestres/Semestres</h3>
	<a href="{{ route('term.type.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Ajouter Trimestre/Semestre</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Nom</th> 
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $term )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $term->name }}</td>				 
				<td>
<a href="{{ route('term.type.edit',$term->id) }}" class="btn btn-info">Modifier</a>
<a href="{{ route('term.type.delete',$term->id) }}" class="btn btn-danger" id="delete">Supprimer</a>

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