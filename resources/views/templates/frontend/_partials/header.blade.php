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