<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Recibo</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    <style type="text/css">
      @page { margin: 0px; }
      html { margin: 0px}
     
      body {
        margin: 0px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        font-style: normal;
        line-height: normal;
        font-weight: normal;
      }
      
      .contenedor-principal {
        height: 11in;
        width: 8.5in;
        padding: 0.5in;
        margin-right: auto;
        margin-left: auto;
      }
      
      .p20 {
        font-size: 20px; 
      }
      
      p {
        font-size: 14px; 
      }
    
    hr {
        margin-top: 0px;
        margin-bottom: 20px;
        border: 0;
        border-top: 1px solid #eee;
    }

    </style>
  </head>
  
  <body>
    <div class="contenedor-principal">
      
      <div class="row"><!-- row -->
          <div class="col-xs-9">
              <address>
                <strong>{{ Cache::get('jdkey')->nombre }}</strong><br>
                {{ Cache::get('jdkey')->calle }}, {{ Cache::get('jdkey')->corregimiento }}<br>
                {{ Cache::get('jdkey')->distrito }}, {{ Cache::get('jdkey')->provincia }}, {{ Cache::get('jdkey')->pais }} <br>
                Tel: {{ Cache::get('jdkey')->telefono }} Email: {{ Cache::get('jdkey')->email }}<br>
              </address>
          </div>
          <div class="col-xs-3" align="right">
              <img style="border-radius: 8px;" src="{{ asset(Cache::get('jdkey')->imagen_S) }}" class="img-responsive" alt="Responsive image">
          </div>
      </div><!-- end row  -->

      <div class="row">
        <div class="col-md-12">
          @if ($pago->anulado==1)
              <p class="text-center p20"><strong>ANULADO</strong></p>
          @endif
        </div>
      </div>    

      <br>
      <br>
      {{-- <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK"> --}}
      
      <div class="row">
          <div class="col-xs-7">
              <div>
                  <h4><strong>RECIBO DE PAGO</strong></h4>
                  <h5><strong>No. {{ sprintf("%06d", $pago->id) }} </strong></h5>
              </div>
          </div>
          
          <div class="col-xs-5">
              <div class="well well-sm pull-right">
                 <h4>Total Recibido &nbsp;&nbsp; <strong>B/. {{ $pago->monto }}</strong></h4>
              </div>
              <br>
          </div>
      </div>
      
      <br>
      
      <div class="row">
        <div class="col-md-12">
           <p class="text-justify">RECIBIMOS de <strong>{{ $prop->user->nombre_completo }}</strong>, propietario(a) del apartamento <strong>{{ $pago->un->codigofull }}</strong>, la suma de <strong>B/. {{ $pago->monto }}</strong>, mediante <strong>{{ $pago->trantipo->nombre }} {{{ $pago->trans_no or '' }}}</strong> el dia <strong>{{ Date::parse($pago->f_pago)->format('l\, j F Y') }}</strong>, en concepto de {{ $pago->concepto }} del {{ Cache::get('jdkey')->nombre }}.</p>
        </div>
      </div>
      
      <br>
      @if ($total > 0)       
        <div class="row">
          <div class="col-md-12">
            <p>Acontinuacion se deglosa la forma en que se contabilizo su pago:</p>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
         
              <div class="row">
                <div class="col-md-12">
                    <table class="table table-condensed">
                        <thead>
                          <strong><tr>
                              <th col width="40px">No</th>
                              <th>Detalle</th>
                              <th col width="90px" class="text-right">Monto</th>                     
                          </tr></strong>
                        </thead>
                        <tbody>
                          @foreach ($detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->no }}</td>
                                <td>{{ $detalle->detalle }}</td>
                                <td col width="90px" align="right">{{ $detalle->monto }}</td>                      
                            </tr>
                          @endforeach
                        </tbody>
                    </table>    
                </div>
              </div><!-- end row -->

              <div class="row">
                <div class="col-md-3 col-md-offset-9">
                  <p class="text-right"><strong>Total B/. {{ $total }}</strong></p>
                </div>
              </div>
              
              <br>
          </div>        
        </div>        
      @endif       
      
      @if ($nota)
        <div class="row">
          <div class="col-md-12">
            <p class="text-justify">{{ $nota }}</p>
          </div>
        </div>          
      @endif
      <br>       
          
      <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK">

      <div class="row">
        <div class="col-md-12">
          <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
        </div>
      </div>         
    </div> <!-- end container -->
  </body>
</html>