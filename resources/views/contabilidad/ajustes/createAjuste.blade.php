@extends('backend._layouts.default')

@section('main')<!-- MAIN PANEL -->
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-12">
	
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-orange" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
					<!-- widget options:
					usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
	
					data-widget-colorbutton="false"
					data-widget-editbutton="false"
					data-widget-togglebutton="false"
					data-widget-deletebutton="false"
					data-widget-fullscreenbutton="false"
					data-widget-custombutton="false"
					data-widget-collapsed="true"
					data-widget-sortable="false" -->
	
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Crear ajuste</h2>
					</header>
	
					<!-- widget div-->
					<div>

						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
						</div>
						<head>

							<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
						</head>
						<body>
						{!! Form::open(array('route' => 'ajustes.store')) !!}		
							{!! csrf_field() !!}
							{!! Form::hidden('periodo_id', $periodo_id) !!}
							<table>
								<thead>
									<tr>
										<th>CODIGO</th>
										<th>CUENTA</th>
										<th>DEBITO</th>
										<th>CREDITO</th>
									</tr>
								</thead>
								@foreach ($datos as $dato)
									<tr>
										<td col width="70px"><strong>{{ $dato->codigo}}</strong></td>
										<td col width="460px">{{ $dato->nombre}}</td>
										<input type="hidden" name="cuenta_{{ $dato->id }}" value="{{ $dato->id }}"/>
										<td col width="20px"><input class="debito" type="text" name="debito_{{ $dato->id }}"/></td>
										<td col width="20px"><input class="credito" type="text" name="credito_{{ $dato->id }}"/></td>
									</tr>
								@endforeach

								<tr id="summation">
									<td>&nbsp;</td>
									<td align="right"></td>
									<td align="center"><span id="sumDebito">0</span></td>
									<td align="center"><span id="sumCredito">0</span></td>
								</tr>
							</table>
							<br />	
							<div class="form-group">
								<label class="col-md-1 control-label">Descripción</label>
								<div class="col-md-11">
							        {!! Form::textarea('descripcion', old('descripcion'),
							        	array(
							        		'class' => 'form-control',
							        		'title' => 'Escriba la descripcion',
							        		'rows' => '2'
							        	)) !!}
							        {!! $errors->first('descripcion', '<li style="color:red">:message</li>') !!}   
								</div>
							</div>	
							<br />	
							<br />	
							<div class="form-actions">
								{!! Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) !!}
								<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>
							</div>
						{!! Form::close() !!}
						<script>
							$(document).ready(function(){
								$(".debito").each(function(){
									$(this).keyup(function(){
										calculateSumDebito();
									});
								});
							
								$(".credito").each(function(){
									$(this).keyup(function(){
										calculateSumCredito();
									});
								});
							});
							
							function calculateSumDebito(){
								var sum=0;
								$(".debito").each(function(){
									if(!isNaN(this.value)&&this.value.length!=0) {
										sum+=parseFloat(this.value);
									}
								});
								$("#sumDebito").html(sum.toFixed(2));
							}
							
							function calculateSumCredito(){
								var sum=0;
								$(".credito").each(function(){
									if(!isNaN(this.value)&&this.value.length!=0) {
										sum+=parseFloat(this.value);
									}
								});
								$("#sumCredito").html(sum.toFixed(2));
							}
						</script>
						</body>
						

					</div>
				</div>
					<!-- end widget div -->
				</div>
				<!-- end widget -->
			</article>
			<!-- WIDGET END -->
		</div>
	</section>
	<!-- end widget grid -->
@stop
