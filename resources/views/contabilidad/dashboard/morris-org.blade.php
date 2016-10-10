<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> SmartAdmin </title>
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Use the correct meta names below for your web application
			 Ref: http://davidbcalhoun.com/2010/viewport-metatag 
			 
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">-->
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.css">

		<!-- SmartAdmin RTL Support is under construction
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/demo.css">

		<!-- FAVICONS -->
		<link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

	</head>
	<body class="">
		<!-- possible classes: minified, fixed-ribbon, fixed-header, fixed-width-->

		<!-- HEADER -->
		<header id="header">
			<div id="logo-group">

				<!-- PLACE YOUR LOGO HERE -->
				<span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
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

				<span class="label">Projects:</span>
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

				<!-- logout button -->
				<div id="logout" class="btn-header transparent pull-right">
					<span> <a href="login.html" title="Sign Out"><i class="fa fa-sign-out"></i></a> </span>
				</div>
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
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img alt="" src="img/flags/us.png"> <span> US </span> <i class="fa fa-angle-down"></i> </a>
						<ul class="dropdown-menu pull-right">
							<li class="active">
								<a href="javascript:void(0);"><img alt="" src="img/flags/us.png"> US</a>
							</li>
							<li>
								<a href="javascript:void(0);"><img alt="" src="img/flags/es.png"> Spanish</a>
							</li>
							<li>
								<a href="javascript:void(0);"><img alt="" src="img/flags/de.png"> German</a>
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
						<img src="img/avatars/sunny.png" alt="me" class="online" /> 
						<span>
							john.doe 
						</span>
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
					<li>
						<a href="index.html" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Dashboard</span></a>
					</li>
					<li>
						<a href="inbox.html"><i class="fa fa-lg fa-fw fa-inbox"></i> <span class="menu-item-parent">Inbox</span><span class="badge pull-right inbox-badge">14</span></a>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Graphs</span></a>
						<ul>
							<li>
								<a href="flot.html">Flot Chart</a>
							</li>
							<li class="active">
								<a href="morris.html">Morris Charts</a>
							</li>
							<li>
								<a href="inline-charts.html">Inline Charts</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Tables</span></a>
						<ul>
							<li>
								<a href="table.html">Normal Tables</a>
							</li>
							<li>
								<a href="datatables.html">Data Tables</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-pencil-square-o"></i> <span class="menu-item-parent">Forms</span></a>
						<ul>
							<li>
								<a href="form-elements.html">Smart Form Elements</a>
							</li>
							<li>
								<a href="form-templates.html">Smart Form Layouts</a>
							</li>
							<li>
								<a href="validation.html">Smart Form Validation</a>
							</li>
							<li>
								<a href="bootstrap-forms.html">Bootstrap Form Elements</a>
							</li>
							<li>
								<a href="plugins.html">Form Plugins</a>
							</li>
							<li>
								<a href="wizard.html">Wizards</a>
							</li>
							<li>
								<a href="other-editors.html">Bootstrap Editors</a>
							</li>
							<li>
								<a href="dropzone.html">Dropzone <span class="badge pull-right inbox-badge bg-color-yellow">new</span></a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">UI Elements</span></a>
						<ul>
							<li>
								<a href="general-elements.html">General Elements</a>
							</li>
							<li>
								<a href="buttons.html">Buttons</a>
							</li>
							<li>
								<a href="#">Icons</a>
								<ul>
									<li>
										<a href="fa.html"><i class="fa fa-plane"></i> Font Awesome</a>
									</li>	
									<li>
										<a href="glyph.html"><i class="glyphicon glyphicon-plane"></i> Glyph Icons </a>
									</li>
								</ul>
							</li>
							<li>
								<a href="grid.html">Grid</a>
							</li>
							<li>
								<a href="treeview.html">Tree View</a>
							</li>
							<li>
								<a href="nestable-list.html">Nestable Lists</a>
							</li>
							<li>
								<a href="jqui.html">JQuery UI</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-folder-open"></i> <span class="menu-item-parent">6 Level Navigation</span></a>
						<ul>
							<li>
								<a href="#"><i class="fa fa-fw fa-folder-open"></i> 2nd Level</a>
								<ul>
									<li>
										<a href="#"><i class="fa fa-fw fa-folder-open"></i> 3ed Level </a>
										<ul>
											<li>
												<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
											</li>
											<li>
												<a href="#"><i class="fa fa-fw fa-folder-open"></i> 4th Level</a>
												<ul>
													<li>
														<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
													</li>
													<li>
														<a href="#"><i class="fa fa-fw fa-folder-open"></i> 5th Level</a>
														<ul>
															<li>
																<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
															</li>
															<li>
																<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
															</li>
														</ul>
													</li>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href="#"><i class="fa fa-fw fa-folder-open"></i> Folder</a>

								<ul>
									<li>
										<a href="#"><i class="fa fa-fw fa-folder-open"></i> 3ed Level </a>
										<ul>
											<li>
												<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
											</li>
											<li>
												<a href="#"><i class="fa fa-fw fa-file-text"></i> File</a>
											</li>
										</ul>
									</li>
								</ul>

							</li>
						</ul>
					</li>
					<li>
						<a href="calendar.html"><i class="fa fa-lg fa-fw fa-calendar"><em>3</em></i> <span class="menu-item-parent">Calendar</span></a>
					</li>
					<li>
						<a href="widgets.html"><i class="fa fa-lg fa-fw fa-list-alt"></i> <span class="menu-item-parent">Widgets</span></a>
					</li>
					<li>
						<a href="gallery.html"><i class="fa fa-lg fa-fw fa-picture-o"></i> <span class="menu-item-parent">Gallery</span></a>
					</li>
					<li>
						<a href="gmap-xml.html"><i class="fa fa-lg fa-fw fa-map-marker"></i> <span class="menu-item-parent">Google Map Skins</span><span class="badge bg-color-greenLight pull-right inbox-badge">9</span></a>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-windows"></i> <span class="menu-item-parent">Miscellaneous</span></a>
						<ul>
							<li>
								<a href="typography.html">Typography</a>
							</li>
							<li>
								<a href="pricing-table.html">Pricing Tables</a>
							</li>
							<li>
								<a href="invoice.html">Invoice</a>
							</li>
							<li>
								<a href="login.html" target="_top">Login</a>
							</li>
							<li>
								<a href="register.html" target="_top">Register</a>
							</li>
							<li>
								<a href="lock.html" target="_top">Locked Screen</a>
							</li>
							<li>
								<a href="error404.html">Error 404</a>
							</li>
							<li>
								<a href="error500.html">Error 500</a>
							</li>
							<li>
								<a href="blank_.html">Blank Page</a>
							</li>
							<li>
								<a href="email-template.html">Email Template</a>
							</li>
							<li>
								<a href="search.html">Search Page</a>
							</li>
							<li>
								<a href="ckeditor.html">CK Editor</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#"><i class="fa fa-lg fa-fw fa-file"></i> <span class="menu-item-parent">Other Pages</span></a>
						<ul>
							<li>
								<a href="forum.html">Forum Layout</a>
							</li>
							<li>
								<a href="profile.html">Profile</a>
							</li>
							<li>
								<a href="timeline.html">Timeline</a>
							</li>
						</ul>
					</li>
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
						Graphs
					</li>
					<li>
						Morris Charts
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

				<div class="row">
					<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
						<h1 class="page-title txt-color-blueDark"><i class="fa fa-bar-chart-o fa-fw "></i> Graph <span>>
							Morris Charts </span></h1>
					</div>
					<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
						<ul id="sparks" class="">
							<li class="sparks-info">
								<h5> My Income <span class="txt-color-blue">$47,171</span></h5>
								<div class="sparkline txt-color-blue hidden-mobile hidden-md hidden-sm">
									1300, 1877, 2500, 2577, 2000, 2100, 3000, 2700, 3631, 2471, 2700, 3631, 2471
								</div>
							</li>
							<li class="sparks-info">
								<h5> Site Traffic <span class="txt-color-purple"><i class="fa fa-arrow-circle-up" data-rel="bootstrap-tooltip" title="Increased"></i>&nbsp;45%</span></h5>
								<div class="sparkline txt-color-purple hidden-mobile hidden-md hidden-sm">
									110,150,300,130,400,240,220,310,220,300, 270, 210
								</div>
							</li>
							<li class="sparks-info">
								<h5> Site Orders <span class="txt-color-greenDark"><i class="fa fa-shopping-cart"></i>&nbsp;2447</span></h5>
								<div class="sparkline txt-color-greenDark hidden-mobile hidden-md hidden-sm">
									110,150,300,130,400,240,220,310,220,300, 270, 210
								</div>
							</li>
						</ul>
					</div>
				</div>

				<!-- widget grid -->
				<section id="widget-grid" class="">

					<!-- row -->
					<div class="row">

						<!-- NEW WIDGET START -->
						<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Sales Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->
										<input type="text">
									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="sales-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

						</article>
						<!-- WIDGET END -->

					</div>

					<!-- end row -->

					<!-- row -->

					<!-- row -->
					<div class="row">

						<!-- NEW WIDGET START -->
						<article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Area Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="area-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-3" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Normal Bar Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="normal-bar-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-5" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Year Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="year-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-7" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Time Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="time-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-9" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>No Grid Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="nogrid-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

						</article>
						<!-- WIDGET END -->

						<!-- NEW WIDGET START -->
						<article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-2" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Bar Graph </h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="bar-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-4" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Stacked Bar Graph </h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="stacked-bar-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-6" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Donut Graph</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="donut-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-8" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Line Graph A </h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="graph-B" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-10" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Line Graph B </h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="non-continu-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

						</article>
						<!-- WIDGET END -->

					</div>

					<!-- end row -->

					<!-- row -->
					<div class="row">

						<!-- NEW WIDGET START -->
						<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget" id="wid-id-11" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
									<h2>Line Graph C</h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">

										<div id="non-date-graph" class="chart no-padding"></div>

									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->

							</div>
							<!-- end widget -->

						</article>
						<!-- WIDGET END -->

					</div>

					<!-- end row -->

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

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="js/libs/jquery-2.0.2.min.js"><\/script>');
			}
		</script>

		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}
		</script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- SPARKLINES -->
		<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

		<!-- browser msie issue fix -->
		<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices -->
		<script src="js/plugin/fastclick/fastclick.js"></script>

		<!--[if IE 7]>

		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->

		<!-- Demo purpose only -->
		<script src="js/demo.js"></script>

		<!-- MAIN APP JS FILE -->
		<script src="js/app.js"></script>

		<!-- PAGE RELATED PLUGIN(S) -->

		<!-- Morris Chart Dependencies -->
		<script src="js/plugin/morris/raphael.2.1.0.min.js"></script>
		<script src="js/plugin/morris/morris.min.js"></script>

		<script type="text/javascript">
			// PAGE RELATED SCRIPTS

			/*
			 * Run all morris chart on this page
			 */
			$(document).ready(function() {
				
				// DO NOT REMOVE : GLOBAL FUNCTIONS!
				pageSetUp();

				if ($('#sales-graph').length) {

					Morris.Area({
						element : 'sales-graph',
						data : [{
							period : '2010 Q1',
							iphone : 2666,
							ipad : null,
							itouch : 2647
						}, {
							period : '2010 Q2',
							iphone : 2778,
							ipad : 2294,
							itouch : 2441
						}, {
							period : '2010 Q3',
							iphone : 4912,
							ipad : 1969,
							itouch : 2501
						}, {
							period : '2010 Q4',
							iphone : 3767,
							ipad : 3597,
							itouch : 5689
						}, {
							period : '2011 Q1',
							iphone : 6810,
							ipad : 1914,
							itouch : 2293
						}, {
							period : '2011 Q2',
							iphone : 5670,
							ipad : 4293,
							itouch : 1881
						}, {
							period : '2011 Q3',
							iphone : 4820,
							ipad : 3795,
							itouch : 1588
						}, {
							period : '2011 Q4',
							iphone : 15073,
							ipad : 5967,
							itouch : 5175
						}, {
							period : '2012 Q1',
							iphone : 10687,
							ipad : 4460,
							itouch : 2028
						}, {
							period : '2012 Q2',
							iphone : 8432,
							ipad : 5713,
							itouch : 1791
						}],
						xkey : 'period',
						ykeys : ['iphone', 'ipad', 'itouch'],
						labels : ['iPhone', 'iPad', 'iPod Touch'],
						pointSize : 2,
						hideHover : 'auto'
					});

				}

				// area graph
				if ($('#area-graph').length) {
					Morris.Area({
						element : 'area-graph',
						data : [{
							x : '2011 Q1',
							y : 3,
							z : 3
						}, {
							x : '2011 Q2',
							y : 2,
							z : 0
						}, {
							x : '2011 Q3',
							y : 0,
							z : 2
						}, {
							x : '2011 Q4',
							y : 4,
							z : 4
						}],
						xkey : 'x',
						ykeys : ['y', 'z'],
						labels : ['Y', 'Z']
					});
				}

				// bar graph color
				if ($('#bar-graph').length) {

					Morris.Bar({
						element : 'bar-graph',
						data : [{
							x : '2011 Q1',
							y : 0
						}, {
							x : '2011 Q2',
							y : 1
						}, {
							x : '2011 Q3',
							y : 2
						}, {
							x : '2011 Q4',
							y : 3
						}, {
							x : '2012 Q1',
							y : 4
						}, {
							x : '2012 Q2',
							y : 5
						}, {
							x : '2012 Q3',
							y : 6
						}, {
							x : '2012 Q4',
							y : 7
						}, {
							x : '2013 Q1',
							y : 8
						}],
						xkey : 'x',
						ykeys : ['y'],
						labels : ['Y'],
						barColors : function(row, series, type) {
							if (type === 'bar') {
								var red = Math.ceil(150 * row.y / this.ymax);
								return 'rgb(' + red + ',0,0)';
							} else {
								return '#000';
							}
						}
					});

				}

				// Use Morris.Bar
				if ($('#normal-bar-graph').length) {

					Morris.Bar({
						element : 'normal-bar-graph',
						data : [{
							x : '2011 Q1',
							y : 3,
							z : 2,
							a : 3
						}, {
							x : '2011 Q2',
							y : 2,
							z : null,
							a : 1
						}, {
							x : '2011 Q3',
							y : 0,
							z : 2,
							a : 4
						}, {
							x : '2011 Q4',
							y : 2,
							z : 4,
							a : 3
						}],
						xkey : 'x',
						ykeys : ['y', 'z', 'a'],
						labels : ['Y', 'Z', 'A']
					});

				}

				// Use Morris.Bar 2
				if ($('#noline-bar-graph').length) {
					Morris.Bar({
						element : 'noline-bar-graph',
						axes : false,
						data : [{
							x : '2011 Q1',
							y : 3,
							z : 2,
							a : 3
						}, {
							x : '2011 Q2',
							y : 2,
							z : null,
							a : 1
						}, {
							x : '2011 Q3',
							y : 0,
							z : 2,
							a : 4
						}, {
							x : '2011 Q4',
							y : 2,
							z : 4,
							a : 3
						}],
						xkey : 'x',
						ykeys : ['y', 'z', 'a'],
						labels : ['Y', 'Z', 'A']
					});
				}

				/* data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type */
				if ($('#year-graph').length) {
					var day_data = [{
						"period" : "2012-10-01",
						"licensed" : 3407,
						"sorned" : 660
					}, {
						"period" : "2012-09-30",
						"licensed" : 3351,
						"sorned" : 629
					}, {
						"period" : "2012-09-29",
						"licensed" : 3269,
						"sorned" : 618
					}, {
						"period" : "2012-09-20",
						"licensed" : 3246,
						"sorned" : 661
					}, {
						"period" : "2012-09-19",
						"licensed" : 3257,
						"sorned" : 667
					}, {
						"period" : "2012-09-18",
						"licensed" : 3248,
						"sorned" : 627
					}, {
						"period" : "2012-09-17",
						"licensed" : 3171,
						"sorned" : 660
					}, {
						"period" : "2012-09-16",
						"licensed" : 3171,
						"sorned" : 676
					}, {
						"period" : "2012-09-15",
						"licensed" : 3201,
						"sorned" : 656
					}, {
						"period" : "2012-09-10",
						"licensed" : 3215,
						"sorned" : 622
					}];
					Morris.Line({
						element : 'year-graph',
						data : day_data,
						xkey : 'period',
						ykeys : ['licensed', 'sorned'],
						labels : ['Licensed', 'SORN']
					})
				}

				// decimal data
				if ($('#decimal-graph').length) {
					var decimal_data = [];
					for (var x = 0; x <= 360; x += 10) {
						decimal_data.push({
							x : x,
							y : Math.sin(Math.PI * x / 180).toFixed(4)
						});
					}
					window.m = Morris.Line({
						element : 'decimal-graph',
						data : decimal_data,
						xkey : 'x',
						ykeys : ['y'],
						labels : ['sin(x)'],
						parseTime : false,
						hoverCallback : function(index, options) {
							var row = options.data[index];
							return "sin(" + row.x + ") = " + row.y;
						},
						xLabelMargin : 10
					});
				}

				// donut
				if ($('#donut-graph').length) {
					Morris.Donut({
						element : 'donut-graph',
						data : [{
							value : 70,
							label : 'foo'
						}, {
							value : 15,
							label : 'bar'
						}, {
							value : 10,
							label : 'baz'
						}, {
							value : 5,
							label : 'A really really long label'
						}],
						formatter : function(x) {
							return x + "%"
						}
					});
				}

				// time formatter
				if ($('#time-graph').length) {
					var week_data = [{
						"period" : "2011 W27",
						"licensed" : 3407,
						"sorned" : 660
					}, {
						"period" : "2011 W26",
						"licensed" : 3351,
						"sorned" : 629
					}, {
						"period" : "2011 W25",
						"licensed" : 3269,
						"sorned" : 618
					}, {
						"period" : "2011 W24",
						"licensed" : 3246,
						"sorned" : 661
					}, {
						"period" : "2011 W23",
						"licensed" : 3257,
						"sorned" : 667
					}, {
						"period" : "2011 W22",
						"licensed" : 3248,
						"sorned" : 627
					}, {
						"period" : "2011 W21",
						"licensed" : 3171,
						"sorned" : 660
					}, {
						"period" : "2011 W20",
						"licensed" : 3171,
						"sorned" : 676
					}, {
						"period" : "2011 W19",
						"licensed" : 3201,
						"sorned" : 656
					}, {
						"period" : "2011 W18",
						"licensed" : 3215,
						"sorned" : 622
					}, {
						"period" : "2011 W17",
						"licensed" : 3148,
						"sorned" : 632
					}, {
						"period" : "2011 W16",
						"licensed" : 3155,
						"sorned" : 681
					}, {
						"period" : "2011 W15",
						"licensed" : 3190,
						"sorned" : 667
					}, {
						"period" : "2011 W14",
						"licensed" : 3226,
						"sorned" : 620
					}, {
						"period" : "2011 W13",
						"licensed" : 3245,
						"sorned" : null
					}, {
						"period" : "2011 W12",
						"licensed" : 3289,
						"sorned" : null
					}, {
						"period" : "2011 W11",
						"licensed" : 3263,
						"sorned" : null
					}, {
						"period" : "2011 W10",
						"licensed" : 3189,
						"sorned" : null
					}, {
						"period" : "2011 W09",
						"licensed" : 3079,
						"sorned" : null
					}, {
						"period" : "2011 W08",
						"licensed" : 3085,
						"sorned" : null
					}, {
						"period" : "2011 W07",
						"licensed" : 3055,
						"sorned" : null
					}, {
						"period" : "2011 W06",
						"licensed" : 3063,
						"sorned" : null
					}, {
						"period" : "2011 W05",
						"licensed" : 2943,
						"sorned" : null
					}, {
						"period" : "2011 W04",
						"licensed" : 2806,
						"sorned" : null
					}, {
						"period" : "2011 W03",
						"licensed" : 2674,
						"sorned" : null
					}, {
						"period" : "2011 W02",
						"licensed" : 1702,
						"sorned" : null
					}, {
						"period" : "2011 W01",
						"licensed" : 1732,
						"sorned" : null
					}];
					Morris.Line({
						element : 'time-graph',
						data : week_data,
						xkey : 'period',
						ykeys : ['licensed', 'sorned'],
						labels : ['Licensed', 'SORN'],
						events : ['2011-04', '2011-08']
					});
				}

				// negative value
				if ($('#graph-B').length) {
					var neg_data = [{
						"period" : "2011-08-12",
						"a" : 100
					}, {
						"period" : "2011-03-03",
						"a" : 75
					}, {
						"period" : "2010-08-08",
						"a" : 50
					}, {
						"period" : "2010-05-10",
						"a" : 25
					}, {
						"period" : "2010-03-14",
						"a" : 0
					}, {
						"period" : "2010-01-10",
						"a" : -25
					}, {
						"period" : "2009-12-10",
						"a" : -50
					}, {
						"period" : "2009-10-07",
						"a" : -75
					}, {
						"period" : "2009-09-25",
						"a" : -100
					}];
					Morris.Line({
						element : 'graph-B',
						data : neg_data,
						xkey : 'period',
						ykeys : ['a'],
						labels : ['Series A'],
						units : '%'
					});
				}

				// no grid
				/* data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type */
				if ($('#nogrid-graph').length) {
					var day_data = [{
						"period" : "2012-10-01",
						"licensed" : 3407,
						"sorned" : 660
					}, {
						"period" : "2012-09-30",
						"licensed" : 3351,
						"sorned" : 629
					}, {
						"period" : "2012-09-29",
						"licensed" : 3269,
						"sorned" : 618
					}, {
						"period" : "2012-09-20",
						"licensed" : 3246,
						"sorned" : 661
					}, {
						"period" : "2012-09-19",
						"licensed" : 3257,
						"sorned" : 667
					}, {
						"period" : "2012-09-18",
						"licensed" : 3248,
						"sorned" : 627
					}, {
						"period" : "2012-09-17",
						"licensed" : 3171,
						"sorned" : 660
					}, {
						"period" : "2012-09-16",
						"licensed" : 3171,
						"sorned" : 676
					}, {
						"period" : "2012-09-15",
						"licensed" : 3201,
						"sorned" : 656
					}, {
						"period" : "2012-09-10",
						"licensed" : 3215,
						"sorned" : 622
					}];
					Morris.Line({
						element : 'nogrid-graph',
						grid : false,
						data : day_data,
						xkey : 'period',
						ykeys : ['licensed', 'sorned'],
						labels : ['Licensed', 'SORN']
					});
				}

				// non-continus data
				/* data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type */
				if ($('#non-continu-graph').length) {
					var day_data = [{
						"period" : "2012-10-01",
						"licensed" : 3407
					}, {
						"period" : "2012-09-30",
						"sorned" : 0
					}, {
						"period" : "2012-09-29",
						"sorned" : 618
					}, {
						"period" : "2012-09-20",
						"licensed" : 3246,
						"sorned" : 661
					}, {
						"period" : "2012-09-19",
						"licensed" : 3257,
						"sorned" : null
					}, {
						"period" : "2012-09-18",
						"licensed" : 3248,
						"other" : 1000
					}, {
						"period" : "2012-09-17",
						"sorned" : 0
					}, {
						"period" : "2012-09-16",
						"sorned" : 0
					}, {
						"period" : "2012-09-15",
						"licensed" : 3201,
						"sorned" : 656
					}, {
						"period" : "2012-09-10",
						"licensed" : 3215
					}];
					Morris.Line({
						element : 'non-continu-graph',
						data : day_data,
						xkey : 'period',
						ykeys : ['licensed', 'sorned', 'other'],
						labels : ['Licensed', 'SORN', 'Other'],
						/* custom label formatting with `xLabelFormat` */
						xLabelFormat : function(d) {
							return (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
						},
						/* setting `xLabels` is recommended when using xLabelFormat */
						xLabels : 'day'
					});
				}

				// non date data
				if ($('#non-date-graph').length) {
					var day_data = [{
						"elapsed" : "I",
						"value" : 34
					}, {
						"elapsed" : "II",
						"value" : 24
					}, {
						"elapsed" : "III",
						"value" : 3
					}, {
						"elapsed" : "IV",
						"value" : 12
					}, {
						"elapsed" : "V",
						"value" : 13
					}, {
						"elapsed" : "VI",
						"value" : 22
					}, {
						"elapsed" : "VII",
						"value" : 5
					}, {
						"elapsed" : "VIII",
						"value" : 26
					}, {
						"elapsed" : "IX",
						"value" : 12
					}, {
						"elapsed" : "X",
						"value" : 19
					}];
					Morris.Line({
						element : 'non-date-graph',
						data : day_data,
						xkey : 'elapsed',
						ykeys : ['value'],
						labels : ['value'],
						parseTime : false
					});
				}

				//stacked bar
				if ($('#stacked-bar-graph').length) {
					Morris.Bar({
						element : 'stacked-bar-graph',
						axes : false,
						grid : false,
						data : [{
							x : '2011 Q1',
							y : 3,
							z : 2,
							a : 3
						}, {
							x : '2011 Q2',
							y : 2,
							z : null,
							a : 1
						}, {
							x : '2011 Q3',
							y : 0,
							z : 2,
							a : 4
						}, {
							x : '2011 Q4',
							y : 2,
							z : 4,
							a : 3
						}],
						xkey : 'x',
						ykeys : ['y', 'z', 'a'],
						labels : ['Y', 'Z', 'A'],
						stacked : true
					});
				}

				// interval
				if ($('#interval-graph').length) {

					var nReloads = 0;
					function data(offset) {
						var ret = [];
						for (var x = 0; x <= 360; x += 10) {
							var v = (offset + x) % 360;
							ret.push({
								x : x,
								y : Math.sin(Math.PI * v / 180).toFixed(4),
								z : Math.cos(Math.PI * v / 180).toFixed(4)
							});
						}
						return ret;
					}

					var graph = Morris.Line({
						element : 'interval-graph',
						data : data(0),
						xkey : 'x',
						ykeys : ['y', 'z'],
						labels : ['sin()', 'cos()'],
						parseTime : false,
						ymin : -1.0,
						ymax : 1.0,
						hideHover : true
					});
					function update() {
						nReloads++;
						graph.setData(data(5 * nReloads));
						$('#reloadStatus').text(nReloads + ' reloads');
					}

					setInterval(update, 100);
				}

			});

			//setup_flots();
			/* end flot charts */

		</script>

		<!-- Your GOOGLE ANALYTICS CODE Below -->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();

		</script>

	</body>

</html>