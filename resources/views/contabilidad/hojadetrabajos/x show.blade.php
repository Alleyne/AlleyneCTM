@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Hoja de Trabajo')

@section('content')
	<div class="row">
	  <div class="col-md-12"><strong><p class="text-center">SityWEB INC.</p></strong></div>
	  <div class="col-md-12"><strong><p class="text-center">HOJA DE TRABAJO PROYECTADA</p></strong></div>
	  <div class="col-md-12"><p class="text-center">Periodo contable del mes de {{ $periodo->periodo }}</p></div>
	  <div class="col-md-12"><p class="text-center">(En Balboas)</p></div>
	</div>

	<div class="row">
		<table class="table table-bordered table-striped">
			<thead class="thead-inverse">
				<tr>
					<th colspan="2" class="text-center"></th>
					<th colspan="2" class="text-center">Balance de pruebas</th>
					<th colspan="2" class="text-center">Ajustes</th>
					<th colspan="2" class="text-center">Balance Ajustado</th>
				</tr>
				<tr>
					<th col width="65px" class="text-center">CODIGO</th>
					<th>CUENTA</th>
					<th style="background:lightblue" col width="65px" class="text-center">DEBITO</th>
					<th style="background:lightblue" col width="65px" class="text-center">CREDITO</th>
					<th style="background:red" col width="65px" class="text-center">DEBITO</th>
					<th style="background:red" col width="65px" class="text-center">CREDITO</th>
					<th style="background:lightgreen" col width="65px" class="text-center">DEBITO</th>
					<th style="background:lightgreen" col width="65px" class="text-center">CREDITO</th>
					<th col width="70px" class="text-center"><i class="fa fa-gear fa-lg"></i></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($datos as $dato)
					<tr>
						<td align="center">{{ $dato['codigo'] }}</td>
						<td>{{ $dato['cta_nombre'] }}</td>
						<td align="right">{{ $dato['saldo_debito']==0 ? '' : number_format($dato['saldo_debito'],2) }}</td>  
						<td align="right">{{ $dato['saldo_credito']==0 ? '' : number_format($dato['saldo_credito'],2) }}</td>  
						
						<td align="right">{{ $dato['saldoAjuste_debito']==0 ? '' : number_format($dato['saldoAjuste_debito'],2) }}</td>
						<td align="right">{{ $dato['saldoAjuste_credito']==0 ? '' : number_format($dato['saldoAjuste_credito'],2) }}</td>
						
						<td align="right">{{ $dato['saldoAjustado_debito']==0 ? '' : number_format($dato['saldoAjustado_debito'],2) }}</td>
						<td align="right">{{ $dato['saldoAjustado_credito']==0 ? '' : number_format($dato['saldoAjustado_credito'],2) }}</td>
						<td col width="70px" align="right">
							<ul class="demo-btns">
								<li>
									<a href="{{ URL::route('verMayorAux',
																 array($dato['periodo'],
																		  $dato['cuenta'],
																			$dato['un_id']
																		)) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="glyphicon glyphicon-book"></i></a>
								</li>
							</ul>												
						</td>
					</tr>
				@endforeach
					<tr>
						<td></td>
						<td></td>
						<td align="right"><strong>{{ $totalDebito }}</strong></td>
						<td align="right"><strong>{{ $totalCredito }}</strong></td>	
						
						<td align="right"><strong>{{ $totalAjusteDebito==0 ? '' : $totalAjusteDebito }}</strong></td>	
						<td align="right"><strong>{{ $totalAjusteCredito==0 ? '' : $totalAjusteCredito }}</strong></td>	
	
						<td align="right"><strong>{{ $totalAjustadoDebito }}</strong></td>	
						<td align="right"><strong>{{ $totalAjustadoCredito }}</strong></td> 
						<td class="text-center">
							@if (Cache::get('esAdminkey') || Cache::get('esContadorkey'))
								@if ($permitirAjustes=='Si')
									<div class="col-md-4">
										<a href="{{ URL::route('createAjustes', $periodo->id) }}" class="btn bg-color-blue txt-color-white btn-xs"><i class="glyphicon glyphicon-wrench"></i></a>
									</div>
								@endif	
								
								@if ($permitirCerrar=='Si')
									<div class="col-md-4">
							        {{Form::open(array(
							          'route' => array('cierraPeriodo',$periodo->id, $periodo->periodo, $periodo->fecha),
							          'method' => 'GET',
							          'style' => 'display:inline'
							        ))}}
							        
							        {{Form::button('<i class="glyphicon glyphicon-lock"></i>', array(
							          'class' => 'btn btn-danger btn-xs',
							          'data-toggle' => 'modal',
							          'data-target' => '#confirmAction',
							          'data-title' => 'Cerrar periodo contable',
							          'data-message' => 'Esta seguro(a) que desea cerrar el presente periodo contable?',
							          'data-btntxt' => 'SI, cerrar periodo contable',
							          'data-btncolor' => 'btn-danger'
							        ))}}
							        {{Form::close()}}                                                    
									</div>
								@endif	
							@endif								
							
							@if ($totalAjustadoDebito==$totalAjustadoCredito) 
								<i style="color:green" class="glyphicon glyphicon-ok"></i> 
							@else
								<i style="color:red" class="glyphicon glyphicon-remove"></i>
							@endif	

						</td>
					</tr>
			</tbody>
		</table>	
    <!-- Incluye la modal box -->
    @include('templates.backend._partials.modal_confirm')
	</div><!-- end row -->
@stop

@section('relatedplugins')
    <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script> 
@stop