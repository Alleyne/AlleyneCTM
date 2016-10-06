<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
//=========================================================//
// RUTAS QUE CONTROLAN FRONTEND
//=========================================================//
Route::group(['middleware' => 'web'], function () {
	Route::get('/', 'WelcomeController@index')->name('frontend');
});

//=========================================================//
// RUTAS QUE CONTROLAN EL BACKEND
//=========================================================//
Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::get('/home', 'HomeController@index');

	Route::group(['namespace' => 'backend'], function()
	{
		//---------------------------------------------------------//
		// Funciones del controlador JdsController
		//---------------------------------------------------------// 	
		Route::resource('jds', 'JdsController');
	    
		//---------------------------------------------------------//
		// Funciones del controlador BloquesController
		//---------------------------------------------------------// 	
	    //rutas adicionales al resource controller
	    Route::get('bloques/indexblqplus', 'BloquesController@indexblqplus')->name('indexblqplus');
	    Route::get('bloques/showblqplus/{bloque_id}', 'BloquesController@showblqplus')->name('showblqplus');
	    Route::get('bloques/createblq/{jd_id}', 'BloquesController@createblq')->name('createblq');
	  	Route::post('subirImagenBloque/{id}', 'BloquesController@subirImagenBloque')->name('subirImagenBloque');
		Route::resource('bloques', 'BloquesController');

		//---------------------------------------------------------//
		// Funciones del controlador SeccionesController
		//---------------------------------------------------------// 	
	    //rutas adicionales al resource controller
	    Route::get('secciones/indexsecplus/{bloque_id}', 'SeccionesController@indexsecplus')->name('indexsecplus');
	    Route::get('secciones/showsecplus/{seccione_id}', 'SeccionesController@showsecplus')->name('showsecplus');
	    Route::get('secciones/createsec/{bloque_id}, {tipo}', 'SeccionesController@createsec')->name('createsec');
	  	Route::post('subirImagenSeccion/{id}', 'SeccionesController@subirImagenSeccion')->name('subirImagenSeccion');
		Route::resource('secciones', 'SeccionesController');
		
		//---------------------------------------------------------//
		// Funciones del controlador UnsController
		//---------------------------------------------------------// 	
	    //rutas adicionales al resource controller
	    Route::get('uns/indexunall', 'UnsController@indexunall')->name('indexunall');
	    Route::get('uns/indexunplus/{seccion_id}', 'UnsController@indexunplus')->name('indexunplus');
	    Route::get('uns/showunplus/{seccione_id}', 'UnsController@showunplus')->name('showunplus');
	    Route::get('uns/createun/{seccione_id}', 'UnsController@createun')->name('createun');
	    Route::get('uns/createungrupo/{seccione_id}', 'UnsController@createungrupo')->name('createungrupo');
	    Route::post('uns/storeungrupo', 'UnsController@storeungrupo')->name('storeungrupo');
		Route::resource('uns', 'UnsController');
		
		//---------------------------------------------------------//
		// Funciones del controlador BlqadminsController
		//---------------------------------------------------------// 	
	    Route::get('blqadmins/indexblqadmin/{bloque_id}', 'BlqadminsController@indexblqadmin')->name('indexblqadmin');
	    Route::get('blqadmins/desvincularblqdmin/{id}', 'BlqadminsController@desvincularblqdmin')->name('desvincularblqdmin');
		Route::resource('blqadmins', 'BlqadminsController');
		
		//---------------------------------------------------------//
		// Funciones del controlador PropsController
		//---------------------------------------------------------// 	
	    Route::get('props/indexprops/{un_id},{seccione_id}', 'PropsController@indexprops')->name('indexprops');
	    Route::get('props/createprop/{un_id},{seccione_id}', 'PropsController@createprop')->name('createprop');
	    Route::get('desvincularprop/{user_id},{un_id}', 'PropsController@desvincularprop')->name('desvincularprop');
		Route::resource('props', 'PropsController');
		
		//---------------------------------------------------------//
		// Funciones del controlador OrgsController
		//---------------------------------------------------------// 	
	    Route::get('orgs/desvincularSubcuenta{org_id}, {ksubcuenta_id}', 'OrgsController@desvincularSubcuenta')->name('desvincularSubcuenta');
	    Route::get('orgs/catalogosPorOrg/{org_id}', 'OrgsController@catalogosPorOrg')->name('catalogosPorOrg');
		Route::resource('orgs', 'OrgsController');

		//---------------------------------------------------------//
		// Funciones del controlador UsersController
		//---------------------------------------------------------// 	
	  	Route::post('subirImagenUser/{user_id}', 'BloquesController@subirImagenUser')->name('subirImagenUser');
		Route::resource('users', 'UsersController');

		//---------------------------------------------------------//
		// Funciones del controlador PhsController
		//---------------------------------------------------------// 	
		Route::post('subirImagenPh/{id}', 'PhsController@subirImagenPh')->name('subirImagenPh');
		Route::resource('phs', 'PhsController');

		//---------------------------------------------------------//
		// Funciones del controlador PermissionsController
		//---------------------------------------------------------// 	
		Route::resource('permissions', 'PermissionsController');
		
		//---------------------------------------------------------//
		// Funciones del controlador RolesController
		//---------------------------------------------------------// 	
	    Route::get('roles/desvincularpermis{role_id}, {permis_id}', 'RolesController@desvincularpermis')->name('desvincularpermis');
	    Route::get('roles/permisPorRole{role_id}', 'RolesController@permisPorRole')->name('permisPorRole');
		Route::resource('roles', 'RolesController');
		
		//---------------------------------------------------------//
		// Funciones del controlador BitacorasController
		//---------------------------------------------------------// 	
		Route::resource('bitacoras', 'BitacorasController');
	});	
	
	Route::group(['namespace' => 'contabilidad'], function()
	{
		//---------------------------------------------------------//
		// Funciones del controlador PagosController
		//---------------------------------------------------------// 		
	    Route::get('procesaChequeRecibido/{pago_id}', 'PagosController@procesaChequeRecibido')->name('procesaChequeRecibido');
	    Route::get('indexPagos/{un_id}', 'PagosController@indexPagos')->name('indexPagos');
	    Route::get('createPago/{un_id}', 'PagosController@createPago')->name('createPago');
	    Route::get('showRecibo/{pago_id}', 'PagosController@showRecibo')->name('showRecibo');
	    Route::get('procesaAnulacionPago/{pago_id}, {un_id}', 'PagosController@procesaAnulacionPago')->name('procesaAnulacionPago');
	    Route::get('eliminaPagoCheque/{pago_id}', 'PagosController@eliminaPagoCheque')->name('eliminaPagoCheque');
		Route::resource('pagos', 'PagosController');
			
		//---------------------------------------------------------//
		// Funciones del controlador FacturasController
		//---------------------------------------------------------// 		
	    Route::get('contabilizaDetallesFactura/{factura_id}', 'FacturasController@contabilizaDetallesFactura')->name('contabilizaDetallesFactura');
	    Route::get('pagarfacturas', 'FacturasController@pagarfacturas')->name('pagarfacturas');
		Route::resource('facturas', 'FacturasController');
		
		//---------------------------------------------------------//
		// Funciones del controlador DetallefacturasController
		//---------------------------------------------------------// 		
	    //Route::get('createDetalleFactura/{factura_id}', 'ContabilidadController@createDetalleFactura')->name('createDetalleFactura');
		Route::resource('detallefacturas', 'DetallefacturasController');

		//---------------------------------------------------------//
		// Funciones del controlador DetallepagofacturasController
		//---------------------------------------------------------// 		
		Route::get('contabilizaDetallePagoFactura/{detallepagofactura_id}', 'DetallepagofacturasController@contabilizaDetallePagoFactura')->name('contabilizaDetallePagoFactura');
		Route::resource('detallepagofacturas', 'DetallepagofacturasController');

		//---------------------------------------------------------//
		// Funciones del controlador CtdasmsController
		//---------------------------------------------------------// 	
	    Route::get('ecuentas/{un_id}, {tipo}', 'CtdasmsController@ecuentas')->name('ecuentas');
		Route::resource('ctdasms', 'CtdasmsController');		

		//---------------------------------------------------------//
		// Funciones del controlador PcontablesController
		//---------------------------------------------------------// 	
	    Route::get('crearPeriodoInicial/{todate}', 'PcontablesController@crearPeriodoInicial')->name('crearPeriodoInicial');
	    Route::get('cerrarPeriodoContable/{pcontable_id}', 'PcontablesController@cerrarPeriodoContable')->name('cerrarPeriodoContable');
		Route::resource('pcontables', 'PcontablesController');

		//---------------------------------------------------------//
		// Funciones del controlador Ctdiarios
		//---------------------------------------------------------// 	
	    Route::get('diarioFinal/{pcontable_id}', 'CtdiariosController@diarioFinal')->name('diarioFinal');
		Route::resource('ctdiarios', 'CtdiariosController');

		//---------------------------------------------------------//
		// Funciones del controlador HojadetrabajosController
		//---------------------------------------------------------// 	
	    Route::get('estadoderesultado/{pcontable_id}', 'HojadetrabajosController@estadoderesultado')->name('estadoderesultado');
	    Route::get('er/{pcontable_id}', 'HojadetrabajosController@er')->name('er');
	    Route::get('bg/{pcontable_id}', 'HojadetrabajosController@bg')->name('bg');
	    Route::get('balancegeneral/{pcontable_id},{periodo}', 'HojadetrabajosController@balancegeneral')->name('balancegeneral');
	    Route::get('hojadetrabajo/{periodo}', 'HojadetrabajosController@hojadetrabajo')->name('hojadetrabajo');
	    Route::get('verMayorAux/{periodo}, {cuenta}', 'HojadetrabajosController@verMayorAux')->name('verMayorAux');
	    Route::get('cierraPeriodo/{pcontable_id},{periodo},{fecha}', 'HojadetrabajosController@cierraPeriodo')->name('cierraPeriodo');
		Route::resource('hojadetrabajos', 'HojadetrabajosController');
	
		//---------------------------------------------------------//
		// Funciones del controlador AjustesController
		//---------------------------------------------------------// 	
	    //Route::get('anularAjuste/{id}, {codigo}', 'AjustesController@anularAjuste')->name('anularAjuste');
	    Route::get('verAjustes/{id}, {periodo}, {cuenta}, {codigo}', 'AjustesController@verAjustes')->name('verAjustes');
	    Route::get('createAjustes/{periodo}', 'AjustesController@createAjustes')->name('createAjustes');
		Route::resource('ajustes', 'AjustesController');
		
		//---------------------------------------------------------//
		// Funciones del controlador InicializarController
		//---------------------------------------------------------// 		
	    Route::get('inicializaUn/{un_id}', 'InicializaunController@inicializaUn')->name('inicializaUn');
	    Route::post('storeInicializacion', 'InicializaunController@storeInicializacion')->name('storeInicializacion');
	});	

	Route::group(['namespace' => 'catalogo'], function()
	{
		//---------------------------------------------------------//
		// Funciones del controlador CatalogosController
		//---------------------------------------------------------// 	
	    Route::get('createCuenta/{id}', 'CatalogosController@createCuenta')->name('createCuenta');
		Route::resource('catalogos', 'CatalogosController');
	});


	//---------------------------------------------------------//
	// Funciones del controlador EmailsController
	//---------------------------------------------------------// 	
	//Route::get('/email', function() {
	    //return view('emails.create');
	//});	    

	Route::group(['namespace' => 'emails'], function()
	{
		Route::get('/email', 'EmailsController@emailNuevoEcuentas');		
	});

	//---------------------------------------------------------//
	// Informes financieros
	//---------------------------------------------------------//
	Route::get('/balance_general', array('as' => 'balance_general', function() {
	    return view('finanzas.bg');
	}));

	Route::get('/estado_resultado', array('as' => 'estado_resultado', function() {
	    return view('finanzas.er');
	}));
});


