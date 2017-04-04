@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de Egreso de caja chica')

@section('content')

	<div class="well well-sm">
		<div class="card card-outline-danger text-center">
	    <h4 class="card-title">Diario de Caja Chica</h4>
		  <div class="row">
		    <div class="col-md-2">
					12/12/2017
		    </div>
		    <div class="col-md-8">
		      xxxxxxxxxxxxxxxxxxx
		    </div>
		    <div class="col-md-2">
					xxxxxxxxxxxxxxxxx
		    </div>
		  </div>		
		</div>	
	</div>

	<div class="well well-sm">	
		<div class="pull-right">
			<a href="{{ URL::route('cajachicas.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
		</div>

		<table id="dt_basic" class="table table-hover">
			<thead>
				<tr>
					<th>FECHA</th>
					<th>DESCRIPCION</th>
					<th>DOC_NO</th>
					<th>AUME</th>
					<th>DISM</th>
					<th>SALDO</th>
					<th>APRUEBA</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($datos as $dato)
						<td col width="80px">{{ $dato->fecha }}</td>
						<td col width="400px">{{ $dato->descripcion }}</td>
						<td col width="60px">{{ $dato->doc_no }}</td>
						<td col width="60px">{{ $dato->aumenta }}</td>
						<td col width="60px">{{ $dato->disminuye }}</td>
						<td col width="60px">{{ $dato->saldo }}</td>
						<td col width="120px">{{ $dato->aprueba }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>		  

@stop