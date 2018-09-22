<?php

namespace App\Http\Controllers\clases_catastro;

class ReferenciaCatastro {

    public $referenciaCatastral = '';
    public $direccion = '';
    public $codigoPostal = '';
    public $provincia = '';
    public $municipio = '';
    public $entidadMenor = '';
    public $distritoMunicipal = '';
    public $residencial = '';
    public $superficie = '';
    public $coeficienteParticipacion = '';
    public $antiguedad = '';
    public $constructivas = [];
    public $subparcelas = [];
// rústica
    public $poligono = '';
    public $parcela = '';
    public $paraje = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($datos, $completa = false) {
        if ($completa) {
            $bien = $datos->bi;
            $referenciaCatastral = $bien->idbi;
        } else {
            $bien = $datos;
            $referenciaCatastral = $datos;
        }
        $this->referenciaCatastral = $referenciaCatastral->rc->pc1 . $referenciaCatastral->rc->pc2 . $referenciaCatastral->rc->car . $referenciaCatastral->rc->cc1 . $referenciaCatastral->rc->cc2;
        if (isset($bien->dt->locs->lous)) { // LOCALIZACIÓN MUNICIPAL DEL BIEN URBANO
            $this->direccion = $this->parseDireccion($bien->dt->locs->lous);
        } elseif (isset($bien->dt->locs->lors)) { // LOCALIZACIÓN MUNICIPAL EL BIEN RÚSTICO
            $this->direccion = $this->parseDireccion($bien->dt->locs->lors);
        }
        $this->provincia = isset($bien->dt->np) ? ucwords(strtolower($bien->dt->np)) : '';
        $this->municipio = isset($bien->dt->nm) ? ucwords(strtolower($bien->dt->nm)) : '';
        $this->entidadMenor = isset($bien->dt->nem) ? ucwords(strtolower($bien->dt->nem)) : '';
        if ($completa) {
            if (isset($bien->debi)) { // DATOS ECONÓMICOS DEL INMUEBLE
                $economicos = $bien->debi;
                $this->residencial = isset($economicos->luso) ? ucwords(strtolower($economicos->luso)) : '';
                $this->superficie = isset($economicos->sfc) ? (int) $economicos->sfc : '';
                $this->coeficienteParticipacion = isset($economicos->cpt) ? (double) $economicos->cpt : '';
                $this->antiguedad = isset($economicos->ant) ? (int) $economicos->ant : '';
            }
            if (isset($datos->lcons->cons)) { // LISTA DE UNIDADES CONSTRUCTIVAS
                $constructivas = $datos->lcons->cons;
                foreach ($constructivas as $constructiva) {
                    $this->constructivas[] = new UnidadConstructiva($constructiva);
                }
            }
            if (isset($datos->lspr->spr)) { // LISTA DE SUBPARCELAS
                $subparcelas = $datos->lspr->spr;
                foreach ($subparcelas as $subparcela) {
                    $this->subparcelas[] = new Subparcela($subparcela);
                }
            }
        }
    }

    private function parseDireccion($datos) {

        $respuesta = '';
        if (isset($datos->lourb)) { // LOCALIZACIÓN URBANA
            if (isset($datos->lourb->dp))
                // Convertimos a json
                $this->codigoPostal = (int) $datos->lourb->dp;
            if (isset($datos->lourb->dm))
                $this->distritoMunicipal = (int) $datos->lourb->dm;
            $direccion = $datos->lourb->dir;
            $respuesta.=isset($direccion->tv) ? $direccion->tv . ' ' : '';
            $respuesta.=isset($direccion->nv) ? $direccion->nv . ', ' : ', ';
            $respuesta.=$this->concatena($direccion, ['pnp', 'plp', 'snp', 'slp']);
            $respuesta.=isset($direccion->km) ? 'Km. ' . $direccion->km . '' : '';
            if (isset($datos->lourb->loint)) { // LOCALIZACIÓN INTERNA
                $interna = $datos->lourb->loint;
                $respuesta.=isset($interna->bq) ? ' Bloque ' . $interna->bq : '';
                $respuesta.=(isset($interna->es) && $interna->es != 1) ? ' Escalera ' . $interna->es : '';
                $respuesta .= isset($interna->pt) ? ' Planta ' . (((string) $interna->pt == '00') ? 'baja' : (int) $interna->pt) : '';
                if (isset($interna->pu)) {
                    $puerta = $interna->pu;
                    switch ($puerta) {
                        case 'IZ': $puerta = "Izquierda"; break;
                        case 'DR': $puerta = "Derecha"; break;
                        default:
                            $puerta = ((int) $puerta) ? (int) $puerta : (string) $puerta;
                            break;
                    }
                    $respuesta.= ' Puerta ' . $puerta;
                }
            }
        } elseif (isset($datos->lorus)) { // LOCALIZACIÓN RÚSTICA
            $this->poligono = isset($datos->lorus->cpp->cpo) ? $datos->lorus->cpp->cpo : '';
            $this->parcela = isset($datos->lorus->cpp->cpa) ? $datos->lorus->cpp->cpa : '';
            $this->paraje = isset($datos->lorus->npa) ? ucwords(strtolower($datos->lorus->npa)) : '';
            $respuesta = '';
        }
        $respuesta = ucwords(strtolower($respuesta));
        return $respuesta;
    }

    private function concatena($datos, $array) {
        $numeros = [];
        foreach ($array as $campo) {
            if (isset($datos->$campo)) {
                if ($campo == 'snp') {
                    if ($datos->$campo > 0)
                        $numeros[] = $datos->$campo;
                } else {
                    $numeros[] = $datos->$campo;
                }
            }
        }
        return implode('-', $numeros);
    }

}
