<?php

namespace App\Http\Controllers\clases_catastro;

class Subparcela {

    public $codigo = '';
    public $calificacionCatastral = '';
    public $claseCultivo = '';
    public $intensidadProductiva = '';
    public $superficie = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($datos) {
        $this->codigo = isset($datos->cspr) ? $datos->cspr : '';
        $this->calificacionCatastral = isset($datos->dspr->ccc) ? $datos->dspr->ccc : '';
        $this->claseCultivo = isset($datos->dspr->dcc) ? $datos->dspr->dcc : '';
        $this->intensidadProductiva = isset($datos->dspr->ip) ? $datos->dspr->ip : '';
        $this->superficie = isset($datos->dspr->ssp) ? $datos->dspr->ssp : '';
    }

}
