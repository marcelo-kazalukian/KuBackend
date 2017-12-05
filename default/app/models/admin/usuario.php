<?php

Load::model('admin/perfil');

class Usuario extends ActiveRecord {

    //public $debug = true;

    public function initialize() {
        $this->belongs_to('perfil');
    }

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

    public function cambiar_password($idUsuario, $pass, $pass1, $pass2) {
        // Verifica si se está actualizando el password      
        if ( !empty($pass1) || !empty($pass2) ) {  
            if ( !empty($pass1) && !empty($pass2) ) {                    
                if (trim($pass1) !== trim($pass2) ) {
                    Flash::error('Los passwords ingresadas no coinciden.');
                    return false;
                } 
            }
            else {
                Flash::error('El nuevo password y su confirmación no coinciden.');
                return false;
            }            
            // Verifica si el usuario logueado es administrador full de usuarios.
            $esAdministradorUsr = (new Perfil)->esAdministradorUsuarios();                    
            
            // Si es administrador full de usuarios no necesita el password actual.
            // Si no es administrador full de usuarios significa que un usuario está cambiando su propia pass
            $usuario = $this->find_first($idUsuario);
            if (!$esAdministradorUsr) {
                if (!empty($pass)) {
                    if (!MyAuth::verificar_pass(trim($pass) . PASS_KEY, $usuario->password)) {
                        Flash::error('El password actual no es correcto');
                        return false;
                    }                    
                }
                else {
                    Flash::error('Debe completar el password actual');
                    return false;
                }                
            }
            $usuario->password = MyAuth::encriptar(trim($pass1 . PASS_KEY));    
            return $usuario->update();
        }                
        Flash::error('Debe completar todos los campos.');
        return false;
    }
}