<?php

Load::model('admin/perfil');

class Usuario extends ActiveRecord {

    //public $debug = true;

    /**
     * Se ejecuta antes de un create/update
     */
    public function before_save() {
        // verifica si el usuario logueado puede editar el usuario
        if (!$this->puedeEditar($this->id)) {
            Flash::error('No tiene permisos para editar el usuario');
            return 'cancel';
        }        
    }

    public function before_update() {
        // Verifica si el usuario logueado es administrador full de usuarios.
        $esAdministradorUsr = (new Perfil)->esAdministradorUsuarios();                    
        // Verifica si se está actualizando el password      
        if (!empty($_POST['passwordActual']) || !empty($_POST['password1']) || !empty($_POST['password2'])) {  
            if (!empty($_POST['password1']) && !empty($_POST['password2'])) {                    
                if (trim($_POST['password1']) !== trim($_POST['password2'])) {
                    Flash::error('Los passwords ingresadas no coinciden.');
                    return 'cancel';
                } 
            }
            else {
                Flash::error('El nuevo password y su confirmación no coinciden.');
                return 'cancel';
            }            
            // Si es administrador full de usuarios no necesita el password actual.
            if (!$esAdministradorUsr) {
                if (!empty($_POST['passwordActual'])) {
                    if (!MyAuth::verificar_pass(trim($_POST['passwordActual']) . PASS_KEY, $usuario->password)) {
                        Flash::error('El password actual no es correcto');
                        return 'cancel';
                    }                    
                }
                else {
                    Flash::error('Debe completar el password actual');
                    return 'cancel';
                }                
            }
            $this->password = MyAuth::encriptar(trim($_POST['password1'] . PASS_KEY));    
        }        
        
    }

    public function crear($datos){
        $obj = new Usuario($datos);        
        $obj->password = MyAuth::encriptar(trim($obj->password) . PASS_KEY);
        return $obj->create();
    }

    public function actualizar($datos) {                        
        $obj = $this->find_first($datos['id']);
        if ($obj) {
            // Verifica si el usuario logueado es administrador full de usuarios.
            $esAdministradorUsr = (new Perfil)->esAdministradorUsuarios();
            if ($esAdministradorUsr) {
                return $obj->update($datos);
            }            
            // Si no es administrador solo puede modificar el password y los campos definidos de forma explícita (por seguridad).
            // Modificar la vista app/views/admin/usuarios/editar.phtml
            // $obj->campoModifificable = $datos['campoModificable'];
            return $obj->update();
        }
        return false;
    }

    public function puedeEditar($id) {
        // El usuario logueado está editando sus propios datos.
        if (Session::get('id') ==  $id) {
            return true;
        }
        // Verifica si el perfil logueado tiene permisos edición de usuario
        elseif ((new Perfil)->esAdministradorUsuarios()) {
            return true;
        }
        return false;
    }
}