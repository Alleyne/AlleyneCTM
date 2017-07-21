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
		}
		
		.contenedor-principal {
			height: 8.5in;
			width: 14in;
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
					<td width="14%"> <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width=70 height=70 alt="Responsive image"></td>
					<td width="69%">
						<div class="encabezado-principal">
							<label>MONAGRE CORP. S.A.</label><br>
							<label>HOJA DE TRABAJO</label><br>
							<label>Periodo contable del mes de {{ $periodo }}</label>
					</div>					</td>
					<td width="17%"><div align="center" class="Estilo1 Estilo2"></div></td>
				  </tr>
				</table>
			</div>
			<br />	
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
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
							<td align="left">{{ $dato->codigo }}</td>
							<td align="left">{{ $dato->nombre }}</td>
							<td>@if ($dato->bp_debito!="0.00") {{ $dato->bp_debito }} 
								@endif
							</td>
							
							<td>@if ($dato->bp_credito!="0.00")
									{{ $dato->bp_credito }}
								@endif
							</td>

							<td>@if ($dato->aj_debito!="0.00")
									{{ $dato->aj_debito }}
								@endif
							</td>
							
							<td>@if ($dato->aj_credito!="0.00")
									{{ $dato->aj_credito }}
								@endif
							</td>

							<td>@if ($dato->ba_debito!="0.00")
									{{ $dato->ba_debito }}
								@endif
							</td>
						    
							<td>@if ($dato->ba_credito!="0.00")
									{{ $dato->ba_credito }}
								@endif
							</td>
						    
							<td>@if ($dato->er_debito!="0.00")
									{{ $dato->er_debito }}
								@endif
							</td>
						    
							<td>@if ($dato->er_credito!="0.00")
									{{ $dato->er_credito }}
								@endif
							</td>
			                
							<td>@if ($dato->bg_debito!="0.00")
									{{ $dato->bg_debito }}
								@endif
							</td>
						    
							<td>@if ($dato->bg_credito!="0.00")
									{{ $dato->bg_credito }}
								@endif
							</td>
						</tr>
					@endforeach

					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix">{{ number_format($total_bp_debito,2) }}</p></td>
						<td><p class="mix">{{ number_format($total_bp_credito,2) }}</p></td>
						<td><p class="mix">{{ number_format($total_aj_debito,2) }}</p></td>
						<td><p class="mix">{{ number_format($total_aj_credito,2) }}</p></td>
						<td><p class="mix">{{ number_format($total_ba_debito,2) }}</p></td>
						<td><p class="mix">{{ number_format($total_ba_credito,2) }}</p></td>
						<td><p class="lineup">{{ number_format($total_er_debito,2) }}</p></td>
						<td><p class="lineup">{{ number_format($total_er_credito,2) }}</p></td>
						<td><p class="lineup">{{ number_format($total_bg_debito,2) }}</p></td>
						<td><p class="lineup">{{ number_format($total_bg_credito,2) }}</p></td>
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
							<td><p class="doublelinedown">{{ number_format($utilidad,2) }}</p></td>
							<td><p class="doublelinedown">&nbsp;</p></td>						
						@else
							<td><p class="doublelinedown">&nbsp;</p></td>	
							<td><p class="doublelinedown rojo">{{ number_format(abs($utilidad),2) }}</p></td>
						@endif

						@if ($utilidad>0)
							<td><p class="doublelinedown">&nbsp;</p></td>
							<td><p class="doublelinedown">{{ number_format($utilidad,2) }}</p></td>
						@else
							<td><p class="doublelinedown rojo">{{ number_format(abs($utilidad),2) }}</p></td>
							<td><p class="doublelinedown">&nbsp;</p></td>							
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
							<td><p class="doublelinedown">{{ number_format(($total_er_debito+$utilidad),2) }}</p></td>
							<td><p class="doublelinedown">{{ number_format($total_er_credito,2) }}</p></td>						
						@else
							<td><p class="doublelinedown">{{ number_format($total_er_debito,2) }}</p></td>	
							<td><p class="doublelinedown" color:"red">{{ number_format(($total_er_credito+abs($utilidad)),2) }}</p></td>
						@endif

						@if ($utilidad>0)
							<td><p class="doublelinedown">{{ number_format($total_bg_debito,2) }}</p></td>
							<td><p class="doublelinedown">{{ number_format(($total_bg_credito+$utilidad),2) }}</p></td>
						@else
							<td><p class="doublelinedown">{{ number_format(($total_bg_debito+abs($utilidad)),2) }}</p></td>
							<td><p class="doublelinedown">{{ number_format($total_bg_credito,2) }}</p></td>							
						@endif

					</tr>
			  </table>
			</div>
		</div>
	</div>
</body>
</html>
