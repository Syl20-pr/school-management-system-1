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
				  <h3 class="box-title"> Détails de Prof. Affetée</h3>
	<a href="{{ route('assign.designation.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Ajouter un(e) Prof à une Désignation</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">

<h4><strong>Prof Affecté(e)s : </strong>{{ $detailsData['0']['designation']['name'] }} </h4>					
					<div class="table-responsive">
					  <table class="table table-bordered table-striped">
						<thead class="thead-light">
			<tr>
				<th width="5%">SL</th>  
				<th width="30%">Nom</th> 
				<th width="50%">Commentaire</th>
			</tr>
		</thead>
		<tbody>
			@foreach($detailsData as $key => $detail )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $detail['teacher_name']['name'] }}</td>				 
				<td> {{ $detail->comment }}</td>
				 
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