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
    protected $tipo; // string
    protected $descripcion; // string

    /**
     * Todos los valores internos de la tarjeta
     * se asignan en el constructor para que no
     * varien con el uso de la misma
     */
    public function __construct(float $valor, ColectivoInterface $colectivo, TarjetaInterface $tarjeta) {
        $this->valor = $valor;
        $this->colectivo = $colectivo;
        $this->tarjeta = $tarjeta;
        $this->fecha = date('d/m/Y H:i:s', $tarjeta->obtenerFechaUltimoViaje());
        $this->tarjetaTipo = $tarjeta->obtenerTipo();
        $this->tarjetaID = $tarjeta->obtenerId();
        $this->tarjetaSaldo = $tarjeta->obtenerSaldo();
        $usoPlus = $tarjeta->obtenerUsoPlus();
        $plusDevueltos = $tarjeta->obtenerPlusDevueltos();
        $plusAbonados = $plusDevueltos * $tarjeta->obtenerValorViaje();
        $this->abonado = (int) (!$usoPlus) * ($valor + $plusAbonados);
        if ($usoPlus) {
            $this->tipo = "Viaje Plus";
        }
        else {
            if ($tarjeta->obtenerUsoFranquicia()) {
                $this->tipo = $this->tarjetaTipo;
            }
            else {
                $this->tipo = "Normal";
            }
            if ($tarjeta->obtenerUsoTransbordo()) {
                $this->tipo .= " Transbordo";
            }
        }
        $this->descripcion = "";
        $this->descripcion .= "Linea: {$colectivo->linea()}\n{$this->fecha}\n";
        if ($plusDevueltos > 0) {
            $this->descripcion .= "Abona {$plusDevueltos} Viajes Plus \${$plusAbonados} y\n";
        }
        $this->descripcion .= "{$this->tipo} ";
        if ($usoPlus) {
            $this->descripcion .= "{$tarjeta->obtenerPlus()} \$0.00\n";
        }
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

    public function obtenerTipo() {
        return $this->tipo;
    }

    public function obtenerDescripcion() {
        return $this->descripcion;
    }

}
