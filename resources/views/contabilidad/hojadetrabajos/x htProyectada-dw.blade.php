<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Hoja de trabajo</title>

    <style type="text/css">
			@page { margin: 0px; }
			html { margin: 0px}
			
			body {
				margin: 0px;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
			}
			
			.contenedor-principal {
				height: 8.5in;
				width: 14in;
				font-size: 3px;
				padding: 0.25in;
				margin-right: auto;
				margin-left: auto;
			}
			
			.contenedor {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 10px;
				font-style: normal;
				line-height: normal;
				font-weight: normal;
				height: 100%;
				width: 100%;
			}
			
			.encabezado-principal {
				font-size: 16px;
				font-style: normal;
				font-weight: bold;
				color: #000000;
				text-align: center;
				height: auto;
				line-height: 18px;
			}

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

			.mytable {
		    font-size: 14px;
			}

			p.mix {
				border-style: solid hidden double hidden;
				font-weight:bold,
			}

			p.lineup {
				border-style: solid hidden hidden hidden;
				font-weight:bold,
			}
			
			p.linedown {
				border-style: hidden hidden hidden solid;
				font-weight:bold,
			}
	    
			p.doublelinedown {
				border-style: hidden hidden double hidden;
				font-weight:bold,
			}
	    	
    	.rojo {
    		color:#FF0000;
    	}
    </style>
