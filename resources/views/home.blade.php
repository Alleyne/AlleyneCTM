@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Bienvenida')

@section('content')

	<div id="main" role="main">
		<!-- MAIN CONTENT -->
		<form class="lockscreen animated flipInY" action="index.html">
			<div class="logo">
				<!-- <h1 class="semi-bold"><img src="{{asset('assets/admin/img/logo-o.png')}}" alt="" /> Sityweb</h1> -->
				<!-- <h1 class="semi-bold"><img src="{{asset('assets/admin/img/logo-o.png')}}" alt="" /> Sityweb</h1> -->
			</div>
			<div>
				<img src="{{asset(Auth::user()->imagen)}}" alt="" width="110" height="110" />
				<div>
					<h2><i class="fa fa-user fa-2x text-muted air air-top-right hidden-mobile"></i>{{ Auth::user()->nombre_completo }} <small><i class="fa fa-unlock-o text-muted"></i> &nbsp;AUTORIZADO</small></h2>
					<p class="text-muted">
						TENGA LA MAS COORDIAL BIENVENIDA A CTMASTER.NET
					</p>

					<p class="no-margin margin-top-5">
						Property management system without pain!
					</p>
				</div>
			</div>
			<p class="font-xs margin-top-5">
				Copyright ctmaster 2015-2020.
			</p>
		</form>
	</div>
@stop