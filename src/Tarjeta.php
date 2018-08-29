<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $saldo = 0; // float
    protected $plus = 0; // int
    protected $valorViaje = 14.8; // float
    /**
    * valorViaje corresponde al valor sin franquicia aplicada
    * Luego el metodo obtenerViajePlus se encarga de aplicar las franquicias
    */

    public function recargar($monto) {
      if ($monto == 10 || $monto == 20 || $monto == 30 || $monto == 50 || $monto == 100 || $monto == 510.15 || $monto == 962.59) {
        $monto -= ($this->plus * $this->valorViaje); // los viajes plus siempre valdran el valor completo, sin franquicia
        $this->saldo += $monto;
        $this->plus = 0;
        if ($monto == 510.15) $this->saldo += 81.93;
        if ($monto == 962.59) $this->saldo += 221.58;
        return true;
      }
      return false;
    }

    public function obtenerSaldo() {
      return $this->saldo;
    }

    public function obtenerValorViaje() {
      return $this->valorViaje;
    }

    public function pagar() {
      if ($this->saldo < 0) return false; // no se toleraran valores negativos
      if ($this->saldo < $this->obtenerValorViaje()) {
        if ($this->plus >= 2) return false; // como maximo podra adeudar 2 viajes plus
        $this->plus ++;
      }
      else $this->saldo -= $this->obtenerValorViaje();
      return true;
    }

}
