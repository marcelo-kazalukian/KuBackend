<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador para proteger los controladores que heredan
 * Para empezar a crear una convención de seguridad y módulos
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */

 Load::model('admin/perfil'); 

class AdminController extends Controller
{

    final protected function initialize()
    {
        // Verifica que exista una identidad válida para la session actual. 
        // Si existe identidad válida continua, sino redirige a pantalla de login.
        if (!KAuth::isLogged()) {
            return Redirect::to('login/entrar');
        }    

        // Cargo los permisos y templates
        $acl = new KAcl(); 
        $modulo = $this->module_name;
        $controlador = $this->controller_name;
        $accion = $this->action_name;
        if (!$acl->check(Session::get('perfil_id'), $modulo, $controlador, $accion)) {
            $viewInicio = (new Perfil)->getViewInicial();
            Flash::error('No tiene permisos para ingresar a <strong>' . Router::get('route') . '</strong> <br>' . 
            "<a href='/$viewInicio'> inicio </a>");
            View::select(NULL, NULL);
            return false;
        }

    }

    final protected function finalize()
    {
        
    }

}
