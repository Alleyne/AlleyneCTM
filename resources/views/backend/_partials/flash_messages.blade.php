@if (Session::has('success'))
	<div class="alert alert-success" role="alert">
	  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
	  {{ Session::get('success') }}
	</div>

@elseif (Session::has('info'))
	<div class="alert alert-info" role="alert">
	  <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
	  {{ Session::get('info') }}
	</div>

@elseif (Session::has('warning'))	
	<div class="alert alert-warning" role="alert">
	  <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
	  {{ Session::get('warning') }}
	</div>

@elseif (Session::has('danger'))	
	<div class="alert alert-danger" role="alert">
	  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
	  {{ Session::get('warning') }}
	</div>
@endif

