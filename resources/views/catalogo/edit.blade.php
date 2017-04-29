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