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
                    Ver Articulos
                </a>
            </li>
            <!-- End Articulos -->
                        
            @if (Auth::check())
                @foreach (Auth::user()->roles as $role)
                    @if($role->name === 'Propietario' && Auth::user()->activated)
                        

                        <!-- Reportes -->
                        <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Reportes</a>
                            <ul class="dropdown-menu">  
                                @foreach (Auth::user()->props as $un)
                                    <li><a href="{{ URL::route('ecuentas', array($un->un_id, 'frontend')) }}">Estado de cuentas {{ $un->un->codigo }}</a></li>
                                    <li><a href="{{ URL::route('indexPagosfrontend', array($un->un_id, $un->un->codigo)) }}">Recibos {{ $un->un->codigo }}</a></li>                                
                                    <li>&nbsp;</li>                                
                                @endforeach
                            </ul>            
                        </li>                              
                        <!-- End Reportes -->
                        
                        <!-- Gerencia -->
                        <li class="dropdown"><a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Gerencia</a>
                            <ul class="dropdown-menu">  
                                <li><a href="{{ URL::route('indexPeriodosfrontend') }}">Periodos contables</a></li>
                                <li><a href="{{ URL::route('vigente') }}">Graficas periodo vigente</a></li>                                
                                <li><a href="{{ URL::route('historico') }}">Graficas historico</a></li>   
                            </ul>   
                        </li>
                        <!-- End Gerencia -->                    
                    @endif
                @endforeach
                <!-- Reservaciones -->
                <li class="dropdown">
                    <a href="{{ URL::route('eventCalendar') }}" class="dropdown-toggle">
                        Reservaciones
                    </a>
                </li>
                <!-- End Reservaciones -->

            @endif
            
            <!-- Junta Directiva -->
            <li class="dropdown">
                <a href="{{ URL::route('directivos') }}" class="dropdown-toggle">
                    Directivos
                </a>
            </li>
            <!-- End Junta Directiva -->

            <!-- Contactar -->
            <li class="dropdown">
                <a href="{{ URL::route('contact') }}" class="dropdown-toggle">
                    Contactar
                </a>
            </li>                    
            <!-- End Contactar -->

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