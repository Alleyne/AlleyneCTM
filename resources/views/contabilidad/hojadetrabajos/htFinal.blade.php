<!DOCTYPE html>
<html lang="en">

<head>
  <title>Hoja de trabajo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<link href="{{ URL::asset('assets/backend/css/bootstrap-3.3.7.min.css') }}" rel="stylesheet" type="text/css" media="screen">
	
	<style type="text/css">
		.borde {
			border: 1px; 
			border-style: solid; 
			border-color: #bfbfc8;
		}
		
		.celBg-green {
		  background-color: rgba(0, 255, 0, 0.14);
		}

		.celBg-red {
		  background-color: rgba(255, 0, 24, 0.08);
		}

		.celBg-yellow {
		    background-color: rgba(233, 233, 157, 0.21);
		}

		.celBg-blue {
		    background-color: rgba(12, 41, 249, 0.11);
		}
		
		.celBg-cian {
		    background-color: rgba(0, 244, 255, 0.14);
		}	

		.celBg-gray {
		    background-color: #bfbfc8;
		}

		p.mix {
		    border-style: solid hidden double hidden;
		    font-weight: bold;
		}
		
		p.lineup {
		    border-style: solid hidden hidden hidden;
		    font-weight: bold;
		}
		
		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		    padding: 2px;
		    line-height: 1.4;
		    vertical-align: top;
		    border-top: 0px solid #ddd;
		}
		
  	.rojo {
  		color:#FF0000;
  	}

	</style>
</head>

