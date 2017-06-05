@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Aprobar Informe de Diario de Caja')

@section('content')

	<div class="row">
		<div class="col-md-10 col-md-offset-1">

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

					<!-- Multiple Radios (inline) -->
					<div class="form-group">
					  <label class="col-md-4 control-label" for="radios">Resultado del arqueo de Caja general:</label>
					  <div class="col-md-8"> 
					    <label class="radio-inline" for="radios-1">
					      <input type="radio" name="arqueocc_radios" id="arqueocc-1" value="1" checked="checked">
					      Arqueo balanceado
					    </label> 
					    <label class="radio-inline" for="radios-2">
					      <input type="radio" name="arqueocc_radios" id="arqueocc-2" value="2">
					      Arqueo con faltante
					    </label>
					    <label class="radio-inline" for="radios-3">
					      <input type="radio" name="arqueocc_radios" id="arqueocc-3" value="3">
					      Arqueo con sobrante
					    </label>
					  </div>
					</div>
					
					<div class="form-group montofaltante" style="display: none;">
						<label class="col-md-3 control-label">Monto faltante</label>
						<div class="col-md-9">
							{{ Form::text('montofaltante', old('montofaltante'),
								array(
								    'class' => 'form-control',
								    'id' => 'montofaltante',
								    'placeholder' => 'Escriba monto faltante...',
									'autocomplete' => 'off',
								))
							}} 
							{!! $errors->first('montofaltante', '<li style="color:red">:message</li>') !!}
						</div>
					</div>										
					
					<div class="form-group montosobrante" style="display: none;">
						<label class="col-md-3 control-label">Monto sobrante</label>
						<div class="col-md-9">
							{{ Form::text('montosobrante', old('montosobrante'),
								array(
								    'class' => 'form-control',
								    'id' => 'montosobrante',
								    'placeholder' => 'Escriba monto sobrante...',
									'autocomplete' => 'off',
								))
							}} 
							{!! $errors->first('montosobrante', '<li style="color:red">:message</li>') !!}
						</div>
					</div>

					<hr>

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

@section('relatedplugins')

<script type="text/javascript">
// DO NOT REMOVE : GLOBAL FUNCTIONS!
$(document).ready(function() {
	pageSetUp();
	
   $("#arqueocc-1").click(function(){
      $(".montofaltante").hide();
      $(".montosobrante").hide();
   });

   $("#arqueocc-2").click(function(){
      $(".montofaltante").show();
      $(".montosobrante").hide();
   });
   
   $("#arqueocc-3").click(function(){
      $(".montofaltante").hide();
      $(".montosobrante").show();
   });

  $("input[type='submit']").attr("disabled", false);
  $("form").submit(function(){
    $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
    return true;
  });
})

</script>
@stop