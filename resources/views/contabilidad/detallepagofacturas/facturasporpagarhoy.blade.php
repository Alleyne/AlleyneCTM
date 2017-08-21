<!DOCTYPE html>
<html lang="en">

<head>
  <title>Facturas por pagar</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<link href="{{ URL::asset('assets/backend/css/bootstrap-3.3.7.min.css') }}" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  <link href="{{ URL::asset('assets/backend/css/toastr.min.css') }}" rel="stylesheet" type="text/css" media="screen"> 

</head>

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

    <div class="row"><!-- row -->
      <div class="col-xs-11">
			  <h4 class="text-center">{{ Cache::get('jdkey')->nombre }}</h4>
			  <p class="text-center" style="margin:0px"><strong>FACTURAS POR PAGAR PARA HOY {{ strtoupper(Date::today()->toFormattedDateString()) }}</strong></p>
			  <p class="text-center" style="margin:0px">(en balboas)</p>
      </div>
      <div class="col-xs-1">
         <img src="{{ asset(Cache::get('jdkey')->imagen_M) }}" width="70px" alt="Responsive image">
      </div>
    </div><!-- end row -->
		<a href="{{ URL::route('backend') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a> 
		<br />
			
		<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
      <thead>
          <tr>
              <th col width="350px">Proveedor</th>
              <th col width="90px" class="text-right">Factura #</th>
              <th col width="90px" class="text-right">Total Fact</th>
              <th col width="90px" class="text-right">Total Pagar</th>
              <th col width="90px" class="text-right">Fecha pago</th>
              <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>   
          </tr>
      </thead>
      <tfoot>
          <tr>
              <th class="text-right">Total por pagar: </th>
              <th></th>
              <th></th>
              <th class="text-right"><strong> B/.&nbsp;{{ number_format($totalPorPagar, 2) }}</strong></th>
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
            <td col width="40px" align="right">
              <button type="button" data-target="#myModalPagar" data-toggle="modal" class="btn btn-success btn-xs pagarContabilizarBtn" data-detallepagofactura_id="{{ $dato->id }}">Pagar</button>
            </td>
          </tr>
				@endforeach
	
			</tbody>
		</table>
    
    <!-- Incluye la modal box -->
    @include('templates.backend._partials.modal_confirm')
    @include('contabilidad.detallepagofacturas.modal_pagar')

  </div>
	
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>	
  <script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap-3.3.7.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <script src="{{ URL::asset('assets/backend/js/toastr/toastr.js') }}"></script>
  
  <!-- NOTIFICACIONES VIA TOASTR-->
  <script>
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}", '<< FELICIDADES >>', {timeOut: 7000});
    @endif

    @if(Session::has('info'))
        toastr.info("{{ Session::get('info') }}", '<< ATENCION >>', {timeOut: 7000});
    @endif

    @if(Session::has('warning'))
        toastr.warning("{{ Session::get('warning') }}", '<< PRECAUCION >>', {timeOut: 7000});
    @endif

    @if(Session::has('danger'))
        toastr.error("{{ Session::get('danger') }}", '<< ERROR >>', {timeOut: 7000});
    @endif
  </script>

  <script type="text/javascript">
    $(document).ready(function() {

      var trantipo_id = jQuery('#trantipo_id');
      var select = this.value;
      trantipo_id.change(function () {
          
        if ($(this).val() == 1) {
          $('.chequeNo').show();
          $('.transaccionNo').hide();
        
        } else if ($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4 || $(this).val() == 6 || $(this).val() == 7) {
          $('.chequeNo').hide();
          $('.transaccionNo').show();
        
        } else if ($(this).val() == 5) {
          $('.chequeNo').hide();
          $('.transaccionNo').hide();
      
        } else {
          $('.chequeNo').hide();
          $('.transaccionNo').hide();
        }
      });

      // pasa el detallepagofactura_id al 
      $('#myModalPagar').on('show.bs.modal', function(e) {
          var id = $(e.relatedTarget).data('detallepagofactura_id');
          $(e.currentTarget).find('input[name="detallepagofactura_id"]').val(id);
      });

      $("input[type='submit']").attr("disabled", false);
      $("form").submit(function(){
        $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
        return true;
      });

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