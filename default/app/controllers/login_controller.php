<?php

Load::model('admin/perfil');

class LoginController extends AppController {

    public function entrar() {
        
        if (KAuth::isLogged()) {        
            return Redirect::to((new Perfil)->getViewInicial());
        }
        else {
            if(Input::hasPost('login', 'password')) {                          
                
                if (KAuth::login(Input::post('login'), Input::post('password'))) {
                    //Redirecciona a la pantalla de inicio según perfil.
                    return Redirect::to((new Perfil)->getViewInicial());
                }
                
                Flash::error('Usuario y/o password incorrectos');            
                
            }                 
        }    
    }

    public function salir() {
        KAuth::logout();
        return Redirect::to('/');                 
    }
}