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