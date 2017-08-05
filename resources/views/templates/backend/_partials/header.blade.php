
<div id="logo-group">

	<!-- PLACE YOUR LOGO HERE -->
	<span id="logo"><a href="#" id="show-shortcut"><img src="{{asset('assets/backend/img/logo.png')}}" alt="ctmaster logo"></a> </span>
	

	<!-- END LOGO PLACEHOLDER -->

	<!-- Note: The activity badge color changes when clicked and resets the number to 0
	Suggestion: You may want to set a flag when this happens to tick off all checked messages / notifications -->
	<span id="activity" class="activity-dropdown"> <i class="fa fa-user"></i> <b class="badge"> 29 </b> </span>

</div>

<!-- projects dropdown -->
<div id="project-context">

	<span class="label"></span>
	<span id="project-selector" class="popover-trigger-element dropdown-toggle" data-toggle="dropdown">Pendientes <i class="fa fa-angle-down"></i></span>

	<!-- Suggestion: populate this list with fetch and push technique -->
	<ul class="dropdown-menu">
		<li>
			<a href="javascript:void(0);">Pagos pendientes a proveedores <b class="badge"> 21 </b></a>
		</li>
		<li>
			<a href="javascript:void(0);">Informes de Caja general pendientes oor aprobar <b class="badge"> 8 </b></a>
		</li>
		<li class="divider"></li>
		<li>
			<a href="javascript:void(0);"><i class="fa fa-power-off"></i> Limpiar</a>
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