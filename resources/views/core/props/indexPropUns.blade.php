@extends('templates.frontend._layouts.default_1')

@section('main')
<div class="row magazine-page">
    @foreach ($datos as $dato)
    <!--Info Block-->
    <div class="col-md-12">
        <div class="funny-boxes funny-boxes-top-sea">
            <div class="row">
                <div class="col-md-8">
                    <h2>{{ $dato->un->codigo }}</h2>
                    @if ($dato->estatus== 'Moroso')
                        <span class="label label-danger">Estatus actual {{ $dato->estatus }}</span>  
                    @else
                        <span class="label label-success">Estatus actual {{ $dato->estatus }}</span> 
                    @endif
                    <p>Unidad ubicada en el piso 15-D, Torre 200 del Ph El Marquez, cuenta con 2 habitaciones 2.5 baños, balcon, sala comedor.</p>
                </div>
                <div class="col-md-4 funny-boxes-img">
                    <!-- <img class="img-responsive" src="{{asset('assets/site/img/main/2.jpg')}}" alt=""> -->
                </div>
            </div>                            
        
            <!-- Top Categories -->
            <div class="row category margin-bottom-20">
                <!-- Info Blocks -->
                <div class="col-md-4">
                    <div class="content-boxes-v3">
                        <i class="icon-custom icon-sm rounded-x icon-bg-light-grey fa fa-hdd-o"></i>
                        <div class="content-boxes-in-v3">
                            <h3><a href="{{ URL::route('genera_estado_de_cuenta', array($dato->un->id, 'corto')) }}"> Estado de Cuentas</a></h3>
                            <p>Permite ver en pantalla el Estado de cuenta actualizado en formato resumido.</p>
                        </div>
                    </div>
                    <div class="content-boxes-v3">
                        <i class="icon-custom icon-sm rounded-x icon-bg-light-grey icon-line icon-badge"></i>
                        <div class="content-boxes-in-v3">
                            <h3><a href="{{ URL::route('indexProps', array($dato->un->id, $dato->un->seccione_id, $goback)) }}"> Ver Propietarios</a></h3>
                            <p>Permite ver, vincular o desvincular propietarios.</p>
                        </div>
                    </div>                 
                    <div class="content-boxes-v3">
                        <i class="icon-custom icon-sm rounded-x icon-bg-light-grey icon-line icon-graduation"></i>
                        <div class="content-boxes-in-v3">
                            <h3><a href="{{ URL::route('editUn', $dato->un->id) }}"> Editar propiedad</a></h3>
                            <p>Permite editar datos, tales como número de finca, documento, etc.</p>
                        </div>
                    </div>
                </div>    
                <!-- End Info Blocks -->
            </div>    
            <!-- End Top Categories -->
        </div>
    </div>
    <!--End Info Block-->
    @endforeach
</div>
@stop