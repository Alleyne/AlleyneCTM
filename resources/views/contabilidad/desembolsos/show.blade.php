<html>
<head>
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Informe para desembolso de Caja Chica</title>
    <style type="text/css">
		@page { margin: 0px; }
		html { margin: 0px}
		
			.contenedor-principal {
				height: 8.5in;
				width: 11in;
				padding: 0.5in;
				margin-right: auto;
				margin-left: auto;
		}
    </style>
</head>
<body>

		<div class="contenedor-principal">
			<div class="well well-sm">
				<div class="card card-outline-danger text-center">
			    <h4 class="card-title">Informe para desembolso de Caja Chica</h4>
				  <div class="row">
				    <div class="col-md-2">
							12/12/2017
				    </div>
				    <div class="col-md-8">
				      Responsable: German Barrios
				    </div>
				    <div class="col-md-2">
							No: 123456
				    </div>
				  </div>		
				</div>	
			</div>

				<div class="pull-right">
					<a href="{{ URL::route('desembolsos.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
				</div>

				<table class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>SERVIPRODUCTO</th>
							<th>GASTO</th>
							<th>CANT</th>
							<th>PRECIO</th>
							<th>ITBMS</th>
							<th class="text-right">TOTAL</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($datos as $dato)
							<tr>
								<td col width="40px">{{ $dato->id }}</td>
								<td><strong>{{ $dato->serviproducto }}</strong></td>
								<td>{{ $dato->codigo }}</td>
								<td col width="60px">{{ $dato->cantidad }}</td>
								<td col width="60px">{{ $dato->precio }}</td>
								<td col width="60px">{{ $dato->itbms }}</td>
								<td col width="60px" class="text-right"><strong>{{ $dato->total }}</strong></td>
							</tr>
						@endforeach
					</tbody>
				</table>
		  
			
			<div class="invoice-footer">
				<div class="col-sm-12">
					<div class="pull-right">
						<h7><strong>Sub Total : <span>{{ number_format(floatval($subTotal),2) }}</span></strong></h7>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="pull-right">
						<h7><strong>Itbms : <span>{{ number_format(floatval($totalItbms),2) }}</span></strong></h7>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="pull-right">
						<h5><strong>Total : <span class="text-success">{{ number_format(floatval($subTotal + $totalItbms),2) }}</span></strong></h5>
					</div>
				</div>
				
				<br />	
				<br />	
				<br />	
						
				<div class="col-sm-12">
					<div class="col-sm-6">
						<div class="pull-left">
							<h5><strong>Responsable : <span class="text-success">_____________________</span></strong></h5>
						</div>
					</div>		

					<div class="col-sm-6">
						<div class="pull-right">
							<h5><strong>Aprobado por : <span class="text-success">_____________________</span></strong></h5>
						</div>
					</div>	
				</div>			
			</div>
	</div>
</body>
</html>