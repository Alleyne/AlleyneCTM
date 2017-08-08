
<meta charset="utf-8">
<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

<title>ctmaster.net @yield('title')</title> <!-- CHANGE THIS TITLE FOR EACH PAGE -->
<meta name="description" content="">
<meta name="author" content="">

<!-- Use the correct meta names below for your web application
	 Ref: http://davidbcalhoun.com/2010/viewport-metatag 
	 
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">-->

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Basic Styles -->
{{-- <link href="{{ URL::asset('assets/backend/css/jquery-datatables-1-10-12-min.css') }}" rel="stylesheet" type="text/css" media="screen"> --}}
<link href="{{ URL::asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">			

<link href="{{ URL::asset('assets/backend/css/bootstrap-datetimepicker-4.17.47.min.css') }}" rel="stylesheet" type="text/css" media="screen">			
<link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">

<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
<link href="{{ URL::asset('assets/backend/css/smartadmin-production.css') }}" rel="stylesheet" type="text/css" media="screen">		
{{-- <link href="{{ URL::asset('assets/backend/css/smartadmin-skins.css') }}" rel="stylesheet" type="text/css" media="screen"> 	 --}}	
<link href="{{ URL::asset('assets/backend/css/lockscreen.css') }}" rel="stylesheet" type="text/css" media="screen"> 		
<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
<link href="{{ URL::asset('assets/backend/css/demo.css') }}" rel="stylesheet" type="text/css" media="screen">	

<!-- SmartAdmin RTL Support is under construction
<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->

<!-- We recommend you use "your_style.css" to override SmartAdmin
     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css">
<link href="{{ URL::asset('assets/backend/css/ctmaster_style.css') }}" rel="stylesheet" type="text/css" media="screen">	 -->

<!-- FAVICONS -->
<link rel="shortcut icon" href="{{ URL::asset('assets/backend/img/favicon/favicon.ico') }}" type="image/x-icon">		
<link rel="icon" href="{{ URL::asset('assets/backend/img/favicon/favicon.ico') }}" type="image/x-icon">	
<link href="{{ URL::asset('assets/backend/css/toastr.min.css') }}" rel="stylesheet" type="text/css" media="screen">	
<link href="{{ URL::asset('assets/backend/css/jquery-datatables-1-10-12-min.css') }}" rel="stylesheet" type="text/css" media="screen">

<!-- GOOGLE FONT -->
<!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700"> -->

@yield('stylesheets')