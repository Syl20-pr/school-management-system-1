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
				  <h3 class="box-title"> Détails de Matière Affetée</h3>
	<a href="{{ route('assign.subject.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Ajouter Une Matière Affectée</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">

<h4><strong>Matière Affectée : </strong>{{ $detailsData['0']['student_class']['name'] }} </h4>					
					<div class="table-responsive">
					  <table class="table table-bordered table-striped">
						<thead class="thead-light">
			<tr>
				<th width="5%">SL</th>  
				<th width="20%">Matière</th> 
				<th width="20%">Note Maximale</th>
				<th width="20%">Note de Passage</th>
				<th width="20%">Coéfficient</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($detailsData as $key => $detail )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $detail['school_subject']['name'] }}</td>				 
				<td> {{ $detail->full_mark }}</td>
				<td> {{ $detail->pass_mark }}</td>
				<td> {{ $detail->subjective_mark }}</td>
				 
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