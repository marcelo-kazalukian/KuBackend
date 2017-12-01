<?php

Load::lib('my_auth');

// Cambiar la PASS_KEY por una propia. La PASS_KEY se concatenará a todas las password para asegurar
// que las passwors tengan una complejidad adecuada.
define('PASS_KEY', 'TuPassKey');

//Cambiar la SESSION_KEY por una propia. Se puede utilizar las de wordpress: https://api.wordpress.org/secret-key/1.1/salt/
define('SESSION_KEY', '(G;T&aZ-tHrps(FOjhT;v0ln6M|&m,5QNa)8q$]o{=Eo<xe+YQEX0XCvM%?ju~Hb');

class KAuth {
    
    /**
     * Realiza el proceso de autenticación
     * 
     * @param $login string Valor opcional del nombre de usuario en la bd
     * @param $pass string Valor opcional de la contraseña del usuario en la bd
     * @return bool
     */
    public static function login($login = '', $password = '') {        
        // clase Base para la gestion de autenticación
        $auth = MyAuth::factory();            
        
        // verificar que no se inicie sesion desde browser distinto con la misma IP
        $auth->setCheckSession(true);
        
        // Modelo a utilizar para el proceso de autenticacion
        $auth->setModel('usuario');                          
        
        // Indica que campos del modelo se cargaran en sesion              
        $auth->setFields(array('id', 'nombre', 'apellido', 'login', 'perfil_id'));                                           
        // Realiza el proceso de identificacion.
        if ($auth->identify($login, $password . PASS_KEY, 'auth')) {
            Session::set(SESSION_KEY, true);
            return true;
        }
        else {
            Session::set(SESSION_KEY, false);
            return false;
        }
    }

    /**
     * logout
     *
     * @param void
     * @return void
     */
    public static function logout() {
        // clase Base para la gestion de autenticación
        $auth = MyAuth::factory('model');
        // logout
        $auth->logout();        
        Session::set(SESSION_KEY, false);
    }

    /**
     * Verifica que exista una identidad válida para la session actual
     *
     * @return bool
     */
    public static function isLogged() {
        // clase Base para la gestion de autenticación
        $auth = MyAuth::factory('model');
        // Verifica que exista una identidad válida para la session actual
        return $auth->isValid() && Session::has(SESSION_KEY) && Session::get(SESSION_KEY) === TRUE;        
    }
}