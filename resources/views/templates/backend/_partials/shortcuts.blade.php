    <!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
    Note: These tiles are completely responsive,
    you can add as many as you like
    -->
    <div id="shortcut">
      <ul>
				@if (Cache::get('esAdminkey'))        
	        <li>
	          <a href="{{ URL::route('jds.show', 1) }}" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Junta Directiva </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('indexblqplus') }}" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Bloques </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('serviproductos.index') }}" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Serviproductos <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('orgs.index') }}" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-truck fa-4x"></i> <span>Proveedores</span> </span> </a>
	        </li>
	        <li>
	          <a href="#" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Facturas x pagar <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('bitacoras.index') }}" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Bitacora</span> </span> </a>
	        </li>
				
				@elseif (Cache::get('esJuntaDirectivakey'))
	        <li>
	          <a href="{{ URL::route('jds.show', 1) }}" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Junta Directiva </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('indexblqplus') }}" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Bloques </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('serviproductos.index') }}" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Serviproductos <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('orgs.index') }}" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-truck fa-4x"></i> <span>Proveedores</span> </span> </a>
	        </li>
	        <li>
	          <a href="#" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Facturas x pagar <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('bitacoras.index') }}" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Bitacora</span> </span> </a>
	        </li>
     		
     		@elseif (Cache::get('esAdministradorkey')) 
	        <li>
	          <a href="{{ URL::route('jds.show', 1) }}" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Junta Directiva </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('indexblqplus') }}" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Bloques </span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('serviproductos.index') }}" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Serviproductos <span class="label pull-right bg-color-darken">14</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('orgs.index') }}" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i class="fa fa-truck fa-4x"></i> <span>Proveedores</span> </span> </a>
	        </li>
	        <li>
	          <a href="#" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i class="fa fa-calendar fa-4x"></i> <span>Facturas x pagar <span class="label pull-right bg-color-darken">99</span></span> </span> </a>
	        </li>
	        <li>
	          <a href="{{ URL::route('bitacoras.index') }}" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i class="fa fa-book fa-4x"></i> <span>Bitacora</span> </span> </a>
	        </li>
				
				@elseif (Cache::get('esContadorkey'))
	        <li>
	          <a href="{{ URL::route('indexblqplus') }}" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i class="fa fa-building fa-4x"></i> <span>Bloques </span> </span> </a>
	        </li>
				@endif   
      </ul>
    </div>
    <!-- END SHORTCUT AREA -->