<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

    protected $valor; // float
    protected $colectivo; // ColectivoInterface
    protected $tarjeta; // TarjetaInterface
    protected $fecha; // int
    protected $tarjetaTipo; // string
    protected $totalAbonado;

    public function __construct($valor, $colectivo, $tarjeta) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
        $this->tarjeta = $tarjeta;
        // todos los valores internos de la tarjeta se asignan en el constructor para que no varien con el uso de la misma
        $this->fecha = $tarjeta->obtenerFechaUltimoViaje();
        $this->tarjetaTipo = $tarjeta->tipo();
        $this->totalAbonado = (int)(!$tarjeta->usoPlus()) * ($valor + $tarjeta->plusDevueltos() * $tarjeta->valor());
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
