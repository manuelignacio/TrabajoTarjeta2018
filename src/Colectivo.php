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
        $valorViaje = $tarjeta->valorViaje($this->linea); // se obtiene el valor del viaje que se va a efectuar
        if ($tarjeta->pagar($this->linea)) return (new Boleto($valorViaje,$this,$tarjeta));
        return false;
    }

}
