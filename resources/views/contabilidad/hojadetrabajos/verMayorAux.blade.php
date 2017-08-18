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

		.mytable {
		    white-space: normal;
		    line-height: normal;
		    font-weight: normal;
		    font-size: 13px;
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
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<td width="81%" align="left">
			  	{{ $cuenta->nombre }}
				</td>
				<td width="17%"><div align="right"><strong>{{ $cuenta->codigo }}</strong></div></td>
		    </table>
			<div>
				<table class="mytable" width="100%" border="0" cellspacing="1" cellpadding="0">
					<tr bgcolor="#CCCCCC">
						<th width="10%">Fecha</th>
						<th width="61%" align="left">Cuenta</th>
						<th width="6%">Ref</th>
						<th width="8%">D&eacute;bito</th>
						<th width="8%">Cr&eacute;dito</th>
					    <th width="9%">Saldo</th>
					</tr>
					
					@foreach ($datas as $data)
					<tr align="right">
						<td align="left">{{ $data['fecha'] }}</td>
						<td align="left">{{ $data['detalle'] }}</td>
						<td>{{ $data['ref'] }}</td>
						<td>{{ $data['debito'] }}</td>
						<td>{{ $data['credito'] }}</td>
					    <td><strong>{{ number_format(floatval($data['saldo']),2) }}</strong></td>
					</tr>
					@endforeach
				</table>
			</div>
		</div>
	</div>
</body>
</html>