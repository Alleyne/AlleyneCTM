@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Editar Organizacion')

@section('content')

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<h1>Editar Organización</h1>

			{{ Form::model($org, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('orgs.update', $org->id))) }}
						<fieldset>
							{{ csrf_field() }}
							<div class="form-group">
								<label class="col-md-2 control-label">Nombre</label>
								<div class="col-md-10">
									{{ Form::text('nombre', $org->nombre, array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">Tipo</label>
								<div class="col-md-10">
									{{ Form::text('tipo', $org->tipo, array('class' => 'form-control input-sm', 'title' => 'Escriba el tipo de organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('tipo', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">RUC</label>
								<div class="col-md-10">
									{{ Form::text('ruc', $org->ruc, array('class' => 'form-control input-sm', 'title' => 'Escriba el RUC de la organización...', 'autocomplete' => 'off')) }}

									{!! $errors->first('ruc', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">Dígito Verif.</label>
								<div class="col-md-10">
									{{ Form::text('digitov', $org->digitov, array('class' => 'form-control input-sm', 'title' => 'Escriba el dígito verificador de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('digitov', '<li style="color:red">:message</li>') !!}
								</div>
							</div>									
							<div class="form-group">
								<label class="col-md-2 control-label">País</label>
								<div class="col-md-10">
									{{ Form::text('pais', $org->pais, array('class' => 'form-control input-sm', 'title' => 'Escriba el país de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('pais', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">Provincia</label>
								<div class="col-md-10">
									{{ Form::text('provincia', $org->provincia, array('class' => 'form-control input-sm', 'title' => 'Escriba la provincia de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('provincia', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	
							<div class="form-group">
								<label class="col-md-2 control-label">Distrito</label>
								<div class="col-md-10">
									{{ Form::text('distrito', $org->distrito, array('class' => 'form-control input-sm', 'title' => 'Escriba el distrito de la Organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('distrito', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">Corregimiento</label>
								<div class="col-md-10">
									{{ Form::text('corregimiento', $org->corregimiento, array('class' => 'form-control input-sm', 'title' => 'Escriba el corregimiento de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('corregimiento', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	
							<div class="form-group">
								<label class="col-md-2 control-label">Comunidad</label>
								<div class="col-md-10">
									{{ Form::text('comunidad', $org->comunidad, array('class' => 'form-control input-sm', 'title' => 'Escriba la comunidad de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('comunidad', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	
							<div class="form-group">
								<label class="col-md-2 control-label">Teléfono</label>
								<div class="col-md-10">
									{{ Form::text('telefono', $org->telefono, array('class' => 'form-control input-sm', 'title' => 'Escriba el teléfono de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('telefono', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label">Celular</label>
								<div class="col-md-10">
									{{ Form::text('celular', $org->celular, array('class' => 'form-control input-sm', 'title' => 'Escriba el número celular de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('celular', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	
							<div class="form-group">
								<label class="col-md-2 control-label">Email</label>
								<div class="col-md-10">
									{{ Form::text('email', $org->email, array('class' => 'form-control input-sm', 'title' => 'Escriba el email de la organización...', 'autocomplete' => 'off')) }}
									{!! $errors->first('email', '<li style="color:red">:message</li>') !!}
								</div>
							</div>
							{{-- <div class="form-group">
								<label class="col-md-2 control-label">Image</label>
								<div class="col-md-10">
									{{ Form::text('imagen', $org->imagen, array('class' => 'form-control input-sm', 'title' => 'Escriba el celular de la organizacion...', 'autocomplete' => 'off')) }}
									{!! $errors->first('imagen', '<li style="color:red">:message</li>') !!}
								</div>
							</div>	 --}}
						</fieldset>
						
						<div class="form-actions">
							{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
							<a href="{{ URL::route('orgs.index') }}" class="btn btn-large">Cancelar</a>
						</div>
			{{ Form::close() }}

		</div>
	</div>

@endsection