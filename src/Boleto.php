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
    protected $descripcion; // string

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
        $this->descripcion = "";
        if ($tarjeta->obtenerUsoPlus()) $this->descripcion = "Viaje Plus";
        else {
            if ($tarjeta->obtenerPlusDevueltos() > 0) $this->descripcion = "Abona {$tarjeta->obtenerPlusDevueltos()} Viajes Plus\n";
            $this->descripcion .= $this->totalAbonado;
        }
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
