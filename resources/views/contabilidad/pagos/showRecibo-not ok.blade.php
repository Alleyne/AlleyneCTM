<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <title> SmartAdmin </title>
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    </head>
    
    <body>
        <br>
        <!-- HEADER -->
        <header>
        </header>
        <!-- END HEADER -->

        <div class="container">
            <div class="row"><!-- row -->
                <div class="col-xs-10">
                    <address>
                      <strong>{{ $ph->nombre }}</strong><br>
                      795 Folsom Ave, Suite 600<br>
                      San Francisco, CA 94107<br>
                      <abbr title="Phone">P:</abbr> (123) 456-7890
                    </address>
                </div>
                <div class="col-xs-2">
                    <img style="height: 40px; border-radius: 8px;" src="{{asset('assets/backend/img/logo.png')}}" class="img-responsive" alt="Responsive image">
                </div>
            </div><!-- end row -->

            <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK">

            <div class="row">
                @if ($pago->anulado==1)
                    <h1><p class="text-center"><strong>ANULADO</strong></p></h1>
                @endif
            </div>
            
            <div class="row">
                <div class="col-md-8 text-md-center">
                    <div class="font-md">
                        <p><strong>RECIBO DE PAGO</strong></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="well well-sm bg-color-darken txt-color-white no-border">
                        <div class="fa-lg">
                            Total Recibido B/. 
                            <span class="pull-right"> {{ $pago->monto }} </span>
                        </div>
                    </div>
                    <br>
                    <br>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <p>RECIBÍ del Sr(a). {{ $prop->user->nombre_completo }}, propietario(a) del apartamento {{ $un->codigofull }}, 'la suma de '$detalle->monto', mediante 'banco' , tipo de pago 'no.' ' no transferencia '} del dia 'fecha', en concepto de pago por servicios de mantenimiento del condominio.' </p>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="row">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <strong><tr>
                                <th col width="40px">No</th>
                                <th>Detalle</th>
                                <th col width="90px" class="text-center">Monto</th>                     
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
                </div><!-- end row -->
                <div class="row">
                      <div class="col-xs-11">
                        <p class="text-right"><strong>Total pagado</strong></p>
                      </div>
                      <div class="col-xs-1">
                        <p class="text-right"><strong>{{ $total }}</strong></p>
                      </div>
                </div>

                <div class="row">
                      <div class="col-xs-11">
                        <p class="text-right"></p>
                      </div>
                      <div class="col-xs-1">
                        <p class="text-right">========</p>
                      </div>
                </div>
                <div class="row">
                      <div class="col-xs-11">
                        <p class="text-left">{{ $nota }}</p>
                      </div>
                      <div class="col-xs-1">
                        <p class="text-right"></p>
                      </div>
                </div>          
            </div>
        </div> <!-- end container -->
    </body>
</html>