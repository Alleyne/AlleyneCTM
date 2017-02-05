<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Asamblea Nacional</title>
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
			border-style: solid hidden double  hidden;
			font-weight:bold,
		}
		
		p.solid {
			border-style: hidden hidden solid hidden;
			font-weight:bold,
		}

		p.double {
			border-style: hidden hidden double  hidden;
			font-weight:bold,
		}
	.Estilo1 {font-weight: bold}
    .Estilo2 {font-weight: bold}
    </style>
</head>
<body>
	<div class="contenedor-principal">	 
		<div class="contenedor">		
			<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
						<td width="14%"> <img src="asamblea_logo.png" width=70 height=70 alt="Responsive image"></td>
						<td width="69%">
							<div class="encabezado-principal">
								<label>MONAGRE CORP. S.A.</label><br>
								<label>INFORME DIARIO DE CAJA</label>
								<br>
								<label>{{ $fecha }}</label>
							</div>
						</td>
						<td width="17%"><div align="center" class="Estilo1 Estilo2"></div></td>
				  </tr>
			  </table>
			</div>
			<br />	
			
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#999999">
						<th colspan="3" bgcolor="#CCCCCC" scope="col"><div align="left">Ingreso de Efectivo</div></th>
					</tr>
					<tr bgcolor="#999999">
						<th width="1%" bgcolor="#CCCCCC" scope="col">C&oacute;digo</th>
						<th width="1.5%"  align="left" bgcolor="#CCCCCC" scope="col">Tipo</th>
						<th width="30%"  align="left" bgcolor="#CCCCCC" scope="col">Cuenta</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Monto</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">ITBMS</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Total</th>
						<th width="2%" bgcolor="#FFFFFF" scope="col">&nbsp;</th>
					</tr>
					@foreach ($ingresoEfectivos as $ingresoEfectivo) 
						<tr align="right">
							<td align="left">{{ $ingresoEfectivo->codigo }}</td>
							<td align="left">{{ $ingresoEfectivo->nombre }}</td>
							<td align="left">{{ $ingresoEfectivo->detalle }} </td>
							<td>{{ $ingresoEfectivo->monto }}</td>
							<td>&nbsp;</td>
							<td>{{ $ingresoEfectivo->monto }}</td>
							<td>&nbsp;</td>
					  </tr>
					@endforeach

					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix" >{{ $totalIngresoEfectivos }}</p></td>
						<td><p class="mix" >&nbsp;</p></td>
				    <td><p class="mix" >{{ $totalIngresoEfectivos }}</p></td>
				    <td><p >&nbsp;</p></td>
					</tr>
					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p >&nbsp;</p></td>
						<td colspan="2"><p >&nbsp;</p></td>
					  <td><p >{{ $totalIngresoEfectivos }}</p></td>
					</tr>
			  </table>
			</div>
			<br />
			
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#999999">
						<th colspan="3" bgcolor="#CCCCCC" scope="col"><div align="left">Desembolso de efectivo</div></th>
					</tr>
					<tr bgcolor="#999999">
						<th width="1%" bgcolor="#CCCCCC" scope="col">C&oacute;digo</th>
						<th width="1.5%"  align="left" bgcolor="#CCCCCC" scope="col">Tipo</th>
						<th width="30%"  align="left" bgcolor="#CCCCCC" scope="col">Cuenta</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Monto</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">ITBMS</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Total</th>
						<th width="2%" bgcolor="#FFFFFF" scope="col">&nbsp;</th>
					</tr>
					
					@foreach ($desembolsoEfectivos as $desembolsoEfectivo) 
						<tr align="right">
							<td align="left">{{ $desembolsoEfectivo->codigo }}</td>
							<td align="left">{{ $desembolsoEfectivo->nombre }}</td>
							<td align="left">{{ $desembolsoEfectivo->detalle }} </td>
							<td>{{ $desembolsoEfectivo->monto }}</td>
							<td>&nbsp;</td>
							<td>{{ $desembolsoEfectivo->monto }}</td>
							<td>&nbsp;</td>
					  </tr>
					@endforeach

					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix" >{{ $totalDesembolsoEfectivos }}</p></td>
						<td><p class="mix" >&nbsp;</p></td>
				    <td><p class="mix" >{{ $totalDesembolsoEfectivos }}</p></td>
				    <td><p >&nbsp;</p></td>
					</tr>
					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p >&nbsp;</p></td>
						<td colspan="2"><p >&nbsp;</p></td>
					  <td><p >{{ $totalDesembolsoEfectivos }}</p></td>
					</tr>
					<tr align="right">
					  <td colspan="6" ><div align="right"><strong>Efectivo neto recibido </strong>&nbsp;&nbsp;</div></td>
					  <td colspan="1" bgcolor="#66FF99"><p class="mix" >{{ $totalIngresoEfectivos - $totalDesembolsoEfectivos }}</p></td>
					</tr>			  
			  </table>
			</div>
			<br />
			
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#999999">
						<th colspan="3" bgcolor="#CCCCCC" scope="col"><div align="left">Desglose del efectivo</div></th>
					</tr>
					<tr bgcolor="#999999">
						<th width="1%" bgcolor="#CCCCCC" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th width="1.5%"  align="left" bgcolor="#CCCCCC" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th width="30%"  align="left" bgcolor="#CCCCCC" scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Monto</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">ITBMS</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Total</th>
						<th width="2%" bgcolor="#FFFFFF" scope="col">&nbsp;</th>
					</tr>
					<tr align="right">
						<td colspan="3" align="left">Efectivo (Billetes y monedas) </td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>{{ $totalEfectivos }}</td>
						<td>&nbsp;</td>
				  </tr>
					<tr align="right">
						<td colspan="3" align="left">Cheques</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="simple">{{ $totalCheques }}</td>
						<td>&nbsp;</td>
				  </tr>
					<tr align="right">
						<td colspan="3" align="left">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix" >{{ $totalCheques +  $totalEfectivos }}</p></td>
						<td>&nbsp;</td>
				  </tr>
					<tr align="right">
						<td colspan="6" ><div align="right"></div></td>
						<td colspan="1"><p >{{ $totalCheques +  $totalEfectivos }}</p></td>
					</tr>
			  </table>
			</div>			
			<br />
			<br />	
		
			<div>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#999999">
						<th colspan="3" bgcolor="#CCCCCC" scope="col"><div align="left">Ingresos por Tarjetas de cr&eacute;dito </div></th>
					</tr>
					<tr bgcolor="#999999">
						<th width="1%" bgcolor="#CCCCCC" scope="col">C&oacute;digo</th>
						<th width="3%"  align="left" bgcolor="#CCCCCC" scope="col">Tipo</th>
						<th width="30%"  align="left" bgcolor="#CCCCCC" scope="col">Cuenta</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Monto</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">ITBMS</th>
						<th width="2%" bgcolor="#CCCCCC" scope="col">Total</th>
						<th width="2%" bgcolor="#FFFFFF" scope="col">&nbsp;</th>
					</tr>
					@foreach ($ingresoTarjetas as $ingresoTarjeta) 
						<tr align="right">
							<td align="left">{{ $ingresoTarjeta->codigo }}</td>
							<td align="left">{{ $ingresoTarjeta->nombre }}</td>
							<td align="left">{{ $ingresoTarjeta->detalle }} </td>
							<td>{{ $ingresoTarjeta->monto }}</td>
							<td>&nbsp;</td>
							<td>{{ $ingresoTarjeta->monto }}</td>
							<td>&nbsp;</td>
					  </tr>
					@endforeach

					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p class="mix" >{{ $totalIngresosTarjetas }}</p></td>
						<td><p class="mix" >&nbsp;</p></td>
					  <td><p class="mix" >{{ $totalIngresosTarjetas }}</p></td>
					  <td><p >&nbsp;</p></td>
					</tr>
					<tr align="right">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><p >&nbsp;</p></td>
						<td colspan="2"><p >&nbsp;</p></td>
					  <td><p >{{ $totalIngresosTarjetas }}</p></td>
					</tr>
					<tr align="right">
						<td colspan="6" ><div align="right"><strong>Total de efectivo y tarjetas &nbsp;&nbsp;</strong>&nbsp;&nbsp;</div></td>
						<td colspan="1" bgcolor="#66FF99"><p class="mix" >{{ $totalIngresoEfectivos + $totalIngresosTarjetas }}</p></td>
					</tr>
					<tr align="right">
						<td colspan="6" >&nbsp;&nbsp;</td>
						<td colspan="1"><p>&nbsp;</p></td>
					</tr>
					<tr align="right">
						<td colspan="6" ><div align="right"><strong>Sobrante o Faltante en efectivo</strong>&nbsp;&nbsp;</div></td>
						<td colspan="1" bgcolor="#66FF99"><p>&nbsp;</p></td>
					</tr>			  
				</table>
			</div>
		</div>
	</div>
</body>
</html>