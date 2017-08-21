
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Estado de Cuentas</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<link href="{{ URL::asset('assets/backend/css/bootstrap-3.3.7.min.css') }}" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
</head>

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

    <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px"><strong>ESTADO DE CUENTAS A LA FECHA</strong></p>
			  <p class="text-center" style="margin:0px">{{ Date::today()->toFormattedDateString() }}</p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
         <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width="70px" alt="Responsive image">
      </div>
    </div><!-- end row -->
		
		<br />
			
		<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
      <thead>
          <tr>
              <th>id</th>
              <th>Fecha</th>
              <th>Descripcion</th>
              <th class="text-right">Debe</th>
              <th class="text-right">Paga</th>
              <th class="text-right">Saldo</th>
          </tr>
      </thead>

			<tbody>
	
				@foreach ($datos as $dato)
					<tr>
						<td>{{ $dato['id'] }}</td>
						<td col width="80px">{{ $dato['fecha'] }}</td>
						<td align="left">{{ $dato['detalle'] }}</td>
						<td align="right">{{ $dato['debe'] }}</td>
						<td align="right">{{ $dato['paga'] }}</td>
						<td align="right"><strong>{{ number_format($dato['saldo'], 2) }}</strong></td>
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
        
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ],

        "paging": false,
        "scrollY": "393px",
        "scrollCollapse": true,
        "stateSave": true,
				"ordering": false,
        
        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles para esta tabla",
            "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ registros",
            "infoEmpty":      "",
            "infoFiltered":   "",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontro ningun registro con ese filtro",
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