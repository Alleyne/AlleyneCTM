<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>ctmaster</title>
    <style type="text/css">
		@page { margin: 0px; }
		html { margin: 0px}
		
		body {
			margin: 0px;
			font-family: Arial, Helvetica, sans-serif;
		}
		
		.contenedor-principal {
			height: 11n;
			width: 8.5in;
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
		p.mix2 {
			border-style: solid hidden hidden hidden;
			font-weight:bold,
		}

		p.mix3 {
			border-style:  hidden hidden solid hidden;
		}
		
		.Estilo2 {font-weight: bold}
		
		p.mix4 {
			border-style:  hidden hidden double hidden;
			font-weight:bold,
		}	.Estilo2 {font-weight: bold}
    	
    	.rojo {
    		color:#FF0000;

	</style>
</head>
<body>
	<div class="contenedor-principal">	 
		<div class="contenedor">		
			<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td width="14%"> <img src="{{asset('assets/backend/img/ctmaster_logo.png') }}" width=70 height=70 alt="Responsive image"></td>
					<td width="66%">
						<div class="encabezado-principal">
							<label>MONAGRE CORP. S.A.</label><br>
							<label>BALANCE GENERAL</label><br>
							<label>Periodo contable de {{ $periodo }}</label>
						</div>					</td>
					<td width="20%"><div align="center" class="Estilo1 Estilo2"></div></td>
				  </tr>
			  </table>
			</div>
			<br />	
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr align="right">
						<td colspan="2" align="left" bgcolor="#CCCCCC"><strong>ACTIVO</strong></td>
						<td width="7%" align="left" bgcolor="#CCCCCC">&nbsp;</td>
						<td width="15%" align="left" bgcolor="#CCCCCC">&nbsp;</td>
						<td width="15%" bgcolor="#CCCCCC">&nbsp;</td>
					</tr>
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>Activo  Corriente</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%">&nbsp;</td>
					</tr>
					
					@foreach ($activoCorrientes as $activoCorriente)
						<tr align="right">
							<td width="3%" align="left"></td>
							<td width="55%" align="left">{{ $activoCorriente->nombre }}</td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="15%" align="left"><div align="right">{{ number_format($activoCorriente->bg_debito,2) }}</div></td>
							<td width="15%">&nbsp;</td>
						</tr>				
					@endforeach

					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left"><strong>Total Activo Corriente</strong> </td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix2">{{ number_format($total_activoCorrientes,2) }}</p></td>
						<td width="15%" align="right"><p>{{ number_format($total_activoCorrientes,2) }}</p></td>
					</tr>
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left">&nbsp;</td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
					</tr>
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>Activo No Corriente</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="18%" align="left">&nbsp;</td>
						<td width="18%">&nbsp;</td>
					</tr>
					
					@foreach ($activoNoCorrientes as $activoNoCorriente)
						<tr align="right">
							<td width="3%" align="left"></td>
							<td width="55%" align="left">{{ $activoNoCorriente->nombre }} </td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="15%" align="left"><div align="right">{{ number_format($activoNoCorriente->bg_debito,2) }}</div></td>
							<td width="15%">&nbsp;</td>
						</tr>				
					@endforeach

					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left"><strong>Total Activo No Corriente</strong> </td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix2">{{ number_format($total_activoNoCorrientes,2) }}</p></td>
						<td width="15%" align="right"><p class="mix3">{{ number_format($total_activoNoCorrientes,2) }}</p></td>
					</tr>		  
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>TOTAL DE ACTIVO</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix4"><strong>{{ number_format(($total_activoCorrientes + $total_activoNoCorrientes),2) }}</strong></p></td>
					</tr>
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left">&nbsp;</td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
					</tr>	
					<tr align="right">
						<td colspan="2" align="left" bgcolor="#CCCCCC"><strong>PASIVO Y PATRIMONIO </strong></td>
						<td width="7%" align="left" bgcolor="#CCCCCC">&nbsp;</td>
						<td width="15%" align="left" bgcolor="#CCCCCC">&nbsp;</td>
						<td width="15%" bgcolor="#CCCCCC">&nbsp;</td>
					</tr>
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>Pasivo Corriente</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%">&nbsp;</td>
					</tr>
					
					@foreach ($pasivoCorrientes as $pasivoCorriente)
						<tr align="right">
							<td width="3%" align="left"></td>
							<td width="55%" align="left">{{ $pasivoCorriente->nombre }} </td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="15%" align="left"><div align="right">{{ number_format($pasivoCorriente->bg_credito,2) }}</div></td>
							<td width="15%">&nbsp;</td>
						</tr>				
					@endforeach
					
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left"><strong>Total Pasivo Corriente</strong> </td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix2">{{ number_format($total_pasivoCorrientes,2) }}</p></td>
						<td width="15%" align="right"><p>{{ number_format($total_pasivoCorrientes,2) }}</p></td>
					</tr>
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left">&nbsp;</td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
					</tr>
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>Pasivo No Corriente</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%">&nbsp;</td>
					</tr>
					
					@foreach ($pasivoNoCorrientes as $pasivoNoCorriente)
						<tr align="right">
							<td width="3%" align="left"></td>
							<td width="55%" align="left">{{ $pasivoNoCorriente->nombre }} </td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="15%" align="left"><div align="right">{{ number_format($pasivoNoCorriente->bg_credito,2) }}</div></td>
							<td width="15%">&nbsp;</td>
						</tr>				
					@endforeach
					
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left"><strong>Total Pasivo No Corriente</strong> </td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix2">{{ number_format($total_pasivoNoCorrientes,2) }}</p></td>
						<td width="15%" align="right"><p class="mix3">{{ number_format($total_pasivoNoCorrientes,2) }}</p></td>
					</tr>		  
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>TOTAL DE PASIVO</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p>{{ number_format(($total_pasivoCorrientes + $total_pasivoNoCorrientes),2) }}</p></td>
					</tr>
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left">&nbsp;</td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
					</tr>	
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>Patrimonio  </strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%">&nbsp;</td>
					</tr>
					
					@foreach ($patrimonios as $patrimonio)
						<tr align="right">
							<td width="3%" align="left"></td>
							<td width="55%" align="left">{{ $patrimonio->nombre }} </td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="15%" align="left"><div align="right">{{ number_format($patrimonio->bg_credito,2) }}</div></td>
							<td width="15%">&nbsp;</td>
						</tr>				
					@endforeach
					<tr align="right">
						<td width="3%" align="left"></td>
						<td width="55%" align="left">Utilidad del periodo </td>
						<td width="7%" align="left">&nbsp;</td>

						@if ($utilidad>=0)
							<td width="15%" align="left"><div align="right">{{ number_format($utilidad,2) }}</div></td>
						@else
							<td width="15%" align="left"><div class="rojo" align="right">{{ number_format($utilidad,2) }}</div></td>
						@endif
						<td width="15%">&nbsp;</td>
					</tr>	
					
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="55%" align="left"><strong>Total de Patrimonio</strong> </td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						@if (($total_patrimonios+$utilidad)>=0)
							<td width="15%" align="right"><p class="mix3">{{ number_format(($total_patrimonios+$utilidad),2) }}</p></td>
						@else
							<td width="15%" align="right"><p class="mix3 rojo">{{ number_format(($total_patrimonios+$utilidad),2) }}</p></td>
						@endif
					</tr>		  
					<tr align="right">
						<td colspan="2" align="left">&nbsp;&nbsp;<strong>TOTAL DE PASIVO Y PATRIMONIO</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="15%" align="left">&nbsp;</td>
						<td width="15%" align="right"><p class="mix4"><strong>{{ number_format(($total_pasivoCorrientes + $total_pasivoNoCorrientes + $total_patrimonios + $utilidad),2) }}</strong></p></td>
					</tr>
			  </table>
			</div>
		</div>
	</div>
</body>
</html>
