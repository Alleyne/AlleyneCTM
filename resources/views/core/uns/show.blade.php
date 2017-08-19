@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Un expediente')

@section('content')

<!-- NEW WIDGET START -->
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="true" data-widget-deletebutton="false">
	<!-- widget options:
	usage: <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">

	data-widget-colorbutton="false"
	data-widget-editbutton="false"
	data-widget-togglebutton="false"
	data-widget-deletebutton="false"
	data-widget-fullscreenbutton="false"
	data-widget-custombutton="false"
	data-widget-collapsed="true"
	data-widget-sortable="false"-->

	<header>
		<span class="widget-icon"> <i class="fa fa-table"></i> </span>
		<h2>Detalles de la Unidad </h2>
		<div class="widget-toolbar">
			{{-- <a href="{{ URL::route('indexunall') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a> --}}
  		<a href="{{ url(Cache::get('goto_1')) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
		
		</div>
	</header>

	<div><!-- widget div-->
		<div class="jarviswidget-editbox"><!-- widget edit box -->
			<!-- This area used as dropdown edit box -->
		</div><!-- end widget edit box -->
		
		<div class="widget-body padding"><!-- widget content -->
				<div class="row">
				  <div class="col-md-6">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-md-4 control-label">Código</label>
								<div class="col-md-8">
									<input class="form-control input-lg" style="font-size:200% " name="numero" type="text" readonly value="{{ $dato->codigo }}">
								</div>
							</div>						
			
							<div class="form-group">
								<label class="col-md-4 control-label">Finca</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="finca" type="text" readonly value="{{ $dato->finca }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-4 control-label">Documento</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="documento" type="text" readonly value="{{ $dato->documento }}">
								</div>
							</div>					
							
							<div class="form-group">
								<label class="col-md-4 control-label">Características propias</label>
								<div class="col-md-8">
									{{ Form::textarea('caracteristicas', $dato->caracteristicas, array('class' => 'form-control input-sm', 'rows' => '2', 'readonly' => 'readonly')) }}
								</div>
							</div>	
							
							@if ($seccion->tipo != 3) <!-- Apartamentos -->
								<div class="form-group">
									<label class="col-md-4 control-label">No. de cuartos</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuartos }}">
									</div>
								</div>						
							@endif
							
							<div class="form-group">
								<label class="col-md-4 control-label">No. de baños</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->banos }}">
								</div>
							</div>	

							<div class="form-group">
								<label class="col-md-4 control-label">Agua caliente</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->agua_caliente  == 0 ? 'No' : 'Si'}}">
								</div>
							</div>											

							<div class="form-group">
								<label class="col-md-4 control-label">Estacionamientos</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->estacionamientos }}">
								</div>
							</div>						
							
							<div class="form-group">
								<label class="col-md-4 control-label">Cuota</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuota_mant }}">
								</div>
							</div>
											
							<div class="form-group">
								<label class="col-md-4 control-label">Área/m2</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->area }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-4 control-label">Aplica recargo</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->m_vence == 0 ? 'Mes corriente' : 'Proximo mes' }}">
								</div>
							</div>
							
							<div class="form-group">

								<label class="col-md-4 control-label">Despueés del día</label>

								<label class="col-md-4 control-label">Después del día</label>

								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->d_vence }}">
								</div>
							</div>							

							<div class="form-group">
								<label class="col-md-4 control-label">Meses para descuento</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->m_descuento }}">
								</div>
							</div>							
						</fieldset>
					</form>
				  </div>

				  <div class="col-md-6">
						<div class="row">
						  <div class="col-md-6">
						  	<a href="{{ URL::route('indexPagos', $dato->id) }}"><img src="{{asset('assets/backend/img/rpago.png') }}" alt="" style="width:110px;height:110px;border:0;"></a>
						  </div>
						  <div class="col-md-6">
						  	<a href="{{ URL::route('ecuentas', array($dato->id, 'backend')) }}"><img src="{{asset('assets/backend/img/ecuentas.png') }}" alt="" style="width:82px;height:82px;border:0;"></a>
						  </div>
						
						</div>
						
						<h1></h1>
						
						<div class="row">
						  <div class="col-md-6">
						  	<a href="{{ URL::route('indexprops', array($dato->id, $seccion->id)) }}"><img src="{{asset('assets/backend/img/propietario.png') }}" alt="" style="width:92px;height:92px;border:0;"></a>
						  </div>
						  <div class="col-md-6">
						  	<a href="{{ URL::route('uns.edit', $dato->id) }}"><img src="{{asset('assets/backend/img/edit.png') }}" alt="" style="width:82px;height:82px;border:0;"></a>
						  </div>
						</div>
						
						<legend></legend>
						<div class="row">
						  <div class="col-md-12">
                <table id="dt_basic" class="table table-hover">
                    <thead>
                        <tr>
                            <th>CÉDULA</th>
                            <th>NOMBRE</th>                          
                            <th>RESPONSABLE</th>       
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($props as $prop)
                            <tr>
                                <td col width="100px">{{ $prop->user->cedula }}</td>
                                <td>{{ $prop->user->fullname }}</td>
                                <td col width="10px">{{ $prop->encargado ? 'Si' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
						  </div>
						</div>
				  </div>
				</div>
		
		</div><!-- end widget content -->
	</div><!-- end widget div -->

</div>
<!-- end widget -->
<!-- WIDGET END -->
@stop