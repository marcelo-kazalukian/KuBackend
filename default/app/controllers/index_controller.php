<?php

/**
 * Controller por defecto si no se usa el routes
 *
 */
class IndexController extends appController
{

    public function index() {
        return Redirect::to('login/entrar');
    }

}
