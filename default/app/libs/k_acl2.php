<?php

/**
 * Funciona perfecto! Tomado de http://wiki.kumbiaphp.com/ACL_Configurado_a_Traves_de_un_Archivo_ini
 * 
 * Código para adminController.php
 * 
 * //instanciamos a la clase KAcl2, y le indicamos el ini a utlizar
 * $acl = new KAcl2('privilegios');  //si no se especifica el archivo a usar, por defecto busca en privilegios.ini
 * $modulo = $this->module_name; //obtenermos el modulo actual
 * $controlador = $this->controller_name; //obtenemos el controlador actual
 * $accion = $this->action_name; //y obtenemos la accion actual
 *
 * $privilegio = Session::get('perfil_id');
 * if (!$acl->check($privilegio, $modulo, $controlador, $accion)) { //verificamos si tiene ó no privilegios
 *     // si no posee los privilegios necesarios le enviamos un mensaje indicandoselo
 *     Flash::error("No posees suficientes PRIVILEGIOS para acceder a: {$modulo}/{$controlador}/{$accion}");
 *     //no dejamos que entre al contenido de la url si no tiene permisos
 *     Flash::error('No tiene permisos para ingresar a <strong>' . Router::get('route'). '</strong>');
 *     View::select(NULL);
 *     return false;
 * }
 *                
 * 
 */
 
class KAcl2 {
 
    /**
     *
     * @var SimpleAcl
     */
    protected $_acl = null;
 
    /**
     * arreglo con los templates para cada usuario
     *
     * @var array 
     */
    protected $_templates = array();
 
    public function __construct($archivo = 'privilegios') {
        $config = Config::read($archivo, true);
 
        $this->_acl = Acl2::factory();
 
        $this->_establecerRoles($config['roles']);
 
        foreach ($config['roles'] as $rol => $padres) {
            $recursos = $config[$rol];
            $this->_establecerPermisos($rol, $recursos);
        }
 
        $this->_establecerTemplates($config['templates']);
    }
 
    protected function _establecerRoles($roles) {
 
        foreach ($roles as $rol => $padres) {
            $this->_acl->user($rol, array($rol));
            if ($padres) {
                $padres = explode(',', $padres);
                $padres = is_array($padres) ? $padres : array($padres);
                $this->_acl->parents($rol, $padres);
            }
        }
    }
 
    protected function _establecerPermisos($rol, $recursos) {
        $urls = array();
        foreach ($recursos as $recurso => $acciones) {
            if ($acciones == '*') {
                $urls[] = "$recurso/*";
            } else {
                foreach (explode(',', $acciones) as $accion) {
                    $urls[] = "$recurso/$accion";
                }
            }
            $this->_acl->allow($rol, $urls);
        }
 
        if (empty($recursos)) {
            $urls[] = "*";
            $this->_acl->allow($rol, $urls);
        }
    }
 
    public function check($usuario, $modulo, $controlador, $accion) {
        if ( isset ($this->_templates["$usuario"]) ){
            View::template("{$this->_templates["$usuario"]}");
        }
        if ($modulo) {
            $recurso1 = "$modulo/$controlador/$accion";
            $recurso2 = "$modulo/$controlador/*";  //por si tiene acceso a todas las acciones
            $recurso3 = "*";  //por si tiene acceso a todo el sistema
        } else {
            $recurso1 = "$controlador/$accion";
            $recurso2 = "$controlador/*"; //por si tiene acceso a todas las acciones
            $recurso3 = "*";  //por si tiene acceso a todo el sistema
        }
        return $this->_acl->check($recurso1, $usuario) ||
        $this->_acl->check($recurso2, $usuario) ||
        $this->_acl->check($recurso3, $usuario);
    }
 
    protected function _establecerTemplates($templates){
        foreach ($templates as $rol => $template){
            $this->_templates["$rol"] = $template ;
        }
    }
 
}