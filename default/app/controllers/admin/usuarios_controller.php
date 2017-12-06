<?php

Load::model('admin/usuario');

class UsuariosController extends ScaffoldController {
    public $model = 'Usuario';

    /**
     * Crea un Registro
     */
    public function crear()
    {
        if (Input::hasPost($this->model)) {

            $obj = new $this->model;
            //En caso que falle la operación de guardar
            if (!$obj->crear(Input::post($this->model))) {
                Flash::error('Falló Operación');
                //se hacen persistente los datos en el formulario
                $this->{$this->model} = $obj;
                return;
            }
            return Redirect::to();
        }
        // Sólo es necesario para el autoForm
        $this->{$this->model} = new $this->model;
    }


    /**
     * Edita la información de un usuario
     */
    public function editar($id) {      
        
        // Verifica si el usuario logueado puede editar al usuario seleccionado.
        if ((new Usuario)->puedeEditar($id)) {
            //se verifica si se ha enviado via POST los datos                        
            if (Input::hasPost($this->model)) {                
                $obj = new $this->model;
                if (!$obj->actualizar(Input::post($this->model))) {
                    Flash::error('Falló Operación');
                    //se hacen persistente los datos en el formulario
                    $this->{$this->model} = Input::post($this->model);
                } else {
                    Flash::valid('Usuario actualizado');                    
                }
            }
            //Aplicando la autocarga de objeto, para comenzar la edición
            $this->{$this->model} = (new $this->model)->find((int) $id);            

            // Verifica si el usuario logueado es administrador de usuarios
            $this->esAdministradorUsuarios = (new Perfil)->esAdministradorUsuarios();
        }
        else {
            Flash::error('No tiene permisos para editar al usuario.');
            return Redirect::to((new Perfil)->getViewInicial(Session::get('perfil_id')));            
        }
    }

    public function cambiar_password($idUsuario) {
        // Verifica si el usuario logueado puede editar al usuario seleccionado.
        if ((new Usuario)->puedeEditar($idUsuario)) {
            //se verifica si se ha enviado via POST los datos                        
            if (Input::hasPost('pass', 'pass1', 'pass2')) {                
                $obj = new $this->model;
                if (!$obj->cambiar_password($idUsuario, Input::post('pass'),  Input::post('pass1'), Input::post('pass2'))) {
                    Flash::error('Falló Operación');
                    //se hacen persistente los datos en el formulario                
                } else {
                    Flash::valid('Usuario actualizado');                    
                }
            }
            // Verifica si el usuario logueado es administrador de usuarios
            $this->esAdministradorUsuarios = (new Perfil)->esAdministradorUsuarios();
        }
        else {
            Flash::error('No tiene permisos para editar al usuario.');
            return Redirect::to((new Perfil)->getViewInicial(Session::get('perfil_id')));            
        }
    }
}