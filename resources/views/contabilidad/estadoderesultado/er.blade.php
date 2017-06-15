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
			padding: 0.125in;
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

    .Estilo2 {font-weight: bold}
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
							<label>ESTADO DE RESULTADO</label><br>
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
						<td colspan="2" align="left"><strong>Ingresos</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td width="13%">&nbsp;</td>
					</tr>

					@foreach ($ingresos as $ingreso)
				  		<tr align="right">
							<td width="3%" align="left">&nbsp;</td>
							<td width="57%" align="left">{{ $ingreso->nombre }}</td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="13%" align="left"><div align="right">{{ number_format(floatval($ingreso->ba_credito),2) }}</div></td>
							<td width="13%">&nbsp;</td>
						</tr>				
					@endforeach
					
					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="57%" align="left"><strong><em>Total de Ingresos</em></strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td width="13%"><p class="mix"><strong>{{ number_format($total_ingresos,2) }}</strong></p></td>
					</tr>
					
					<tr>&nbsp;</tr>					
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>

					<tr align="right">
						<td colspan="2" align="left"><strong>Menos Gastos</strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td width="13%">&nbsp;</td>
					</tr>

					@foreach ($gastos as $gasto)
						<tr align="right">
							<td width="3%" align="left">&nbsp;</td>
							<td width="57%" align="left">{{ $gasto->nombre }}</td>
							<td width="7%" align="left">&nbsp;</td>
							<td width="13%" align="right">{{ number_format(floatval($gasto->ba_debito),2) }}</td>
							<td width="13%">&nbsp;</td>
						</tr>				
					@endforeach

					<tr>&nbsp;</tr>					
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>

					<tr align="right">
						<td width="3%" align="left">&nbsp;</td>
						<td width="57%" align="left"><strong><em>Total de Gastos</em></strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td width="13%"><p class="mix" ><strong>{{ number_format($total_gastos,2) }}</strong></p></td>
					</tr>

					<tr align="right">
						<td colspan="2" align="left"></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td width="13%">&nbsp;</td>
					</tr>
					<tr align="right">
						<td colspan="2" align="left"><strong>Utilidad Neta </strong></td>
						<td width="7%" align="left">&nbsp;</td>
						<td width="13%" align="left">&nbsp;</td>
						<td><p class="mix" ><strong>{{ number_format($utilidad,2) }}</strong></p></td>
					</tr>
			  </table>
			</div>
		</div>
	</div>
</body>
</html>
