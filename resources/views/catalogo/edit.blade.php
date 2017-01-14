@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Editar cuenta')

@section('content')

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<h1>Editar Cuenta</h1>

			{{ Form::model($cuenta, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('catalogos.update', $cuenta->id))) }}
						<fieldset>
							{{ csrf_field() }}
							<div class="form-group">
								<label class="col-md-3 control-label">Nombre</label>
								<div class="col-md-9">
									{{ Form::text('nombre', $cuenta->nombre, array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre de la cuenta...', 'autocomplete' => 'off')) }}
									{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							
							@if ($cuenta->tipo=='1' || $cuenta->tipo=='2') 
								<div class="form-group">
									<label class="col-md-3 control-label">Corriente si no</label>
									<div class="col-md-9">
										{{ Form::checkbox('corriente_siono') }}
										{!! $errors->first('corriente_siono', '<li style="color:red">:message</li>') !!}
									</div>
								</div>
							@endif
							
							@if ($cuenta->tipo=='6') 	
								<div class="form-group">
									<label class="col-md-3 control-label">Nombre en factura</label>
									<div class="col-md-9">
										{{ Form::text('nombre_factura', $cuenta->nombre_factura, array('class' => 'form-control input-sm', 'title' => 'Descripcion en factura...', 'autocomplete' => 'off')) }}
										{!! $errors->first('nombre_factura', '<li style="color:red">:message</li>') !!}
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-md-3 control-label">Visible en factura</label>
									<div class="col-md-9">
										{{ Form::checkbox('enfactura') }}
										{!! $errors->first('enfactura', '<li style="color:red">:message</li>') !!}
									</div>
								</div>	
							@endif

							<div class="form-group">
								<label class="col-md-3 control-label">Estatus</label>
								<div class="col-md-9">
									{{ Form::checkbox('activa') }}
									{!! $errors->first('activa', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
						</fieldset>
						
						<div class="form-actions">
							{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
							<a href="{{ URL::route('catalogos.index') }}" class="btn btn-large">Cancelar</a>
						</div>
			{{ Form::close() }}

		</div>
	</div>

@endsection