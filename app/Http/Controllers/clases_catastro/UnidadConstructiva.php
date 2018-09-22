<?php

namespace App\Http\Controllers\clases_catastro;

class UnidadConstructiva {

    public $uso = '';
    public $bloque = '';
    public $escalera = '';
    public $puerta = '';
    public $planta = '';
    public $superficie = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($datos) {
        $this->uso = isset($datos->lcd) ? ucwords(strtolower((string) $datos->lcd)) : '';
        if (isset($datos->dt->lourb->loint)) {
            $interior = $datos->dt->lourb->loint;
            $this->bloque = isset($interior->bq) ? (string) $interior->bq : '';
            $this->escalera = isset($interior->es) ? (string) $interior->es : '';
            $this->puerta = isset($interior->pu) ? (string) $interior->pu : '';
            $this->planta = isset($interior->pt) ? (string) $interior->pt : '';
        }
        $this->superficie = isset($datos->dfcons->stl) ? (int) $datos->dfcons->stl : '';
    }

}
