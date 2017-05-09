@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Aprobar Informe de Diario de Caja')

@section('content')

	<div class="row">
		<div class="col-md-8 col-md-offset-2">

			{{ Form::model($diariocaja, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('diariocajas.update', $diariocaja->id))) }}
				<fieldset>
					{{ csrf_field() }}

					<div class="col col-5 mensaje_1">
						<div class="alert alert-block alert-warning">
							<a class="close" data-dismiss="alert" href="#">×</a>
							<h4 class="alert-heading">Aprobar Informe de Diario de Caja General!</h4>
							Antes de aprobar un Informe de Caja General, debera revisar que los montos enumerados en el reporte coincidan con las monto que se encuentran en la Caja general al momento de aprobar. Si los monto no coinciden se debera hacer un ajuste de Diario de Caja General antes de aprobar. Atencion, una vez aprobado el informe no podra hacer ajustes.
						</div>
					</div>

					<section class="col col-5">
						<label>Aprobar?</label>
						<label class="toggle">
							<input type="checkbox" name="checkbox">
							<i data-swchon-text="SI" data-swchoff-text="NO"></i></label>
					</section>

				</fieldset>
				
				<div class="form-actions">
					{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
					<a href="{{ URL::route('diariocajas.index') }}" class="btn btn-large">Cancelar</a>
				</div>
			{{ Form::close() }}

		</div>
	</div>

@endsection