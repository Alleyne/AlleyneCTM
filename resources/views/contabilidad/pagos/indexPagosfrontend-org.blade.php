@extends('templates.frontend._layouts.unify')

@section('title', '| Pagos efectuados')

@section('content')

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
</style>

<div class="contenedor-principal">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Pagos realizados apartamento {{ $codigo }}</h3>
    </div>
    <div class="panel-body">
        <table id="dt_basic" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TIPO</th>  
                    <th>NO</th>
                    <th>BANCO</th>                          
                    <th class="text-left">FECHA</th>                                   
                    <th class="text-right">MONTO</th> 
                    <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                </tr>
            </thead>
            <tbody>
                @foreach ($datos as $dato)
                    <tr>
                        <td col width="60px"><strong>{{ $dato->id }}</strong></td>
                        <td col width="100px" align="left">{{ $dato->trantipo->nombre }}</td>
                        <td col width="70px"><strong>{{ $dato->trans_no }}</strong></td>
                        <td>{{ $dato->banco->nombre }}</td>
                        <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->f_pago)->format('M j\\, Y') }}</td>
                        <td col width="90px" align="right">{{ $dato->monto }}</td>
                        <td col width="175px" align="right">
                            <ul class="demo-btns">
                                <li>
                                    <a href="{{ URL::route('showRecibo', $dato->id) }}" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-list"></i></a>
                                </li>                
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK">

        <div class="row">
          <div class="col-xs-12">
            <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
          </div>
        </div> 
    </div>
  </div>
</div>
@stop

@section('scripts')
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/jquery.dataTables-cust.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/ColReorder.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
 
        $('#dt_basic').dataTable({
            "sPaginationType" : "bootstrap_full"
        });
    })
    </script>
@stop