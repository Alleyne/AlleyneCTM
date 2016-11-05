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

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-responsive-collapse">
        <ul class="nav navbar-nav">
            <!-- Home -->
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                    Home
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::route('frontend') }}">Home</a></li>
                </ul>
            </li>
            <!-- End Home -->

            <!-- Portfolio -->
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                    About Us
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::route('about') }}">About Us</a></li>
                </ul>
            </li>
            <!-- Ens Portfolio -->

            <!-- Blog -->
            <li class="dropdown active">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                    Blog
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::route('blog') }}">Blogs</a></li>
                </ul>
            </li>
            <!-- End Blog -->

            <!-- Contacts -->
            <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                    Contacts
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::route('contact') }}">Contact Us</a></li>
                </ul>
            </li>                    
            <!-- End Contacts -->

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