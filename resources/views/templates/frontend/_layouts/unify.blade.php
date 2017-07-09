<!DOCTYPE html>
<html lang="en">
  <head>
    @include('templates.frontend._partials.head')
  </head>
  
  <body>
    <!--=== Style Switcher ===-->    
    <i class="style-switcher-btn fa fa-cogs hidden-xs"></i>
    <div class="style-switcher animated fadeInRight">
      @include('templates.frontend._partials.switcher')     
    </div><!--/style-switcher-->
    <!--=== End Style Switcher ===--> 

    <div class="wrapper">
      <!--=== Header ===-->    
      <div class="header">
        <!-- Topbar -->
        <div class="topbar">
          @include('templates.frontend._partials.header')            
        </div>
        <!-- End Topbar -->
     
        <!-- Navbar -->
        <div class="navbar navbar-default" role="navigation">
          @include('templates.frontend._partials.nav')
        </div>      
        <!-- End Navbar -->
      </div>
      <!--=== End Header ===--> 
      
      <!--=== Slider ===-->
      @yield('slider')   
      <!--=== End Slider ===-->  
      
      <!--=== Content Part ===-->
      <div class="container content">   
        <!-- @include('templates.frontend._partials.messages') -->
        @include('templates.frontend._partials.flash_messages')
        @yield('content')      
      </div><!--/container-->   
      <!--=== End Content Part ===-->

      <!--=== Footer ===-->
      <div class="footer">
          <div class="container">
            @include('templates.frontend._partials.footer')          
          </div> 
      </div><!--/footer-->
      <!--=== End Footer ===-->
    
      <!--=== Copyright ===-->
      <div class="copyright">
        @include('templates.frontend._partials.copyright')
      </div> 
      <!--=== End Copyright ===-->
    </div><!--/wrapper-->
    
    @include('templates.frontend._partials.javascript')
    @yield('relatedplugins')

  </body>
</html>