
<li>
	<a href="{{ URL::route('frontend') }}" title="FrontEnd"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Sitio Web</span></a>
</li>

<!-- Escoje la navegación de acuerdo al grupo al que pertenece el usuario -->
@if (Cache::get('esAdminkey'))
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">Juntas Directivas</span></a>
		<ul>
			<li>
			<a href="{{ URL::route('jds.index') }}" ><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Juntas</span></a>  
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
		</ul>
	</li>		
	
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">PH Bloques</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexblqplus') }}"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Bloques</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Blqadmins</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Ingresos</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexunall') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Por Unidades</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Ctas cobrar empleados</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Otros</span></a>
			</li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Egreso Caja General</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('facturas.index') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Registrar facturas</span></a>
			</li>
			<li>
				<a href="{{ URL::route('pagarfacturas') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Pagar facturas</span></a>
			</li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Contabilidad</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('pcontables.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
			<li>
				<a href="{{ URL::route('cajachicas.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Admin Caja Chica</span></a>
			</li>
			<li>
				<a href="{{ URL::route('pagosnoids.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Pagos no identificados</span></a>
			</li>
			<li>
				<a href="{{ URL::route('catalogos.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Catalogo</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Dashboard</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('vigente') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Vigente</span></a>
			</li>
			<li>
				<a href="{{ URL::route('historico') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Historico</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="{{ URL::route('serviproductos.index') }}"><i class="fa fa-lg fa-fw fa-book"></i> <span class="menu-item-parent">Serviproductos</span></a>
	</li>
	<li>
		<a href="{{ URL::route('orgs.index') }}"><i class="fa fa-lg fa-fw fa-truck"></i> <span class="menu-item-parent">Proveedores</span></a>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Noticias</span></a>
		<ul>
			<li>
				<a href="{{ route('posts.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Noticias</span></a>
			</li>
			<li>
				<a href="{{ route('categories.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Categorias</span></a>
			</li>
			<li>
				<a href="{{ route('tags.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Etiquetas</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Autorizacion</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('users.index') }}"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Usuarios</span></a>
			</li>
			<li>
				<a href="{{ URL::route('permissions.index') }}"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Permisos</span></a>
			</li>
			<li>
				<a href="{{ URL::route('roles.index') }}"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Roles</span></a>
			</li>
		</ul>
	</li>	
	<li>
		<a href="{{ URL::route('ecajachicas.index') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Egreso Caja Chica</span></a>
	</li>
	<li>
		<a href="{{ URL::route('bitacoras.index') }}"><i class="fa fa-archive"></i> <span class="menu-item-parent">Bitácora</span></a>
	</li>

@elseif (Cache::get('esJuntaDirectivakey'))
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">Juntas Directivas</span></a>
		<ul>
			<li>
			<a href="{{ URL::route('jds.index') }}" ><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Juntas</span></a>  
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
		</ul>
	</li>		
	
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">PH Bloques</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexblqplus') }}"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Bloques</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Blqadmins</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Contabilidad</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('pcontables.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
			<li>
				<a href="{{ URL::route('cajachicas.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Admin Caja Chica</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Dashboard</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('vigente') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Vigente</span></a>
			</li>
			<li>
				<a href="{{ URL::route('historico') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Historico</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Autorizacion</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('users.index') }}"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Usuarios</span></a>
			</li>
			<li>
				<a href="{{ URL::route('roles.index') }}"><i class="fa fa-lg fa-fw fa-key"></i> <span class="menu-item-parent">Roles</span></a>
			</li>
		</ul>
	</li>	
	<li>
		<a href="{{ URL::route('bitacoras.index') }}"><i class="fa fa-archive"></i> <span class="menu-item-parent">Bitácora</span></a>
	</li>

@elseif (Cache::get('esAdministradorkey'))
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">PH Bloques</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexblqplus') }}"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Bloques</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Blqadmins</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Ingresos</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexunall') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Por Unidades</span></a>
			</li>
			<li>
				<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Otros</span></a>
			</li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Egreso Caja General</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('facturas.index') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Registrar facturas</span></a>
			</li>
			<li>
				<a href="{{ URL::route('pagarfacturas') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Pagar facturas</span></a>
			</li>
		</ul>
	</li>

	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Contabilidad</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('pcontables.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
			<li>
				<a href="{{ URL::route('pagosnoids.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Pagos no identificados</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Dashboard</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('vigente') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Vigente</span></a>
			</li>
			<li>
				<a href="{{ URL::route('historico') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Historico</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="{{ URL::route('serviproductos.index') }}"><i class="fa fa-lg fa-fw fa-book"></i> <span class="menu-item-parent">Serviproductos</span></a>
	</li>
	<li>
		<a href="{{ URL::route('orgs.index') }}"><i class="fa fa-lg fa-fw fa-truck"></i> <span class="menu-item-parent">Proveedores</span></a>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Noticias</span></a>
		<ul>
			<li>
				<a href="{{ route('posts.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Noticias</span></a>
			</li>
			<li>
				<a href="{{ route('categories.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Categorias</span></a>
			</li>
			<li>
				<a href="{{ route('tags.index') }}"><i class="fa fa-lg fa-fw fa-desktop"></i> <span class="menu-item-parent">Etiquetas</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="{{ URL::route('ecajachicas.index') }}"><i class="fa fa-lg fa-fw fa-money"></i> <span class="menu-item-parent">Egreso Caja Chica</span></a>
	</li>
	<li>
		<a href="{{ URL::route('bitacoras.index') }}"><i class="fa fa-archive"></i> <span class="menu-item-parent">Bitácora</span></a>
	</li>

@elseif (Cache::get('esContadorkey'))
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-building"></i> <span class="menu-item-parent">PH Bloques</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('indexblqplus') }}"><i class="fa fa-lg fa-fw fa-table"></i> <span class="menu-item-parent">Ver Bloques</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Contabilidad</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('pcontables.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Periodos</span></a>
			</li>
			<li>
				<a href="{{ URL::route('cajachicas.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Admin Caja Chica</span></a>
			</li>
			<li>
				<a href="{{ URL::route('pagosnoids.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Pagos no identificados</span></a>
			</li>
			<li>
				<a href="{{ URL::route('catalogos.index') }}"><i class="fa fa-lg fa-fw fa-eye"></i> <span class="menu-item-parent">Catalogo</span></a>
			</li>
		</ul>
	</li>
	<li>
		<a href="#"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Dashboard</span></a>
		<ul>
			<li>
				<a href="{{ URL::route('vigente') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Vigente</span></a>
			</li>
			<li>
				<a href="{{ URL::route('historico') }}"><i class="fa fa-lg fa-fw fa-bar-chart-o"></i> <span class="menu-item-parent">Historico</span></a>
			</li>
		</ul>
	</li>
@endif

<li>
	<a href="{{ url('/logout') }}"
    	onclick="event.preventDefault();
        document.getElementById('logout-form').submit();"><i class="fa fa-lg fa-fw fa-sign-out"></i>
        <span class="menu-item-parent">Logout</span>
    </a>
</li>

<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>