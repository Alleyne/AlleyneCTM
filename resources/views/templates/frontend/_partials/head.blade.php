
<title>ctmaster.net @yield('title')</title> <!-- CHANGE THIS TITLE FOR EACH PAGE -->

<!-- Meta -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Favicon -->
<link rel="shortcut icon" href="favicon.ico">

<!-- CSS Global Compulsory -->
<link href="{{ URL::asset('assets/backend/css/jquery.datatables-1.10.12.min.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('assets/frontend/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- CSS Implementing Plugins -->
<link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/line-icons/line-icons.css') }}">            
<link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/font-awesome/css/font-awesome.min.css') }}">    
<!--<link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/flexslider/flexslider.css') }}"> -->   
<link rel="stylesheet" href="{{ URL::asset('assets/frontend/plugins/parallax-slider/css/parallax-slider.css') }}">   

<!-- CSS Theme -->    
<link rel="stylesheet" href="{{ URL::asset('assets/frontend/css/themes/default.css') }}" id="style_color">     

<!-- CSS Customization -->
<link rel="stylesheet" href="{{ URL::asset('assets/frontend/css/custom.css') }}">     

<!-- Another way to link stylesheets -->
<!-- {{ Html::style('frontend/css/style.css') }} -->
<link href="{{ asset('assets/frontend/css/style.css') }}" rel="stylesheet">

@yield('stylesheets')