use Carbon\Carbon;
use App\library\Sity;
use App\Ctdasm;
use App\Detallepago;
use App\Pago;
use App\Ctdiario;
use App\Pcontable;
use App\Secapto;
use App\Un;

//***********************************************************
// SECCION PARA LA CREACION DE ESCENARIOS CONTABLES
//***********************************************************

//---------------------------------------------------------//
// Crea un escenario de prueba de enero y febrero
//---------------------------------------------------------//
Route::get('/aaa', function () {
	Un::where('inicializada', 1)
      ->update(['inicializada' => 0]);	
	Sity::limpiar();
	//Sity::periodo('2016-07-01');
	//Sity::facturar('2016-07-01');
	//Sity::periodo('2016-08-01');
	//Sity::facturar('2016-08-01');
	//Sity::penalizar('2016-08-04');
	//Sity::periodo('2016-09-01');
	//Sity::facturar('2016-09-01');
	//Sity::penalizar('2016-09-04');	
	return 'Escenario limpiado ...';
});

Route::get('/bbb', function () {
	Un::where('inicializada', 1)
      ->update(['inicializada' => 0]);
	return 'Se inicializa todas las unidades a cero ...';
});

//---------------------------------------------------------//
// Ruta para hacer pruebas
//---------------------------------------------------------//
//use DB;
Route::get('/truncate-all', function () {
	DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
	DB::table('props')->truncate();
	DB::table('uns')->truncate();
	DB::table('secciones')->truncate();
	DB::table('secaptos')->truncate();
  //DB::table('phs')->truncate();
	DB::table('bloques')->truncate();
	DB::table('blqadmins')->truncate();
	DB::table('jds')->truncate();
	
	DB::table('pcontables')->truncate();
	DB::table('facturas')->truncate();
	DB::table('detallefacturas')->truncate();	
	DB::table('ctdasms')->truncate();	
	DB::table('detallepagos')->truncate();
	DB::table('pagos')->truncate();
	DB::table('ctmayores')->truncate();
	DB::table('bitacoras')->truncate();		
	DB::table('ctdiarios')->truncate();

	DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	return 'tablas limpias';
});