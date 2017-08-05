<!DOCTYPE html>
<html lang="en-us">
  <head>
    @include('templates.backend._partials.head')
  </head>
  <body class="container">
    <!-- possible classes: minified, fixed-ribbon, fixed-header, fixed-width-->

    <!-- HEADER -->
    <header id="header">
      @include('templates.backend._partials.header')
    </header>
    <!-- END HEADER -->

    <!-- Left panel : Navigation area -->
    <!-- Note: This width of the aside area can be adjusted through LESS variables -->
    <aside id="left-panel">

      <!-- User info -->
      <div class="login-info">
        <span> <!-- User image size is adjusted inside CSS, it should stay as it --> 
          <a href="#">
            @if (Auth::check())
              <img src="{{asset(Auth::user()->imagen_S)}}" alt="imagen del usuario" class="online"> 
              <span><a href="#" class="navbar-link"> {{ Auth::user()->username }}</a></span> 
            @else
                <span>Invitado</span>
            @endif 
          </a> 
        </span>
      </div>
      <!-- end user info -->

      <!-- NAVIGATION : This navigation is also responsive

      To make this navigation dynamic please make sure to link the node
      (the reference to the nav > ul) after page load. Or the navigation
      will not initialize.
      -->
      <nav>
        <!-- NOTE: Notice the gaps after each icon usage <i></i>..
        Please note that these links work a bit different than
        traditional hre="" links. See documentation for details.
        -->
        <ul>
          @include('templates.backend._partials.nav')
        </ul>
      </nav>
      <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>

    </aside>
    <!-- END NAVIGATION -->

    <!-- MAIN PANEL -->
    <div id="main" role="main">

      <!-- RIBBON -->
      <div id="ribbon">

        <span class="ribbon-button-alignment"> <span id="refresh" class="btn btn-ribbon" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true"><i class="fa fa-refresh"></i></span> </span>

        <!-- breadcrumb -->
        <ol class="breadcrumb">
          <li>
            Sistema para la administración transparente de propiedades...
          </li>
        </ol>
        <!-- end breadcrumb -->

        <!-- You can also add more buttons to the
        ribbon for further usability

        Example below:

        <span class="ribbon-button-alignment pull-right">
        <span id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa-grid"></i> Change Grid</span>
        <span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa-plus"></i> Add</span>
        <span id="search" class="btn btn-ribbon" data-title="search"><i class="fa-search"></i> <span class="hidden-mobile">Search</span></span>
        </span> -->

      </div>
      <!-- END RIBBON -->

      <!-- MAIN CONTENT -->
      <div id="content">
        <!-- widget grid -->
        <section id="widget-grid" class="">
          <!--@ --> <!--include('templates.backend._partials.flash_messages')-->
          @yield('content') 
        </section>
        <!-- end widget grid -->
      </div>
      <!-- END MAIN CONTENT -->

    </div>
    <!-- END MAIN PANEL -->
    
    <!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
    Note: These tiles are completely responsive,
    you can add as many as you like
    -->
    @include('templates.backend._partials.shortcuts')
    <!-- END SHORTCUT AREA -->    

    @include('templates.backend._partials.javascript')
    @yield('relatedplugins')

  </body>
</html>