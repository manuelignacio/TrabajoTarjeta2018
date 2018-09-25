<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {

    protected $valor; // float
    protected $colectivo; // ColectivoInterface
    protected $tarjeta; // TarjetaInterface
    protected $fecha; // string
    protected $tarjetaTipo; // string
    protected $tarjetaID; // int
    protected $tarjetaSaldo; // float
    protected $totalAbonado; // float
    protected $descripcion; // string

    public function __construct($valor, $colectivo, $tarjeta) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
        $this->tarjeta = $tarjeta;
        // todos los valores internos de la tarjeta se asignan en el constructor para que no varien con el uso de la misma
        $this->fecha = date("d/m/Y H:i:s", $tarjeta->obtenerFechaUltimoViaje());
        $this->tarjetaTipo = $tarjeta->obtenerTipo();
        $this->tarjetaID = $tarjeta->obtenerId();
        $this->tarjetaSaldo = $tarjeta->obtenerSaldo();
        $plusAbonados = $tarjeta->obtenerPlusDevueltos() * $tarjeta->obtenerValor();
        $this->totalAbonado = (int)(!$tarjeta->obtenerUsoPlus()) * ($valor + $plusAbonados);
        $this->descripcion = "";
        $this->descripcion .= "Linea: {$colectivo->linea()}\n{$this->fecha}\n";
        if ($tarjeta->obtenerUsoPlus()) $this->descripcion .= "Viaje Plus {$tarjeta->obtenerPlusEnDeuda()} \$0.00\n";
        else {
            if ($valor == $tarjeta->obtenerValor()) $this->descripcion .= "Normal\n";
            else $this->descripcion .= "{$this->tarjetaTipo}\n";
            if ($tarjeta->obtenerPlusDevueltos() > 0) $this->descripcion .= "Abona {$tarjeta->obtenerPlusDevueltos()} Viajes Plus \${$plusAbonados} y\n";
            $this->descripcion .= "Tarros: \${$valor}\nTotal abonado: \${$this->totalAbonado}\n";
        }
        $this->descripcion .= "Saldo(S.E.U.O): \${$this->tarjetaSaldo}\nID Tarjeta: {$this->tarjetaID}";
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

    public function obtenerFecha() {
        return $this->fecha;
    }

    public function obtenerTarjetaTipo() {
        return $this->tarjetaTipo;
    }

    public function obtenerTarjetaID() {
        return $this->tarjetaID;
    }

    public function obtenerTarjetaSaldo() {
        return $this->tarjetaSaldo;
    }

    public function obtenerTotalAbonado() {
        return $this->totalAbonado;
    }

    public function obtenerDescripcion() {
        return $this->descripcion;
    }

}
