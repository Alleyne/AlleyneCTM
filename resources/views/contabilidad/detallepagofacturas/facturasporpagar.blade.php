<!DOCTYPE html>
<html lang="en">

<head>
  <title>Balance General</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<link href="{{ URL::asset('assets/backend/css/bootstrap-3.3.7.min.css') }}" rel="stylesheet" type="text/css" media="screen">
	<link href="{{ URL::asset('assets/backend/css/jquery.datatables-1.10.12.min.css') }}" rel="stylesheet" type="text/css" media="screen">
	
	<style type="text/css">

	</style>
</head>

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

			<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Proveedor</th>
                <th>Factura #</th>
                <th>Pago</th>
                <th>Monto</th>
                <th>Fecha de pago</th>
            </tr>
        </thead>
				<tbody>
		
					@foreach ($datos as $dato)
						<tr>
							<td col width="40px">xxx</td>
							<td><strong>xxx</strong></td>
							<td>xxx</td>
							<td>xxx</td>
							<td>xxx</td>
						</tr>
					@endforeach
		
				</tbody>
			</table>




{{--     <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px">FACTURAS POR PAGAR A PROVEEDORES</p>
			  <p class="text-center" style="margin:0px">Periodo contable del mes de </p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
         <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width="70px" alt="Responsive image">
      </div>
    </div><!-- end row -->
		
		<br />

		<table id="example" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th></th>
		                <th>Proveedor</th>
		                <th>Factura #</th>
		                <th>Pago</th>
		                <th>Monto</th>
		                <th>Fecha de pago</th>
		            </tr>
		        </thead>
		        <tfoot>
		            <tr>
		                <th></th>
		                <th></th>
		                <th></th>
		                <th>Total por pagar</th>
		                <th><strong>{{ number_format($totalPorPagar, 2) }}</strong></th>
		                <th></th>
		            </tr>
		        </tfoot>
		</table>
  </div> --}}



</body>
</html>