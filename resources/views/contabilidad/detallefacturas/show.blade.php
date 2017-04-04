@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de Egreso de factura')

@section('content')

<div class="well well-sm">
	<div class="card card-outline-danger text-center">
		    <h4 class="card-title">Egreso de Caja General</h4>
			  <div class="row">
			    <div class="col-md-2">
						12/12/2017
			    </div>
			    <div class="col-md-8">
			      Veneficiario: Mi compania
			    </div>
			    <div class="col-md-2">
						Factura no: 123456
			    </div>
			  </div>
	</div>
</div>

<div class="well well-sm">	
	<div class="pull-right">
		<a href="{{ URL::route('facturas.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
			@if ($factura->etapa < 2)
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

				@if ($factura->etapa < 2)
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
					@if ($factura->etapa < 3)
						<td col width="40px" align="right">
							<ul class="demo-btns">
								<li>
									{{ Form::open(array(
										'route' => array('detallefacturas.destroy', $dato->id),
										'method' => 'DELETE',
										'style' => 'display:inline'
										))
									}}

									{{ Form::button('Borrar', array(
										'class' => 'btn btn-danger btn-xs',
										'data-toggle' => 'modal',
										'data-target' => '#confirmDelete',
										'data-title' => 'Borrar detalle de egreso de factura',
										'data-message' => 'Esta seguro(a) que desea borrar el presente detalle de egreso de factura?',
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
	
					{{ Form::open(array('class' => 'form-horizontal', 'route' => 'detallefacturas.store')) }}
						<fieldset>
							{{ Form::hidden('factura_id', $factura->id) }}
							
							<div class="form-group">
								<label class="col-md-3 control-label">Serviproducto</label>
								<div class="col-md-9">
									{{ Form::select('serviproducto_id', ['' => 'Selecione un serviproducto ...'] + $serviproductos, 0, ['class' => 'form-control']) }}
									{!! $errors->first('serviproducto_id', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
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