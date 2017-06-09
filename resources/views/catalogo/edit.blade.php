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
  					
	            <!-- Multiple Radios (inline) -->
	            <div class="form-group libroMasmenos">
	              <label class="col-md-3 control-label" for="radios">Grupo en conciliacion</label>
	              <div class="col-md-9"> 
	                <label class="radio-inline" for="radios-1">
	                  <input type="radio" name="concilia_radios" id="ninguan" value="1" checked="checked">
	                  Ninguno
	                </label> 
	                <label class="radio-inline" for="radios-2">
	                  <input type="radio" name="concilia_radios" id="nc" value="2">
	                  Nota de credito
	                </label>
	                <label class="radio-inline" for="radios-3">
	                  <input type="radio" name="concilia_radios" id="nd" value="3">
	                  Nota de debito
	                </label> 
	              </div>
	            </div>
  					</fieldset>
						
						<div class="form-actions">
				      {{Form::button('Salvar', array(
				          'class' => 'btn btn-success btn-large',
				          'data-toggle' => 'modal',
				          'data-target' => '#confirmAction',
				          'data-title' => 'Editar cuenta contable',
				          'data-message' => 'Esta seguro(a) que desea editar la cuenta contable?',
				          'data-btntxt' => 'SI, editar cuenta',
				          'data-btncolor' => 'btn-success'
				      ))}}
							<a href="{{ URL::route('catalogos.index') }}" class="btn btn-large">Cancelar</a>
						</div>
			{{ Form::close() }}
		</div>
	</div>

	<!-- Incluye la modal box -->
	@include('templates.backend._partials.modal_confirm')
@endsection
 
@section('relatedplugins')
	<!-- PAGE RELATED PLUGIN(S) -->
	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>
@stop