</head>
<body>
	<div class="contenedor-principal">	 
		<div class="contenedor">		
			<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td width="14%"> <img src="{{ asset(Cache::get('jdkey')->imagen_S) }}" alt="Logo"></td>
					<td width="69%">
						<div class="encabezado-principal">
							<label>MONAGRE CORP. S.A.</label><br>
							<label>HOJA DE TRABAJO</label><br>
							<label>Periodo contable del mes de {{ $periodo->periodo }}</label>
					</div>					</td>
					<td width="17%"><div align="center" class="Estilo1 Estilo2"></div></td>
				  </tr>
				</table>
			</div>
			<br />	
			<div>
				<table class="mytable" width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#999999">
						<th  bgcolor="#FFFFFF" colspan="2" scope="col">&nbsp;</th>
						<th colspan="2" bgcolor="#CCCCCC" scope="col">Balance de Pruebas</th>
						<th colspan="2" bgcolor="#CCCCCC" scope="col">Ajustes</th>
						<th colspan="2" bgcolor="#CCCCCC" scope="col">Balance Ajustado</th>
						<th colspan="2" bgcolor="#CCCCCC" scope="col">Estado de Resultado</th>
						<th colspan="2" bgcolor="#CCCCCC" scope="col">Balance General</th>
					</tr>
					<tr bgcolor="#999999">
						<th width="5%" bgcolor="#CCCCCC" scope="col">C&oacute;digo</th>
						<th width="32%"  align="left" bgcolor="#CCCCCC" scope="col">Cuenta</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">D&eacute;bito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">Cr&eacute;dito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">D&eacute;bito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">Cr&eacute;dito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">D&eacute;bito</th>
					  <th width="5.5%" bgcolor="#CCCCCC" scope="col">Cr&eacute;dito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">D&eacute;bito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">Cr&eacute;dito</th>
						<th width="5.5%" bgcolor="#CCCCCC" scope="col">D&eacute;bito</th>
					  <th width="5.5%" bgcolor="#CCCCCC" scope="col">Cr&eacute;dito</th>
					</tr>

					@foreach ($datos as $dato)
						<tr align="right">
							<td align="left"><a href="{{ URL::route('verMayorAux', array($dato['periodo'], $dato['cuenta'], $dato['un_id'])) }}"> {{ $dato['codigo'] }} </a></td>
							<td align="left">{{ $dato['cta_nombre'] }}</td>
							<td class="borde celBg-yellow">@if ($dato['saldo_debito'] != "0.00")
								  {{ $dato['saldo_debito'] }} 
								@endif
							</td>
							
							<td class="borde celBg-yellow">@if ($dato['saldo_credito'] != "0.00")
									{{ $dato['saldo_credito'] }}
								@endif
							</td>

							<td class="borde celBg-red">@if ($dato['saldoAjuste_debito'] != "0.00")
									{{ $dato['saldoAjuste_debito'] }}
								@endif
							</td>
							
							<td class="borde celBg-red">@if ($dato['saldoAjuste_credito'] != "0.00")
									{{ $dato['saldoAjuste_credito'] }}
								@endif
							</td>

							<td class="borde celBg-green">@if ($dato['saldoAjustado_debito'] != "0.00")
									{{ $dato['saldoAjustado_debito'] }}
								@endif
							</td>
						    
							<td class="borde celBg-green">@if ($dato['saldoAjustado_credito'] != "0.00")
									{{ $dato['saldoAjustado_credito'] }}
								@endif
							</td>
						    
							<td class="borde celBg-blue">@if ($dato['er_debito'] != "0.00")
									{{ $dato['er_debito'] }}
								@endif
							</td>
						    
							<td class="borde celBg-blue">@if ($dato['er_credito'] != "0.00")
									{{ $dato['er_credito'] }}
								@endif
							</td>
			                
							<td class="borde celBg-cian">@if ($dato['bg_debito'] != "0.00")
									{{ $dato['bg_debito'] }}
								@endif
							</td>
						    
							<td class="borde celBg-cian">@if ($dato['bg_credito'] != "0.00")
									{{ $dato['bg_credito'] }}
								@endif
							</td>
						</tr>
					@endforeach

					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix celBg-yellow">{{ number_format($total_bp_debito,2) }}</p></td>
						<td><p class="mix celBg-yellow">{{ number_format($total_bp_credito,2) }}</p></td>
						<td><p class="mix celBg-red">{{ number_format($total_aj_debito,2) }}</p></td>
						<td><p class="mix celBg-red">{{ number_format($total_aj_credito,2) }}</p></td>
						<td><p class="mix celBg-green">{{ number_format($total_ba_debito,2) }}</p></td>
						<td><p class="mix celBg-green">{{ number_format($total_ba_credito,2) }}</p></td>
						<td><p class="lineup celBg-blue">{{ number_format($total_er_debito,2) }}</p></td>
						<td><p class="lineup celBg-blue">{{ number_format($total_er_credito,2) }}</p></td>
						<td><p class="lineup celBg-cian">{{ number_format($total_bg_debito,2) }}</p></td>
						<td><p class="lineup celBg-cian">{{ number_format($total_bg_credito,2) }}</p></td>
					</tr>
					<tr align="right">
						<td>&nbsp;</td>
						<td align="left">Utilidad neta</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@if ($utilidad>0)
							<td><p class="doublelinedown celBg-blue">{{ number_format($utilidad,2) }}</p></td>
							<td><p class="doublelinedown celBg-blue">&nbsp;</p></td>						
						@else
							<td><p class="doublelinedown celBg-blue">&nbsp;</p></td>	
							<td><p class="doublelinedown rojo celBg-blue">{{ number_format(abs($utilidad),2) }}</p></td>
						@endif

						@if ($utilidad>0)
							<td><p class="doublelinedown celBg-cian">&nbsp;</p></td>
							<td><p class="doublelinedown celBg-cian">{{ number_format($utilidad,2) }}</p></td>
						@else
							<td><p class="doublelinedown rojo celBg-cian">{{ number_format(abs($utilidad),2) }}</p></td>
							<td><p class="doublelinedown celBg-cian">&nbsp;</p></td>							
						@endif
					</tr>
					
					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						@if ($utilidad>0)
							<td><p class="doublelinedown celBg-blue">{{ number_format(($total_er_debito+$utilidad),2) }}</p></td>
							<td><p class="doublelinedown celBg-blue">{{ number_format($total_er_credito,2) }}</p></td>						
						@else
							<td><p class="doublelinedown celBg-blue">{{ number_format($total_er_debito,2) }}</p></td>	
							<td><p class="doublelinedown celBg-blue" color:"red">{{ number_format(($total_er_credito+abs($utilidad)),2) }}</p></td>
						@endif

						@if ($utilidad>0)
							<td><p class="doublelinedown celBg-cian">{{ number_format($total_bg_debito,2) }}</p></td>
							<td><p class="doublelinedown celBg-cian">{{ number_format(($total_bg_credito+$utilidad),2) }}</p></td>
						@else
							<td><p class="doublelinedown celBg-cian">{{ number_format(($total_bg_debito+abs($utilidad)),2) }}</p></td>
							<td><p class="doublelinedown celBg-cian">{{ number_format($total_bg_credito,2) }}</p></td>							
						@endif

					</tr>
					<tr>
						<td>
							@if (Cache::get('esAdminkey') || Cache::get('esContadorkey'))
								@if ($permitirAjustes=='Si')
									<div class="col-md-4">
										<form method="get" action="{{ URL::route('createAjustes', $periodo->id) }}">
											<input type="submit" value="Ajustar" />
										</form>  
									</div>
								@endif			
							@endif	
						</td>
						<td>
							@if (Cache::get('esAdminkey') || Cache::get('esContadorkey'))	
								@if ($permitirCerrar=='Si')
									<div class="col-md-4">
										<form method="get" action="{{ route('cierraPeriodo', array($periodo->id, $periodo->periodo, $periodo->fecha)) }}">
											<input type="submit" value="Cerrar periodo" />
										</form>                     
									</div>
								@endif	
							@endif								
						</td>
						<td>
							<div>
								@if ($total_ba_debito == $total_ba_credito) 
									<i style="color:green" class="glyphicon glyphicon-ok"></i> 
								@else
									<i style="color:red" class="glyphicon glyphicon-remove"></i>
								@endif
							</div>
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>	
						<td></td>
						<td></td>
					</tr>

			  </table>
			</div>

      <div class="row">
        <div class="col-xs-12">
          <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
        </div>
      </div>   
		
		</div>
	</div>

</body>
</html>