<body style="font-size:13px;"">
	<div class="container" style="width:13in; background-color:white";>

    <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px">HOJA DE TRABAJO FINAL</p>
			  <p class="text-center" style="margin:0px">Periodo contable del mes de {{ $periodo->periodo }}</p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
        <img style="margin-top:10px; border-radius: 4px;" src="{{ asset(Cache::get('jdkey')->imagen_M) }}" class="img-responsive" alt="Logo">
      </div>
    </div><!-- end row -->

		<table class="table table-hove table-hover">
		  <thead>
			  <tr>
			   <th class="hidden-print"></th>
			   <th col width="65px"></th> 
			   <th col width="395px"></th> 
			   <th colspan="2" class="text-center borde celBg-gray">Balance de pruebas</th> 
			   <th colspan="2" class="text-center borde celBg-gray">Ajustes</th> 
			   <th colspan="2" class="text-center borde celBg-gray">Balance Ajustado</th> 
			   <th colspan="2" class="text-center borde celBg-gray">Estado de Resultado</th> 
			   <th colspan="2" class="text-center borde celBg-gray">Balance General</th> 
			  </tr>
			  
			  <tr align="right">
			   <th col width="20px" class="hidden-print"></th>
			   <th>Codigo</th> 
			   <th>Cuenta</th> 
			   <th class="text-center borde celBg-gray">Debito</th> 
			   <th class="text-center borde celBg-gray">Credito</th> 
			   <th class="text-center borde celBg-gray">Debito</th> 
			   <th class="text-center borde celBg-gray">Credito</th> 	  
			   <th class="text-center borde celBg-gray">Debito</th> 
			   <th class="text-center borde celBg-gray">Credito</th> 
			   <th class="text-center borde celBg-gray">Debito</th> 
			   <th class="text-center borde celBg-gray">Credito</th> 
			   <th class="text-center borde celBg-gray">Debito</th> 
			   <th class="text-center borde celBg-gray">Credito</th> 
			  </tr>
		  </thead> 
		  
		  <tbody> 
				@foreach ($datos as $dato)		   

				  <tr> 
						<td class="hidden-print"><a href="{{ URL::route('verMayorAuxHis',
														 array($dato['periodo'],
																  $dato['cuenta'],
																	0 
																)) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="glyphicon glyphicon-book"></i></a>
						</td>

						<td>{{ $dato['codigo'] }}</td>
						<td>{{ $dato['cta_nombre'] }}</td>
					
						<td class="text-right celBg-yellow borde">
							{{ $dato['saldo_debito'] == '0.00' ? '' : number_format($dato['saldo_debito'],2) }}
						</td>
						
						<td class="text-right celBg-yellow borde">
							{{ $dato['saldo_credito'] == '0.00' ? '' : number_format($dato['saldo_credito'],2) }}
						</td>

						<td class="text-right celBg-red borde">
							{{ $dato['saldoAjuste_debito'] == '0.00' ? '' : number_format($dato['saldoAjuste_debito'],2) }}
						</td>
						
						<td class="text-right celBg-red borde">
							{{ $dato['saldoAjuste_credito'] == '0.00' ? '' : number_format($dato['saldoAjuste_credito'],2) }}
						</td>

						<td class="text-right celBg-green borde">
							{{ $dato['saldoAjustado_debito'] == '0.00' ? '' : number_format($dato['saldoAjustado_debito'],2) }}
						</td>
					    
						<td class="text-right celBg-green borde">
							{{ $dato['saldoAjustado_credito'] == '0.00' ? '' : number_format($dato['saldoAjustado_credito'],2) }}
						</td>
					    
						<td class="text-right celBg-blue borde">
							{{ $dato['er_debito'] == '0.00' ? '' : number_format($dato['er_debito'],2) }}
						</td>
					    
						<td class="text-right celBg-blue borde">
							{{ $dato['er_credito'] == '0.00' ? '' : number_format($dato['er_credito'],2) }}
						</td>
		                
						<td class="text-right celBg-cian borde">
							{{ $dato['bg_debito'] == '0.00' ? '' : number_format($dato['bg_debito'],2) }}
						</td>
					    
						<td class="text-right celBg-cian borde">
							{{ $dato['bg_credito'] == '0.00' ? '' : number_format($dato['bg_credito'],2) }}
						</td>
					</tr>
				@endforeach

				<tr> 
					<td class="hidden-print">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td> 
					<td class="text-right"><p class="mix">{{ number_format($total_bp_debito,2) }}</p></td> 
					<td class="text-right"><p class="mix">{{ number_format($total_bp_credito,2) }}</p></td> 
					<th class="text-right"><p class="mix">{{ number_format($total_aj_debito,2) }}</p></td> 
					<td class="text-right"><p class="mix">{{ number_format($total_aj_credito,2) }}</p></td> 
					<td class="text-right"><p class="mix">{{ number_format($total_ba_debito,2) }}</p></td> 
					<td class="text-right"><p class="mix">{{ number_format($total_ba_credito,2) }}</p></td> 
					<th class="text-right"><p class="mix lineup">{{ number_format($total_er_debito,2) }}</p></td> 
					<td class="text-right"><p class="mix lineup">{{ number_format($total_er_credito,2) }}</p></td> 
					<td class="text-right"><p class="mix lineup">{{ number_format($total_bg_debito,2) }}</p></td> 
					<td class="text-right"><p class="mix lineup">{{ number_format($total_bg_credito,2) }}</p></td> 
				</tr>		  

				<tr> 
					<td class="hidden-print"></td>
					<td></td> 
					<td>Utilidad del periodo</td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 

					@if ($utilidad > 0)
						<td class="text-right"><strong>{{ number_format($utilidad,2) }}</strong></td> 
						<td class="text-right">&nbsp;</td> 
					@else
						<td class="text-right">&nbsp;</td> 
						<td class="text-right rojo"><strong>{{ number_format(abs($utilidad),2) }}</strong></td> 
					@endif
					
					@if ($utilidad > 0)
						<td class="text-right">&nbsp;</td> 
						<td class="text-right"><strong>{{ number_format($utilidad,2) }}</strong></td> 
					@else
						<td class="text-right rojo"><strong>{{ number_format(abs($utilidad),2) }}</strong></td> 
						<td class="text-right">&nbsp;</td> 
					@endif 
				</tr>	
				
				<tr> 
					<td class="hidden-print"></td> 
					<td></td>
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
					<td></td> 
						
					@if ($utilidad > 0)
						<th class="text-right"><strong><p class="mix">{{ number_format(($total_er_debito+$utilidad),2) }}</p></strong></td> 
						<th class="text-right"><strong><p class="mix">{{ number_format($total_er_credito,2) }}</p></strong></td> 
					@else
						<th class="text-right"><strong><p class="mix">{{ number_format($total_er_debito,2) }}</p></strong></td> 
						<th class="text-right"><strong><p class="mix">{{ number_format(($total_er_credito+abs($utilidad)),2) }}</p></strong></td> 
					@endif
					
					@if ($utilidad > 0)
						<th class="text-right"><strong><p class="mix">{{ number_format($total_bg_debito,2) }}</p></strong></td> 
						<th class="text-right"><strong><p class="mix">{{ number_format(($total_bg_credito+$utilidad),2) }}</p></strong></td> 
					@else
						<th class="text-right"><strong><p class="mix">{{ number_format(($total_bg_debito+abs($utilidad)),2) }}</p></strong></td> 
						<th class="text-right"><strong><p class="mix">{{ number_format($total_bg_credito,2) }}</p></strong></td> 
					@endif 
				</tr>		 
		  </tbody>
		</table>
    
    <!-- Incluye la modal box -->
    @include('templates.backend._partials.modal_confirm')
    
    <div class="row">
      <div class="col-xs-12">
        <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
      </div>
    </div> 

	</div> <!-- end container -->

  <script src="{{ URL::asset('assets/backend/js/libs/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap-3.3.7.min.js') }}"></script>
  <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script> 

</body>
</html>
