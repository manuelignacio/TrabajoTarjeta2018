<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

    protected $valor; // float
    protected $colectivo; // ColectivoInterface

    public function __construct($valor, $colectivo, $tarjeta) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
    }

    /**
     * Devuelve el valor del boleto.
     *
     * @return float
     */
    public function obtenerValor() {
        return $this->valor;
    }

    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */
    public function obtenerColectivo() {
        return $this->colectivo;
    }

}
