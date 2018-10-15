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
    protected $abonado; // float
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
        $plusAbonados = $tarjeta->obtenerPlusDevueltos() * $tarjeta->obtenerValorViaje();
        $this->abonado = (int)(!$tarjeta->obtenerUsoPlus()) * ($valor + $plusAbonados);
        if ($tarjeta->obtenerUsoTransbordo()) $this->tipo = "Transbordo";
        else if ($tarjeta->obtenerUsoPlus()) $this->tipo = "Viaje Plus";
        else if ($valor == $tarjeta->obtenerValorViaje()) $this->tipo = "Normal";
        else $this->tipo = $this->tarjetaTipo;
        $this->descripcion = "";
        $this->descripcion .= "Linea: {$colectivo->linea()}\n{$this->fecha}\n";
        if ($tarjeta->obtenerPlusDevueltos() > 0) $this->descripcion .= "Abona {$tarjeta->obtenerPlusDevueltos()} Viajes Plus \${$plusAbonados} y\n";
        $this->descripcion .= "{$this->tipo} ";
        if ($tarjeta->obtenerUsoPlus()) $this->descripcion .= "{$tarjeta->obtenerPlus()} \$0.00\n";
        else {
            $this->descripcion .= "\${$valor}\nTotal abonado: \${$this->abonado}\n";
        }
        $this->descripcion .= "Saldo(S.E.U.O): \${$this->tarjetaSaldo}\nTarjeta: {$this->tarjetaID}";
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

    public function obtenerAbonado() {
        return $this->abonado;
    }

    public function obtenerDescripcion() {
        return $this->descripcion;
    }

}
