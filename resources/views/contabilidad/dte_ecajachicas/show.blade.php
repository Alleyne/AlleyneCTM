@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de Egreso de caja chica')

@section('content')

	<div class="well well-sm">
		<div class="card card-outline-danger text-center">
	    <h4 class="card-title">Factura por egreso de Caja Chica</h4>
			  <hr>
			  <div class="row">
			    <div class="col-md-3">
						Factura No: {{ $ecajachica->doc_no }}							
			    </div>
			    <div class="col-md-3">
			      A Favor de: {{ $ecajachica->afavorde }}
			    </div> 
			    <div class="col-md-3">
			      Saldo actual B/.: {{ $ecajachica->total }}
			    </div> 
			    <div class="col-md-3">
			    	Fecha: {{ $ecajachica->fecha }}
			    </div>
			  </div>				
		</div>	
	</div>

	<div class="well well-sm">	
		<div class="pull-right">
			<a href="{{ URL::route('ecajachicas.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
				@if ($ecajachica->etapa < 3)
					<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
						 Agregar Serviproducto
					</button>
				@endif
		</div>

		<table id="dt_basic" class="table table-hover">
			<thead>
				<tr>
					<th>ID</th>
					<th>SERVIPRODUCTO</th>
					<th>GASTO</th>
					<th>CANT</th>
					<th>PRECIO</th>
					<th>ITBMS</th>
					<th>TOTAL</th>

					@if ($ecajachica->etapa < 3)
						<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>	
					@endif
				</tr>
			</thead>
			<tbody>
				@foreach ($datos as $dato)
					<tr>
						<td col width="40px">{{ $dato->id }}</td>
						<td><strong>{{ $dato->nombre }}</strong></td>
						<td>{{ $dato->codigo }}</td>
						<td col width="60px">{{ $dato->cantidad }}</td>
						<td col width="60px">{{ $dato->precio }}</td>
						<td col width="60px">{{ $dato->itbms }}</td>
						<td col width="60px"><strong>{{ $dato->total }}</strong></td>
						@if ($ecajachica->etapa < 3)
							<td col width="40px" align="right">
								<ul class="demo-btns">
									<li>
										{{ Form::open(array(
											'route' => array('dte_ecajachicas.destroy', $dato->id),
											'method' => 'DELETE',
											'style' => 'display:inline'
											))
										}}

										{{ Form::button('Borrar', array(
											'class' => 'btn btn-danger btn-xs',
											'data-toggle' => 'modal',
											'data-target' => '#confirmDelete',
											'data-title' => 'Borrar detalle de egreso de caja chica',
											'data-message' => 'Esta seguro(a) que desea borrar el presente detalle de egreso de caja chica?',
											'data-btncancel' => 'btn-default',
											'data-btnaction' => 'btn-danger',
											'data-btntxt' => 'Borrar detalle'
											))
										}}

										{{ Form::close() }}
									</li>
								</ul>
							</td>
						@endif
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>		  
	
	<div class="invoice-footer">
		<div class="col-sm-12">
			<div class="invoice-sum-total pull-right">
				<h7><strong>Sub Total : <span>{{ number_format(floatval($subTotal),2) }}</span></strong></h7>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="invoice-sum-total pull-right">
				<h7><strong>Itbms : <span>{{ number_format(floatval($totalItbms),2) }}</span></strong></h7>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="invoice-sum-total pull-right">
				<h5><strong>Total : <span class="text-success">{{ number_format(floatval($subTotal + $totalItbms),2) }}</span></strong></h5>
			</div>
		</div>
	</div>

	<!-- Incluye la modal box -->
	@include('templates.backend._partials.modal_confirm')

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title" id="myModalLabel">Agregar serviproducto</h4>
				</div>
				<div class="modal-body">
	
					{{ Form::open(array('class' => 'form-horizontal', 'route' => 'dte_ecajachicas.store')) }}
						<fieldset>
							{{ Form::hidden('ecajachica_id', $ecajachica->id) }}
							
								<!-- Multiple Radios (inline) -->
								<div class="form-group">
								  <label class="col-md-3 control-label" for="radios">Tipo</label>
								  <div class="col-md-9"> 
								    <label class="radio-inline" for="radios-0">
								      <input type="radio" name="tipo_radios" id="tipo-1" value="0" checked="checked">
								      Producto
								    </label> 
								    <label class="radio-inline" for="radios-1">
								      <input type="radio" name="tipo_radios" id="tipo-2" value="1">
								      Servicio
								    </label>
								  </div>
								</div>

							<div class="form-group productos">
								<label class="col-md-3 control-label">Productos</label>
								<div class="col-md-9">
									{{ Form::select('producto_id', ['' => 'Escoja el producto que desea vincular!'] + $productos, 0, ['class' => 'form-control']) }}
									{!! $errors->first('producto_id', '<li style="color:red">:message</li>') !!}
								</div>
							</div>

							<div class="form-group servicios" style="display: none;">
								<label class="col-md-3 control-label">Servicios</label>
								<div class="col-md-9">
									{{ Form::select('servicio_id', ['' => 'Escoja el servicio que desea vincular!'] + $servicios, 0, ['class' => 'form-control']) }}
									{!! $errors->first('servicio_id', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							
							<div class="form-group cantidad">
								<label class="col-md-3 control-label">Cantidad</label>
								<div class="col-md-9">
									{{ Form::text('cantidad', old('cantidad'),
										array(
										    'class' => 'form-control',
										    'id' => 'cantidad',
										    'placeholder' => 'Escriba la cantidad ...',
												'autocomplete' => 'off',
										))
									}} 
									{!! $errors->first('cantidad', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Precio</label>
								<div class="col-md-9">
									{{ Form::text('precio', old('precio'),
										array(
										    'class' => 'form-control',
										    'id' => 'precio',
										    'placeholder' => 'Escriba el precio ...',
												'autocomplete' => 'off',
										))
									}} 
									{!! $errors->first('precio', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	
							
							<div class="form-group">
								<label class="col-md-3 control-label">Itbms</label>
								<div class="col-md-9">
									{{ Form::text('itbms', old('itbms'),
										array(
										    'class' => 'form-control',
										    'id' => 'itbms',
										    'placeholder' => 'Escriba el Itbms ...',
												'autocomplete' => 'off',
										))
									}} 
									{!! $errors->first('itbms', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	

						</fieldset>				
						
						<div class="form-actions">
							{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
							<button type="button" class="btn btn-default" data-dismiss="modal">
								Cancel
							</button>
						</div>
					{{ Form::close() }}
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
@stop

@section('relatedplugins')
  
  <script>	
	   $("#tipo-1").click(function(){
	       $(".productos").show();
	       $(".servicios").hide();
	       $(".cantidad").show();	   
	   });

	   $("#tipo-2").click(function(){
	       $(".productos").hide();
	       $(".servicios").show();
	       $(".cantidad").hide();	  
	   });

	</script>

@stop