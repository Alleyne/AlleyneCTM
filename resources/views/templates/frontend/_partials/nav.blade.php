<div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-bars"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::route('frontend') }}"></a>
            <img style="border-radius: 3px;" id="logo-header" src="{{ asset(Cache::get('jdkey')->imagen_S) }}" alt="Logo">
        </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-responsive-collapse">
        <ul class="nav navbar-nav">
            <!-- Inicio -->
            <li class="dropdown active">
                <a href="{{ URL::route('frontend') }}" class="dropdown-toggle">
                    Inicio
                </a>
            </li>
            <!-- End Inicio -->

            <!-- Articulos -->
            <li class="dropdown">
                <a href="{{ URL::route('blog') }}" class="dropdown-toggle">
                    Articulos
                </a>
            </li>
            <!-- End Articulos -->
            @if (Auth::check())
                @if (Cache::get('esPropietariokey') && Auth::user()->activated)
                    <!-- Reportes -->
                    <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Reportes</a>
                        <ul class="dropdown-menu">  
                            @foreach (Auth::user()->props as $un)
                                <li><a href="{{ URL::route('ecuentas', array($un->un_id, 'frontend')) }}">Estado de cuentas {{ $un->un->codigo }}</a></li>
                                <li><a href="{{ URL::route('indexPagosfrontend', array($un->un_id, $un->un->codigo)) }}">Recibos {{ $un->un->codigo }}</a></li>                                
                            @endforeach
                        </ul>            
                    </li>                              
                    <!-- End Reportes -->
                    
                    <!-- Gerencia -->
                    <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Gerencia</a>
                        <ul class="dropdown-menu">  
                            <li><a href="{{ URL::route('indexPeriodosfrontend') }}">Periodos contables</a></li>
                            <li><a href="{{ URL::route('eventCalendar') }}">Reservaciones</a></li>    
                            <li><a href="{{ URL::route('vigente') }}">Graficas periodo vigente</a></li>                                
                            <li><a href="{{ URL::route('historico') }}">Graficas historico</a></li>   
                        </ul>   
                    </li>
                    <!-- End Gerencia -->                    
                @endif
            @endif
            <!-- Directiva -->            
            <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Directiva</a>
                <ul class="dropdown-menu">  
                    <li><a href="{{ URL::route('directivos') }}">Junta Directiva</a></li>
                    <li><a href="{{ URL::route('reglamento') }}">Reglamentos</a></li>                                
                    <li><a href="{{ URL::route('contact') }}">Contactar</a></li>
                </ul>   
            </li>
            <!-- End Directiva -->

            <!-- Search Block -->
            <li>
                <i class="search fa fa-search search-btn"></i>
                <div class="search-open">
                    <div class="input-group animated fadeInDown">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-group-btn">
                            <button class="btn-u" type="button">Go</button>
                        </span>
                    </div>
                </div>    
            </li>
            <!-- End Search Block -->
        </ul>
    </div><!--/navbar-collapse-->
</div>    