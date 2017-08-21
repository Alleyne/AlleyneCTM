@extends('templates.frontend._layouts.unify')

@section('title', '| Pagos efectuados')

@section('content')
<!--Hover Rows-->
<div class="col-xs-12 col-sm-12 col-md-12">

    <div class="alert alert-success" role="alert">
        <strong>Estimado Propietario</strong>, A continuación se muestran todos su pagos.
    </div>
    <table id="dt_basic" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="text-left">FECHA</th> 
                <th>TIPO</th>  
                <th>NO</th>
                <th>BANCO</th>                          
                                                  
                <th class="text-right">MONTO</th> 
                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $dato)
                <tr>
                    <td col width="60px"><strong>{{ $dato->id }}</strong></td>
                    <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->f_pago)->format('M j\\, Y') }}</td>
                    <td col width="130px" align="left">{{ $dato->trantipo->nombre }}</td>
                    <td col width="70px"><strong>{{ $dato->trans_no }}</strong></td>
                    <td>{{{ $dato->banco->nombre or '' }}}</td>
                    <td col width="90px" align="right">{{ $dato->monto }}</td>
                    <td col width="75px" align="right">
                        <a href="{{ URL::route('showRecibo', $dato->id) }}" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-list"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
      <div class="col-xs-12">
        <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
      </div>
    </div> 
            
</div>
<!--End Hover Rows-->
@stop

@section('relatedplugins')

    <script src="{{ URL::asset('assets/backend/js/libs/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/datatables/jquery.dataTables-1.10.15.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#dt_basic tfoot th').each( function () {
                var title = $('#dt_basic thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            } );
         
            // DataTable
            var table = $('#dt_basic').DataTable( {
                stateSave: true,
          
                 "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay datos disponibles para esta tabla",
                    "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ pagos",
                    "infoEmpty":      "",
                    "infoFiltered":   "",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ pagos",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encontró ningun pago con ese filtro",
                    "paginate": {
                        "first":      "Primer",
                        "last":       "Último",
                        "next":       "Próximo",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": active para ordenar ascendentemente",
                        "sortDescending": ": active para ordenar descendentemente"
                    }
                }
            } );           

            // Restore state
            if ( state ) {
              table.columns().eq( 0 ).each( function ( colIdx ) {
                var colSearch = state.columns[colIdx].search;
                
                if ( colSearch.search ) {
                  $( 'input', table.column( colIdx ).footer() ).val( colSearch.search );
                }
              } );
              
              table.draw();
            }
         
            // Apply the search
            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
                    table
                        .column( colIdx )
                        .search( this.value )
                        .draw();
                } );
            } );
        } );
    </script>
@stop