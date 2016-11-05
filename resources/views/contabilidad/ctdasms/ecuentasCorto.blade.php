@extends('templates.frontend._layouts.default_1')

@section('main')

<div class="row">
  <div class="col-md-12">
	<h4><p class="text-center"><strong>ESTADO DE CUENTAS</strong></p></h4>
	<h7><p class="text-center"><strong>UNIDAD NO. {{ $data['unidad'] }}</strong></p></h7>		
	<h7><p class="text-center">al dia {{ $data['fecha'] }}</p></h7>
  </div>
</div>	

<!-- Columns are always 50% wide, on mobile and desktop -->
<div class="row">
  <div class="col-xs-6">
  		<address>
		  <strong>{{ $data['propnombre'] }}</strong><br>
		  795 Folsom Ave, Suite 600<br>
		  San Francisco, CA 94107<br>
		  <abbr title="Phone">P:</abbr> (123) 456-7890
		</address>
  </div>
</div>

<div class="row">
	<div class="col-xs-12">	
		<table class="table table-bordered table-striped">
			<thead>
				<strong><tr>
					<th col width="100px" class="text-center">Mes-Año</th>
					<th col width="75px" class="text-center">Importe</th>
					<th col width="65px" class="text-center">(+) Recargo</th>
					<th col width="65px" class="text-center">(-) Desc</th>
					<th col width="75px" class="text-right">Total a pagar</th>
				</tr></strong>
			</thead>
			<tbody>
				@foreach ($das as $da)
					<tr>
						@if($da->importe_pagado=='')	
							<td align="center"><mark>{{ $da->mes_anio }}</mark></td>								
						@else	
							<td align="center">{{ $da->mes_anio }}</td>									
						@endif

						<td align="center">{{ $da->importe }}</td>
						<td align="center">{{ $da->recargo }}</td>
						<td align="center">{{ $da->descuento }}</td>
						<td align="right">{{ $da->importe_a_pagar}}</td>	
					</tr>
				@endforeach
			</tbody>
		</table>	
	</div>
</div>	

<div class="row">
	<div class="col-xs-12">
		  <div class="col-xs-10">
		  	<p class="text-right"><strong>Total Adeudado</strong></p>
		  </div>
		  <div class="col-xs-2">
		  	<p class="text-right"><strong>{{ $data['total_adeudado'] }}</strong></p>
		  </div>
	</div>
  	<!-- <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK"> -->
	<hr class="divider">
	
	<div>
		<p class="text-center"><button class="btn-u btn-brd btn-brd-hover rounded-4x btn-u-yellow btn-u-sm" type="button"><i class="fa fa-envelope-o"></i> <a href="{{ URL::route('genera_estado_de_cuenta', array($da->un_id, 'completo')) }}"> Estado de Cuentas Completo</a></button></p>
	</div>
    
    <!-- Begin Content -->
    <div class="col-md-9">
        <!-- Accordion v1 -->                
        <div class="panel-group acc-v1" id="accordion-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-1" href="#collapse-One">
                            <strong> Ver Instructivo</strong>
                        </a>
                    </h4>
                </div>
                <div id="collapse-One" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Accordion v1 -->                
    </div>
    <!-- End Content -->
</div>
@stop