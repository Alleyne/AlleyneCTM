<!DOCTYPE html>
<html lang="en">

<head>
  <title>Balance General</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
	<link href="{{ URL::asset('assets/backend/css/bootstrap-3.3.7.min.css') }}" rel="stylesheet" type="text/css" media="screen">
	<link href="{{ URL::asset('assets/backend/css/jquery.datatables-1.10.12.min.css') }}" rel="stylesheet" type="text/css" media="screen">
	
	<style type="text/css">
		td.details-control {
		    background: url('../vendor/datatables/details_open.png') no-repeat center center;
		    cursor: pointer;
		}
		tr.shown td.details-control {
		    background: url('../vendor/datatables/details_close.png') no-repeat center center;
		}
	</style>
</head>

<body style="font-size:13px;">
	<div class="container" style="width:8.5in; background-color:white";>

    <div class="row"><!-- row -->
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
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
	
	<script type="text/javascript">
		/* Formatting function for row details - modify as you need */
		function format ( d ) {
		    // `d` is the original data object for the row
	    
			/*"render": function(d){
          if(d !== null){
              var table = "<table>";
              $.each(d, function(k, v){
                  table += "<tr><td>" + v.Issue + "</td><td>" + v.IssueDate + "</td><td>" + v.Number + "</td></tr>";
              });
              return table + "</table>";
          }else{
              return "";
          }
      }*/

	    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
	        '<tr>'+
	            '<td>Fecha: '+ d.factura.fecha+'</td>'+
	            '<td>Factura #'+d.factura.doc_no+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monto B/.'+d.factura.total+'</td>'+
	        '</tr>'+
	    '</table>';
		}
		 
		$(document).ready(function() {
		  var table = $('#example').DataTable( {

        paging: false,
        stateSave: true,

			  "data": [{!! $data !!}],

        "columns": [
            {
              "className":      'details-control',
              "orderable":      false,
              "data":           null,
              "defaultContent": ''
            },
            { "data": "afavorde" },
            { "data": "factura_no" },
            { "data": "pagotipo" },
            { "data": "monto" },
            { "data": "f_pago" }
        ],
        "order": [[1, 'asc']]
		  });
		     
		    // Add event listener for opening and closing details
		    $('#example tbody').on('click', 'td.details-control', function () {
		        var tr = $(this).closest('tr');
		        var row = table.row( tr );
		 
		        if ( row.child.isShown() ) {
		            // This row is already open - close it
		            row.child.hide();
		            tr.removeClass('shown');
		        }
		        else {
		            // Open this row
		            row.child( format(row.data()) ).show();
		            tr.addClass('shown');
		        }
		    } );
		} );	
	</script>


</body>
</html>