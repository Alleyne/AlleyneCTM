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
			font-size: 12px;
			font-style: normal;
			line-height: normal;
			font-weight: normal;
		}
		
		.contenedor-principal {
			height: 11in;
			width: 8.5in;
			padding: 0.25in;
			margin-right: auto;
			margin-left: auto;
		}
		
		.contenedor {
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
		
		.mytable {
		    white-space: normal;
		    line-height: normal;
		    font-weight: normal;
		    font-size: 12px;
		    font-variant: normal;
		    font-style: normal;
		    color: -internal-quirk-inherit;
		    text-align: start;
		}

		p.mix {
			border-style: solid hidden double  hidden;
			font-weight:bold,
		}
    </style>
</head>
<body>
	<div class="contenedor-principal">	 
		<div class="contenedor">		
			<div>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td width="14%"> <img src="{{asset('assets/backend/img/ctmaster_logo.png') }}" width=70 height=70 alt="Responsive image"></td>
					<td width="69%">
						<div class="encabezado-principal">
							<label>MONAGRE CORP. S.A.</label><br>
							<label>LIBRO DIARIO</label><br>
							<label>Periodo contable de {{ $periodo }}</label>
						</div>
					</td>
					<td width="17%"><div align="center" class="Estilo1 Estilo2"></div></td>
				  </tr>
				</table>
			</div>
			<br />	

			<div>
				<table class="mytable" width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#CCCCCC">
						<th width="10%">Fecha</th>
						<th width="69" align="left">Cuenta</th>
						<th width="5%">Ref</th>
						<th width="8%">D&eacute;bito</th>
						<th width="8%">Cr&eacute;dito</th>
					</tr>

					@foreach ($datos as $dato)
						@if ($dato->fecha)
							<tr align="right">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						@endif
						<tr align="right">
							<td align="left"><strong>{{ $dato->fecha }}</strong></td>
							@if ($dato->debito >= $dato->credito)
								<td align="left">{{ $dato->detalle }}</td>
							@else
								<td align="left">&nbsp;&nbsp;&nbsp; {{ $dato->detalle }}</td>
							@endif
							<td>{{ $dato->ref }}</td>
							<td>{{ $dato->debito!='0.00' ? $dato->debito : Null }}</td>
							<td>{{ $dato->credito!='0.00' ? $dato->credito : Null }}</td>
						</tr>
					@endforeach
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right"><strong><p class="mix">{{ $total_debito }}</p></strong></td>
						<td align="right"><strong><p class="mix">{{ $total_credito }}</p></strong></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>
</html>