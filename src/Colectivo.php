<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {

    protected $linea; // string
    protected $empresa; // string
    protected $numero; // int

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
        if ($tarjeta->pagar(14.8)) return (new Boleto(14.8,$this,$tarjeta));
        return false;
    }

}
