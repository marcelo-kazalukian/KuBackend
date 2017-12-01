<?php

class DashboardController extends AdminController {

    public function index() {
        $this->idPerfil = Session::get('perfil_id');
    }
}