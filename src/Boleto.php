<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

    protected $valor; // float
    protected $colectivo; // ColectivoInterface
    protected $tarjeta; // TarjetaInterface
    protected $fecha; // int
    protected $tarjetaTipo; // string
    protected $totalAbonado; // float
    protected $tarjetaSaldo; // float
    protected $tarjetaID; // int

    public function __construct($valor, $colectivo, $tarjeta) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
        $this->tarjeta = $tarjeta;
        // todos los valores internos de la tarjeta se asignan en el constructor para que no varien con el uso de la misma
        $this->fecha = $tarjeta->obtenerFechaUltimoViaje();
        $this->tarjetaTipo = $tarjeta->obtenerTipo();
        $this->totalAbonado = (int)(!$tarjeta->obtenerUsoPlus()) * ($valor + $tarjeta->obtenerPlusDevueltos() * $tarjeta->obtenerValor());
        $this->tarjetaSaldo = $tarjeta->obtenerSaldo();
        $this->tarjetaID = $tarjeta->obtenerId();
    }

    public function obtenerValor() {
        return $this->valor;
    }

    public function obtenerColectivo() {
        return $this->colectivo;
    }

    public function obtenerTarjeta() {
        return $this->tarjeta;
    }

}
