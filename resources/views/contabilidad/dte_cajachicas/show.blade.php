@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de Egreso de caja chica')

@section('content')

	<div class="well well-sm">
		<div class="card card-outline-danger text-center">
	    <h4 class="card-title">Diario de Caja Chica #{{ $cajachica->id }}</h4>
		  <div class="row">
		    <div class="col-md-4">
					Caja Chica No: {{ $cajachica->id }}							
		    </div>
		    <div class="col-md-4">
		      Responsable: {{ $cajachica->responsable }}
		    </div> 
		    <div class="col-md-4">
		    	Fecha: {{ $f_actual }}
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
					<th>DESCRIPCIÓN</th>
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