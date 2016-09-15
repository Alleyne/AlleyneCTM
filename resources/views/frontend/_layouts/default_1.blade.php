<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Unify | Welcome...</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- CSS Global Compulsory -->
    <link href="{{ asset('assets/frontend/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/css/style.css') }}" rel="stylesheet">
    
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/line-icons/line-icons.css') }}">            
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/font-awesome/css/font-awesome.min.css') }}">    
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/flexslider/flexslider.css') }}">    
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/parallax-slider/css/parallax-slider.css') }}">     

    <!-- CSS Theme -->    
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/css/themes/default.css') }}" id="style_color">     

    <!-- CSS Customization -->
    <link rel="stylesheet" href="{{ URL::asset('assets/frontend/css/custom.css') }}">     

</head> 

<body>
<!--=== Style Switcher ===-->    
<i class="style-switcher-btn fa fa-cogs hidden-xs"></i>
<div class="style-switcher animated fadeInRight">
    <div class="theme-close"><i class="icon-close"></i></div>
    <div class="theme-heading">Theme Colors</div>
    <ul class="list-unstyled">
        <li class="theme-default theme-active" data-style="default" data-header="light"></li>
        <li class="theme-blue" data-style="blue" data-header="light"></li>
        <li class="theme-orange" data-style="orange" data-header="light"></li>
        <li class="theme-red" data-style="red" data-header="light"></li>
        <li class="theme-light last" data-style="light" data-header="light"></li>

        <li class="theme-purple" data-style="purple" data-header="light"></li>
        <li class="theme-aqua" data-style="aqua" data-header="light"></li>
        <li class="theme-brown" data-style="brown" data-header="light"></li>
        <li class="theme-dark-blue" data-style="dark-blue" data-header="light"></li>
        <li class="theme-light-green last" data-style="light-green" data-header="light"></li>
    </ul>
    <div style="margin-bottom:18px;"></div>
    <div class="theme-heading">Layouts</div>
    <div class="text-center">
        <a href="javascript:void(0);" class="btn-u btn-u-green btn-block active-switcher-btn wide-layout-btn">Wide</a>
        <a href="javascript:void(0);" class="btn-u btn-u-green btn-block boxed-layout-btn">Boxed</a>
    </div>
</div><!--/style-switcher-->
<!--=== End Style Switcher ===-->    

<div class="wrapper">
    <!--=== Header ===-->    
    <div class="header">
        <!-- Topbar -->
        <div class="topbar">
            <div class="container">
                <!-- Topbar Navigation -->
                <ul class="loginbar pull-right">
             <!--        <li>
                        <i class="fa fa-globe"></i>
                        <a>Languages</a>
                        <ul class="lenguages">
                            <li class="active">
                                <a href="#">English <i class="fa fa-check"></i></a> 
                            </li>
                            <li><a href="#">Spanish</a></li>
                            <li><a href="#">Russian</a></li>
                            <li><a href="#">German</a></li>
                        </ul>
                        <li class="topbar-devider"></li>
                    </li> -->
                    
                    <li>
                        <i class="fa fa-user"></i>
                            @if (Auth::check())
                                <span><a href="#" class="navbar-link">  {{Auth::user()->username}}</a><span> 
                            @else
                                <span>Invitado</span>
                            @endif 
                    </li>
                    
                    @if(Auth::check())
                        <li class="topbar-devider"></li>   
                        <li class="topbar-devider"></li>   
                        <li><a href="{{ url('/logout') }}">Logout</a></li>
                    @else
                        <li class="topbar-devider"></li>   
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li class="topbar-devider"></li>  
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @endif
                </ul>
                <!-- End Topbar Navigation -->
            </div>
        </div>
        <!-- End Topbar -->
        <!-- Navbar -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="fa fa-bars"></span>
                    </button>
                    <a class="navbar-brand" href="{{ URL::route('frontend') }}"></a>
                        <img id="logo-header" src="{{asset('assets/frontend/img/logo1-default.png')}}" alt="Logo">
                    </a>
                </div>
                @include('frontend._partials.main_menu')        
            </div>    
        </div>      
        <!-- End Navbar -->
    </div>
    <!--=== End Header ===-->    
    
    <!-- Contenido -->
        {!! Notification::showAll() !!}
        @yield('main')
        @include('frontend._partials.contenido') 
    
    <!--=== End Contenido ===-->

</div><!--/wrapper-->

<!-- JS Global Compulsory -->           
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/jquery-1.10.2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/jquery-migrate-1.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- JS Implementing Plugins -->
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/back-to-top.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/flexslider/jquery.flexslider-min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/parallax-slider/js/modernizr.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/plugins/parallax-slider/js/jquery.cslider.js') }}"></script>

<!-- JS Page Level -->           
<script type="text/javascript" src="{{ URL::asset('assets/frontend/js/app.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/frontend/js/pages/index.js') }}"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        App.init();
        App.initSliders();
        Index.initParallaxSlider();        
    });
</script>
</body>
</html> 