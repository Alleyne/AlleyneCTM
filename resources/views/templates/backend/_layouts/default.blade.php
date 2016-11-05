<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> ctmaster.net </title>
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Use the correct meta names below for your web application
			 Ref: http://davidbcalhoun.com/2010/viewport-metatag 
			 
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">-->
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<!-- Basic Styles -->
		<link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
		<link href="{{ URL::asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">			
		<link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link href="{{ URL::asset('assets/backend/css/smartadmin-production.css') }}" rel="stylesheet" type="text/css" media="screen">		
		<link href="{{ URL::asset('assets/backend/css/smartadmin-skins.css') }}" rel="stylesheet" type="text/css" media="screen"> 		
		<link href="{{ URL::asset('assets/backend/css/lockscreen.css') }}" rel="stylesheet" type="text/css" media="screen"> 		
		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link href="{{ URL::asset('assets/backend/css/demo.css') }}" rel="stylesheet" type="text/css" media="screen">	
		
		<!-- SmartAdmin RTL Support is under construction
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->
		<link href="{{ URL::asset('assets/backend/css/ctmaster_style.css') }}" rel="stylesheet" type="text/css" media="screen">	


		<!-- FAVICONS -->
		<link rel="shortcut icon" href="{{ URL::asset('assets/backend/img/favicon/favicon.ico') }}" type="image/x-icon">		
		<link rel="icon" href="{{ URL::asset('assets/backend/img/favicon/favicon.ico') }}" type="image/x-icon">	
		
		<!-- GOOGLE FONT -->
		<!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700"> -->

	</head>
	<body class="container">
		<!-- possible classes: minified, fixed-ribbon, fixed-header, fixed-width-->

		<!-- HEADER -->
		<header id="header">
			<div id="logo-group">

				<!-- PLACE YOUR LOGO HERE -->
				<span id="logo"> <img src="{{asset('assets/backend/img/logo.png')}}" alt="SmartAdmin"> </span>

				<!-- END LOGO PLACEHOLDER -->

				<!-- Note: The activity badge color changes when clicked and resets the number to 0
				Suggestion: You may want to set a flag when this happens to tick off all checked messages / notifications -->
				<span id="activity" class="activity-dropdown"> <i class="fa fa-user"></i> <b class="badge"> 21 </b> </span>

				<!-- AJAX-DROPDOWN : control this dropdown height, look and feel from the LESS variable file -->
				<div class="ajax-dropdown">

					<!-- the ID links are fetched via AJAX to the ajax container "ajax-notifications" -->
					<div class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default">
							<input type="radio" name="activity" id="ajax/notify/mail.html">
							Msgs (14) </label>
						<label class="btn btn-default">
							<input type="radio" name="activity" id="ajax/notify/notifications.html">
							notify (3) </label>
						<label class="btn btn-default">
							<input type="radio" name="activity" id="ajax/notify/tasks.html">
							Tasks (4) </label>
					</div>

					<!-- notification content -->
					<div class="ajax-notifications custom-scroll">

						<div class="alert alert-transparent">
							<h4>Click a button to show messages here</h4>
							This blank page message helps protect your privacy, or you can show the first message here automatically.
						</div>

						<i class="fa fa-lock fa-4x fa-border"></i>

					</div>
					<!-- end notification content -->

					<!-- footer: refresh area -->
					<span> Last updated on: 12/12/2013 9:43AM
						<button type="button" data-loading-text="<i class='fa fa-refresh fa-spin'></i> Loading..." class="btn btn-xs btn-default pull-right">
							<i class="fa fa-refresh"></i>
						</button> </span>
					<!-- end footer -->

				</div>
				<!-- END AJAX-DROPDOWN -->
			</div>

			<!-- projects dropdown -->
			<div id="project-context">

				<span class="label">Projectos:</span>
				<span id="project-selector" class="popover-trigger-element dropdown-toggle" data-toggle="dropdown">Recent projects <i class="fa fa-angle-down"></i></span>

				<!-- Suggestion: populate this list with fetch and push technique -->
				<ul class="dropdown-menu">
					<li>
						<a href="javascript:void(0);">Online e-merchant management system - attaching integration with the iOS</a>
					</li>
					<li>
						<a href="javascript:void(0);">Notes on pipeline upgradee</a>
					</li>
					<li>
						<a href="javascript:void(0);">Assesment Report for merchant account</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="javascript:void(0);"><i class="fa fa-power-off"></i> Clear</a>
					</li>
				</ul>
				<!-- end dropdown-menu-->

			</div>
			<!-- end projects dropdown -->

			<!-- pulled right: nav area -->
			<div class="pull-right">

				<!-- collapse menu button -->
				<div id="hide-menu" class="btn-header pull-right">
					<span> <a href="javascript:void(0);" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
				</div>
				<!-- end collapse menu -->

				<!-- logout button
				<div id="logout" class="btn-header transparent pull-right">
					<span> <a href="{{ url('/logout') }}" title="Sign Out"><i class="fa fa-sign-out"></i></a> </span>
				</div> -->
				<!-- end logout button -->

				<!-- search mobile button (this is hidden till mobile view port) -->
				<div id="search-mobile" class="btn-header transparent pull-right">
					<span> <a href="javascript:void(0)" title="Search"><i class="fa fa-search"></i></a> </span>
				</div>
				<!-- end search mobile button -->

				<!-- input: search field -->
				<form action="#search.html" class="header-search pull-right">
					<input type="text" placeholder="Find reports and more" id="search-fld">
					<button type="submit">
						<i class="fa fa-search"></i>
					</button>
					<a href="javascript:void(0);" id="cancel-search-js" title="Cancel Search"><i class="fa fa-times"></i></a>
				</form>
				<!-- end input: search field -->

				<!-- multiple lang dropdown : find all flags in the image folder -->
				<ul class="header-dropdown-list hidden-xs">
					<li>
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img alt="" src="{{asset('assets/backend/img/flags/us.png')}}"> <span> US </span> <i class="fa fa-angle-down"></i> </a>
						<ul class="dropdown-menu pull-right">
							<li class="active">
								<a href="javascript:void(0);"><img alt="" src="{{asset('assets/backend/img/flags/us.png')}}"> US</a>
							</li>
							<li>
								<a href="javascript:void(0);"><img alt="" src="{{asset('assets/backend/img/flags/es.png')}}"> Spanish</a>
							</li>
							<li>
								<a href="javascript:void(0);"><img alt="" src="{{asset('assets/backend/img/flags/de.png')}}"> German</a>
							</li>
						</ul>
					</li>
				</ul>
				<!-- end multiple lang -->

			</div>
			<!-- end pulled right: nav area -->

		</header>
		<!-- END HEADER -->

		<!-- Left panel : Navigation area -->
		<!-- Note: This width of the aside area can be adjusted through LESS variables -->
		<aside id="left-panel">

			<!-- User info -->
			<div class="login-info">
				<span> <!-- User image size is adjusted inside CSS, it should stay as it --> 
					<a href="javascript:void(0);" id="show-shortcut">
						@if (Auth::check())
							<img src="{{asset(Auth::user()->imagen)}}" alt="me" class="online" /> 
							<span><a href="#" class="navbar-link">  {{Cache::get('userFullNamekey')}}</a><span> 
				        @else
				            <span>Invitado</span>
				        @endif 
						
						<i class="fa fa-angle-down"></i>
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
					@include('templates.backend._partials.navigation')
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
						Sistema de administración de Propiedades Horizontales...
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
	                @include('templates.backend._partials.flash_messages')
	                @yield('main') 
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
		<div id="shortcut">
			<ul>
				<li>
					<a href="#inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-envelope fa-4x"></i> <span>Mail <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
				</li>
				<li>
					<a href="#calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
				</li>
				<li>
					<a href="#gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
				</li>
				<li>
					<a href="#invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Invoice <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
				</li>
				<li>
					<a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
				</li>
				<li>
					<a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
				</li>
			</ul>
		</div>
		<!-- END SHORTCUT AREA -->

		<!--================================================== -->
		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>
		<script data-pace-options='{ "restartOnRequestAfter": true }'src="{{ URL::asset('assets/backend/js/plugin/pace/pace.min.js') }}"></script>-->

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script> 
		
		<!-- <script>
			if (!window.jQuery) {
				<script src="{{ URL::asset('assets/backend/js/libs/jquery-2.0.2.min.js') }}"></script>
			}
		</script> -->

		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<!--<script>
			if (!window.jQuery.ui) {
				<script src="{{ URL::asset('assets/backend/js/libs/jquery-ui-1.10.3.min.js.js') }}"></script>
			}
		</script> -->

		<!-- <script src="{{ URL::asset('assets/backend/js/libs/jquery-2.0.2.min.js') }}"></script>
		<script src="{{ URL::asset('assets/backend/js/libs/jquery-ui-1.10.3.min.js.js') }}"></script> -->
		
		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->
		<script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap.min.js') }}"></script>
		
		<!-- CUSTOM NOTIFICATION -->
		<script src="{{ URL::asset('assets/backend/js/notification/SmartNotification.min.js') }}"></script>
		
		<!-- JARVIS WIDGETS -->
		<script src="{{ URL::asset('assets/backend/js/smartwidgets/jarvis.widget.min.js') }}"></script>
		
		<!-- EASY PIE CHARTS
		<script src="{{ URL::asset('assets/backend/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js') }}"></script> --> 
		
		<!-- SPARKLINES
		<script src="{{ URL::asset('assets/backend/js/plugin/sparkline/jquery.sparkline.min.js') }}"></script> -->
		
		<!-- JQUERY VALIDATE -->
		<script src="{{ URL::asset('assets/backend/js/plugin/jquery-validate/jquery.validate.min.js') }}"></script>
		
		<!-- JQUERY MASKED INPUT
		<script src="{{ URL::asset('assets/backend/js/plugin/masked-input/jquery.maskedinput.min.js') }}"></script> -->
		
		<!-- JQUERY SELECT2 INPUT
		<script src="{{ URL::asset('assets/backend/js/plugin/select2/select2.min.js') }}"></script> -->
		
		<!-- JQUERY UI + Bootstrap Slider
		<script src="{{ URL::asset('assets/backend/js/plugin/bootstrap-slider/bootstrap-slider.min.js') }}"></script> -->
		
		<!-- browser msie issue fix -->
		<script src="{{ URL::asset('assets/backend/js/plugin/msie-fix/jquery.mb.browser.min.js') }}"></script>
		
		<!-- FastClick: For mobile devices
		<script src="{{ URL::asset('assets/backend/js/plugin/fastclick/fastclick.js') }}"></script> -->
		
		<!--[if IE 7]>

		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->

		<!-- Demo purpose only-->
		<!-- <script src="{{ URL::asset('assets/backend/js/demo.js') }}"></script> -->
		
		<!-- MAIN APP JS FILE-->
		<script src="{{ URL::asset('assets/backend/js/app.js') }}"></script> 
		
		<!-- PAGE RELATED PLUGIN(S) -->
		@yield('relatedplugins')

	</body>
</html>