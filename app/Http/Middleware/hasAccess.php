<?php namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Redirect;
use App\Permission;
use Cache;
use Session;
use App\User;

class hasAccess {
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if(Auth::check()) {
      // Get the current route.
      $route = $request->route();

      // Get the current route actions.
      $currentAction = $route->getAction();
      $currentAction = $currentAction['controller'];
      $currentAction = collect(explode(chr(92), $currentAction));
      $currentAction = $currentAction->last();
      //dd($currentAction);
      
      // encuentra todos los roles del usuario logueado
      $roles= User::find(Auth::user()->id)->roles;
      
      // si el usuario no tiene ningun role
      if(empty($roles)) {
        if ($request->ajax()) {
            return response('Unauthorized.', 401);
        } else {
            Session::flash('warning', 'Usted no tiene ningun Role, favor contacte al administrador del sistema!');
            return redirect()->to('/login');
        } 
      }

      // verifica si el usuario tiene role de Admin
      foreach ($roles as $role) {
       if ($role->name === 'Admin') {
            return $next($request);   
        } 

        // si el usuario tiene algun role que no sea Admin
        // encuentra todos los permisos del role al cual pertenece el usuario logueado
        $permisos = $role->permissions;
        //dd($permisos->toArray());
        
        foreach ($permisos as $permiso) {
          //dd($permiso->toArray(), $currentAction, $permiso->value);
          if ($permiso->value === $currentAction) {
              return $next($request);   
          } 
        }                        
        
        // usuario tiene role pero no tiene permiso para ejecutar el metodo del controlador
        if ($request->ajax()) {
          return response('Unauthorized.', 401);
        } else {
          Session::flash('danger', '--'. $currentAction . '-- Usted no tiene permiso para accesar esta pagina!');
          return back();
        }            
      }            
                 
    } else {
      Session::flash('warning', 'Su sesión ha caducado o usted no tiene permiso para accesar esta pagina!, favor autenticarse!');
      return redirect()->to('/login'); 
    }
  }  

} // end of class