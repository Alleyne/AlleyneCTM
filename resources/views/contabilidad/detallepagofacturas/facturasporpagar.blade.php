<!DOCTYPE html>
<html lang="en">

<head>
  <title>Facturas por pagar</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  

<link href="{{ URL::asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">      

  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  <link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">

</head>

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

    <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px"><strong>FACTURAS POR PAGAR A LA FECHA</strong></p>
        <p class="text-center" style="margin:0px">{{ Date::today()->toFormattedDateString() }}</p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
         <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width="70px" alt="Responsive image">
      </div>
    </div><!-- end row -->
		<a href="{{ URL::previous() }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a> 
		<br />
			
		<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
      <thead>
          <tr>
              <th>Proveedor</th>
              <th class="text-right">Factura #</th>
              <th class="text-right">Total Factura</th>
              <th class="text-right">Total Pagar</th>
              <th class="text-right">Fecha de pago</th>
              <th class="text-center"></th>
          </tr>
      </thead>
      <tfoot>
          <tr>
              <th></th>
              <th></th>
              <th></th>
              <th class="text-right">Total por pagar: B/.  <strong>{{ number_format($totalPorPagar, 2) }}</strong></th>
              <th></th>
              <th></th>
          </tr>
      </tfoot>

			<tbody>
	
				@foreach ($datos as $dato)
					<tr>
						<td col width="40px">{{ $dato->afavorde }}</td>
						<td align="right">{{ $dato->factura->doc_no }}</td>
						<td align="right">{{ number_format($dato->factura->total, 2) }}</td>
						<td align="right"><strong>{{ number_format($dato->monto, 2) }}</strong></td>
						<td align="right">{{ $dato->f_pago }}</td>
            <td col width="50px" align="right">
              <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-file-pdf-o"></i> Pdf</a>
            </td>

          </tr>
				@endforeach
	
			</tbody>
		</table>
  </div>

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>	
	<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  
  <script type="text/javascript">
    $(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "393px",
        "scrollCollapse": true,
        "stateSave": true,

        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles para esta tabla",
            "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ registros",
            "infoEmpty":      "",
            "infoFiltered":   "",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ unidades",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontro ninguna unidad con ese filtro",
            "paginate": {
              "first":      "Primer",
              "last":       "Ultimo",
              "next":       "Proximo",
              "previous":   "Anterior"
            },
            "aria": {
              "sortAscending":  ": active para ordenar ascendentemente",
              "sortDescending": ": active para ordenar descendentemente"
            }
        }
      });
    })
  </script>
</body>
</html>