<?php
/**
 * Tomado de 
 * http://wiki.kumbiaphp.com/ACL_Configurado_a_Traves_de_un_Archivo_ini
 * 
 * Adaptado para que toma la info desde PHP en lugar de un archivo .ini
 * El próximo paso sería pasarlo a una BD para una gestión dinámica.
 */
class KAcl {
 
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
    protected $_templates = [];
 
    public function __construct() {        
 
        $this->_acl = Acl2::factory();
 
        $perfiles = (new Perfil)->getPerfilesYPadres();

        $this->_establecerRoles($perfiles);
 
        foreach ($perfiles as $perfil => $padres) {            
            $this->_establecerPermisos($perfil, (new Perfil)->getRecursos($perfil));
        }
 
        $this->_establecerTemplates((new Perfil)->getTemplates());
    }
 
    protected function _establecerRoles($perfiles) {
 
        foreach ($perfiles as $perfil => $padres) {
            $this->_acl->user($perfil, array($perfil));
            if ($padres) {                
                $this->_acl->parents($perfil, $padres);
            }
        }
    }
 
    protected function _establecerPermisos($perfil, $recursos) {
        $urls = array();
        
        foreach ($recursos as $recurso => $acciones) {
            if ($acciones == '*') {
                $urls[] = "$recurso/*";
            } else {                
                foreach ($acciones as $accion) {
                    $urls[] = "$recurso/$accion";
                }
            }

            $this->_acl->allow($perfil, $urls);
        }
 
        if (empty($recursos)) {
            $urls[] = "*";
            $this->_acl->allow($perfil, $urls);
        }
    }
 
    public function check($idPerfil, $modulo, $controlador, $accion) {
        if ( isset ($this->_templates["$idPerfil"]) ){
            View::template("{$this->_templates["$idPerfil"]}");            
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
        return $this->_acl->check($recurso1, $idPerfil) ||
        $this->_acl->check($recurso2, $idPerfil) ||
        $this->_acl->check($recurso3, $idPerfil);
    }
 
    protected function _establecerTemplates($templates){        
        foreach ($templates as $perfil => $template){            
            $this->_templates["$perfil"] = $template ;
        }
    }
 
}