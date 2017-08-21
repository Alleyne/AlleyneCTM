<!DOCTYPE html>
<html lang="en">

<head>
  <title>Balance General Proyectado</title>
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

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

    <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px">BALANCE GENERAL PROYECTADO</p>
			  <p class="text-center" style="margin:0px">Periodo contable del mes de {{ $periodo->periodo }}</p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
         <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width="70px" alt="Responsive image">
      </div>
    </div><!-- end row -->
		
		<br />
		
		<table class="table table-hove table-hover celBg-yellow">
		  <thead>
			  <tr col width="10px"></tr>

			  <tr>
			   <th colspan="12" class="text-center borde celBg-gray">ACTIVOS</th> 
			  </tr>
			  
			  <tr>
			   <th colspan="4" class="text-left borde celBg-gray">&nbsp;<strong>Activos liquidos</strong></th> 
			  </tr>
		  </thead> 			  
		  <tbody> 						
			  <tr>
			   <td col width="10px"></td>
			   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;Banco</td> 
			   <td col width="100px" class="text-right borde">{{ number_format($saldoBanco,2) }}</td> 
			   <td col width="100px" class="text-right"></td> 			  
			   <td col width="100px"></td> 
			  </tr>

			  <tr>
			   <td col width="10px"></td>		   
			   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;Caja general</td> 
			   <td col width="100px" class="text-right borde">{{ number_format($cgeneralSaldo,2) }}</td> 
			   <td col width="100px" class="text-right"></td> 			  
			   <td col width="100px"></td> 
			  </tr>
		  
			  <tr>
			   <td col width="10px"></td>		   
			   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;Caja menuda</td> 
			   <td col width="100px" class="text-right borde">{{ number_format($cchicaSaldo,2) }}</td> 
			   <td col width="100px" class="text-right borde"><strong>{{number_format($totalLiquido,2) }}</strong></td> 			  
			   <td col width="100px"></td> 
			  </tr>
		  </tbody>
		</table>

		<table class="table table-hove table-hover celBg-yellow">
		  <thead>
			  <tr col width="10px"></tr>
			  <tr></tr>
			  <tr>
			   <th colspan="4" class="text-left borde celBg-gray">&nbsp;<strong>Cuentas por cobrar</strong></th> 
			  </tr>
		  </thead> 			  
		  <tbody> 						

				@foreach ($bloques as $bloque)
	  
				  <tr>
				   <td col width="10px"></td>
				   <td col width="210px" colspan="3" class="text-left borde">&nbsp;&nbsp;&nbsp;<strong>{{ $bloque->nombre }}</strong></td> 
				   <td col width="100px"></td>  
				   <td col width="100px"></td> 			  
				  </tr>

				  <tr>
				   <td col width="10px"></td>		   
				   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cuota regular</td> 
				   <td col width="100px" class="text-right borde">{{ number_format($bloque->cuotaRegPorCobrar,2) }}</td> 
				   <td col width="100px"></td> 			  
				   <td col width="100px"></td> 
				  </tr>
				  
				  <tr>
				   <td col width="10px"></td>		   
				   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cuota extraordinaria</td> 
				   <td col width="100px" class="text-right borde">{{ number_format($bloque->cuotaExtraPorCobrar,2) }}</td> 
				   <td col width="100px"></td> 			  
				   <td col width="100px"></td> 
				  </tr>

				  <tr>
				   <td col width="10px"></td>		   
				   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Recargos</td> 
				   <td col width="100px" class="text-right borde">{{ number_format($bloque->recargoPorCobrar,2) }}</td> 
				   <td col width="100px"></td> 			  
				   <td col width="100px"></td> 
				  </tr>

				  <tr>
				   <td col width="10px"></td>		   
				   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Multas</td> 
				   <td col width="100px" class="text-right borde">0.00</td> 
				   <td col width="100px" class="text-right borde"><strong>{{ number_format(($bloque->cuotaRegPorCobrar + $bloque->cuotaExtraPorCobrar + $bloque->recargoPorCobrar),2) }}</strong></td> 			  
				   <td col width="100px"></td> 
				  </tr>
				
				@endforeach					  

		  </tbody>
		</table>		

		<table class="table table-hove table-hover celBg-yellow">
		  <thead>
			  <tr col width="10px"></tr>
			  <tr></tr>
			  <tr>
			   <th colspan="4" class="text-left borde celBg-gray">&nbsp;<strong>Otros activos</strong></th> 
			  </tr>
		  </thead> 			  
		  <tbody> 						
			  
			  @foreach ($otrosActivos as $otrosActivo)
		
					@if ($otrosActivo != $otrosActivos->last())
				  <tr>
				   <td col width="10px"></td>		   
				   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $otrosActivo['nombre'] }}</td> 
				   <td col width="100px" class="text-right borde">{{ number_format($otrosActivo['saldo'], 2) }}</td> 
				   <td col width="100px" class="text-right"></td> 		  
				   <td col width="100px"></td> 
				  </tr>
			  
					@else
					  <tr>
					   <td col width="10px"></td>		   
					   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $otrosActivo['nombre'] }}</td> 
					   <td col width="100px" class="text-right borde">{{ number_format($otrosActivo['saldo'], 2) }}</td> 
					   <td col width="100px" class="text-right borde"><strong>{{ number_format($otrosActivo['saldo'], 2) }}</strong></td> 		  
					   <td col width="100px"></td> 
					  </tr>
					@endif

				@endforeach
			  
			  <tr>
			   <td col width="10px"></td>		   
			   <td col width="210px" colspan="4" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Total de activos</strong></td> 
			   <td col width="100px" class="text-right"><strong><p class="mix">{{ number_format(($saldoBanco + $cgeneralSaldo + $cchicaSaldo + $totalCuotasPorCobrar),2) }}</p></strong></td> 
			  </tr>
		  </tbody>
		</table>	

		<table class="table table-hove table-hover celBg-red">
		  <thead>
			  <tr col width="10px"></tr>

			  <tr>
			   <th colspan="12" class="text-center borde celBg-gray">PASIVOS Y PATRIMONIO</th> 
			  </tr>
			  
			  <tr>
			   <th colspan="4" class="text-left borde celBg-gray">&nbsp;<strong>Pasivos</strong> 
				  <span class="hidden-print"><a href="{{ URL::route('facturasporpagar') }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="glyphicon glyphicon-book"></i></a>
					</span>
				</th>


			  </tr>
		  </thead> 			  
		  <tbody> 						
			  
			  @foreach ($pasivos as $pasivo)
		
					@if ($pasivo != $pasivos->last())
					  <tr>
					   <td col width="10px"></td>		   
					   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $pasivo->nombre }}</td> 
					   <td col width="100px" class="text-right borde">{{ number_format($pasivo->saldo, 2) }}</td> 
					   <td col width="100px" class="text-right"></td> 		  
					   <td col width="100px"></td> 
					  </tr>
					@else
					  <tr>
					   <td col width="10px"></td>		   
					   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $pasivo->nombre }}</td> 
					   <td col width="100px" class="text-right borde">{{ number_format($pasivo->saldo, 2) }}</td> 
					   <td col width="100px" class="text-right borde"><strong>{{ number_format($totalPasivos, 2) }}</strong></td> 		  
					   <td col width="100px"></td> 
					  </tr>

					@endif
				
				@endforeach		
		  
		  </tbody>
		</table>

		<table class="table table-hove table-hover celBg-cian">
		  <thead>
			  <tr col width="10px"></tr>
			  <tr></tr>
			  <tr>
			   <th colspan="4" class="text-left borde celBg-gray">&nbsp;<strong>Patrimonio</strong></th> 
			  </tr>
		  </thead> 			  
		  <tbody> 						
			  
			  @foreach ($patrimonios as $patrimonio)

					@if ($patrimonio != $patrimonios->last())
					  <tr>
					   <td col width="10px"></td>		   
					   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $patrimonio->nombre }}</td> 
					   <td col width="100px" class="text-right borde">{{ number_format($patrimonio->saldo, 2) }}</td> 
					   <td col width="100px" class="text-right"></td> 		  
					   <td col width="100px"></td> 
					  </tr>
					@else
					  <tr>
					   <td col width="10px"></td>		   
					   <td col width="210px" colspan="2" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $patrimonio->nombre }}</td> 
					   <td col width="100px" class="text-right borde">{{ number_format($patrimonio->saldo, 2) }}</td> 
					   <td col width="100px" class="text-right borde"><strong>{{ number_format($totalPatrimonios, 2) }}</strong></td> 		  
					   <td col width="100px"></td> 
					  </tr>
					@endif
				
				@endforeach		
			  
			  <tr>
			   <td col width="10px"></td>		   
			   <td col width="210px" colspan="4" class="text-left borde">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Total pasivo y patrimonio</strong></td> 
			   <td col width="100px" class="text-right"><strong><p class="mix">{{ number_format($totalPasivos + $totalPatrimonios, 2) }}</p></strong></td> 
			  </tr>

		  </tbody>
		</table>		
  
  <script src="{{ URL::asset('assets/backend/js/libs/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap-3.3.7.min.js') }}"></script>

</body>
</html>