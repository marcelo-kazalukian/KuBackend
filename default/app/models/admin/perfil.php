<?php

class Perfil extends ActiveRecord {

    // public $debug = true;

    /**
     * Se ejecuta antes del create/update
     */
    public function before_save() {
        if (!$this->esAdministradorUsuarios()) {
            Flash::error('No tiene permisos para editar el usuario');
            return 'cancel';
        }
    }

    /**
     * Se ejecuta antes del delete
     */
    public function before_delete() {
        if (!$this->esAdministradorUsuarios()) {
            Flash::error('No tiene permisos para editar el usuario');
            return 'cancel';
        }
    }
    
    /**
     * Devuelve todos los perfiles.
     */
    public function getPerfiles() {
        return $this->find();
    }

    /**
     * Verifica si el perfil logueado tiene admnistración full de usuarios del sistema.
     * Levar a BD si se quiere una adminstración dinámica.
     */
    public function esAdministradorUsuarios() {        
        if (Session::get('perfil_id') == 1) {
            return true;
        }
        return false;
    }
    

    /**
     * Devuelve la url de la pantalla inicial para el perfil logueado.
     * Llevar a BD si se quiere una adminstración dinámica.
     */
    public function getViewInicial() {
        $idPerfil = (Session::get('perfil_id'));
        switch ($idPerfil) {
            case 1:
                return  'admin/usuarios';
                break;            
            default:
                return 'dashboard';
                break;
        }
    }

    /**
     * Devuelve el menú de navegación según el perfil logueado.
     */
    public function getMenuNavegacion() {
        $idPerfil = (Session::get('perfil_id'));
        switch ($idPerfil) {
            case 1:
                return  [
                        ['label' => 'Inicio', 'url' => $this->getViewInicial()],
                        ['label' => 'Usuarios', 'url' => 'admin/usuarios'],
                        ['label' => 'Perfiles', 'url' => 'admin/perfiles'],
                        ['label' => 'Salir', 'url' => 'login/salir']
                        ];
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Acá se deben definir los perfiles que tiene la aplicación y sus padres.
     * 
     * Ejemplo:
     * [
     *  1 => null,
     *  2 => [1],
     *  3 => null,
     *  4 => [1, 3]
     * ]
     * 
     * Perfil 1 no hereda permisos de ningún perfil.
     * Perfil 2 hereda los permisos del perfil 1. Además tiene los permisos definidos explicitamente para el perfil 2.
     * Perfil 3 no hereda permisos de ningún perfil.
     * Perfil 4 hereda los permisos del perfil 1 y 3. Además tiene los permisos definidos explicitamente para el perfil 4.
     * 
     */
    public function getPerfilesYPadres() {
        return [
            1 => null            
        ];
    }

    /**
     * Se definen los recursos permitidos para cada uno de los perfiles del sistema.
     * 
     * Ejemplo:
     * [
     *  'admin/usuarios' => ['index'],
     *  'usuarios' => ['index', 'crear', 'editar],
     *  'perfiles => ['*']
     * ]
     * 
     */
    public function getRecursos($idPerfil) {
        switch ($idPerfil) {
            case 1:
                return [
                    'admin/usuarios' => ['*'],                    
                    'admin/perfiles' => ['*']
                ];
                break;            
            default:
                return ['Perfil inválido' => ['Sin permisos']];
                break;
        }
    }

    /**
     * Se definen los templates para cada uno de los perfiles del sistema.
     */
    public function getTemplates() {
        return [
            1 => 'default'            
        ];
    }
}