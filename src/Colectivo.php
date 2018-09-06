<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {

    protected $linea; // string
    protected $empresa; // string
    protected $numero; // int

    public function __construct($linea, $empresa, $numero) {
        $this->linea = $linea;
        $this->empresa = $empresa;
        $this->numero = $numero;
    }

    public function linea() {
        return $this->linea;
    }

    public function empresa() {
        return $this->empresa;
    }

    public function numero() {
        return $this->numero;
    }

    public function pagarCon(TarjetaInterface $tarjeta) {
        if ($tarjeta->pagar()) return (new Boleto($tarjeta->obtenerValorViaje(),$this,$tarjeta));
        return false;
    }

}
