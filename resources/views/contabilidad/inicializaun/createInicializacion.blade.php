@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Inicializar Unidad')

@section('content')
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-12">
	
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0">
	
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Formunlario para inicializar una Unidad</h2>
					</header>
	
					<!-- widget div-->
					<div>
	
						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
	
						</div>
						<!-- end widget edit box -->
	
							<!-- widget content -->
							<div class="widget-body">
							{{ Form::open(['class' => 'form-horizontal', 'route' => 'storeInicializacion']) }}
									<fieldset>
	 									{{ csrf_field() }}
	 									{{ Form::hidden('un_id', $un_id) }}
										
										<div class="col-sm-12 mensaje_1">
											<div class="alert alert-block alert-warning">
												<a class="close" data-dismiss="alert" href="#">×</a>
												<h4 class="alert-heading">Inicializar una unidad con deuda acumulada!</h4>
												Si una unidad tiene una deuda acumulada en cuotas de mantenimiento regular o extraordinarias, recargos, multas y otros, se debera calcular el monto total y conversar con el propietario para acordar un arreglo de pago. En el arreglo de pago se dividira la totalidad de la deuda en meses para que sea mas comodo para el propietario cancelar la totalidad de la deuda. Esta deuda se cargara a la cuenta de Cuotas de mantinimiento por cobrar para facilitar el proceso de inicializacion y cobro de la misma.
											</div>
										</div>
										
                    <div class="form-group">
                        <label class="col-md-3 control-label">Total de meses</label>
                        <div class="col-md-9">
                            <input class="form-control spinner-left"  id="spinner3" name="meses" value= "1" min="1" max="6" type="number">
                            <p class="text-left">Escriba en numero de meses acordados con el propietario para cancelar la deuda! (1 a 6 meses) </p>
                            {!! $errors->first('meses', '<li style="color:red">:message</li>') !!}
                        </div>
                    </div>


										<div class="form-group">
											<label class="col-md-3 control-label">Monto total adeudado</label>
											<div class="col-md-9">
												{{ Form::text('monto', old('monto'),
													array(
													    'class' => 'form-control',
													    'id' => 'monto',
													    'placeholder' => 'Escriba el monto total adeudado por el propietario!',
														'autocomplete' => 'off'
													))
												}} 
												{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										
										<hr>
										
										<div class="col-sm-12">
											<div class="alert alert-block alert-warning">
												<a class="close" data-dismiss="alert" href="#">×</a>
												<h4 class="alert-heading">Inicializar una unidad con pagos anticipados!</h4>
												Si una unidad presenta pagos anticipados, se debera registrar la totalidad. Este monto sera utilizado por el sistema para cobrar o completar pagos ya se de Cuotas de mantenimiento regular o extraordinaria y recargos en un futuro. Se considera que este dinero ya esta depositado en la cuenta de banco.
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Total de pagos anticipados</label>
											<div class="col-md-9">
												{{ Form::text('anticipados', old('anticipados'),
													array(
													    'class' => 'form-control',
													    'id' => 'anticipados',
													    'placeholder' => 'Escriba el monto total en pagos anticipados!',
														'autocomplete' => 'off'
													))
												}} 
												{!! $errors->first('anticipados', '<li style="color:red">:message</li>') !!}
											</div>
										</div>		
									</fieldset>

									<div class="form-actions">
										{{ Form::submit('Inicializar unidad', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>
									</div>
								{{ Form::close() }}
							</div>
							<!-- end widget content -->
					</div>
					<!-- end widget div -->
				</div>
				<!-- end widget -->
			</article>
			<!-- WIDGET END -->
		</div>
	
		<!-- end row -->
	
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
	<script type="text/javascript">
	  $(document).ready(function(){
	    $("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    })
	  })
    
    // Spinners
    $("#spinner3").spinner({
        min: 1,
        max: 6,
        step: 1,
        start: 1,
        numberFormat: "C"
    }); 

	</script>
@stop