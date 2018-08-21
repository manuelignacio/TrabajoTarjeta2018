<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {
    protected $saldo = 0; // float
    protected $plus = 0; // int

    public function recargar($monto) {
      if ($monto == 10 || $monto == 20 || $monto == 30 || $monto == 50 || $monto == 100 || $monto == 510.15 || $monto == 962.59) {
        $monto -= ($this->plus * 14.8); // el costo de los viajes plus siempre sera 14.8, independientemente de su franquicia
        $this->saldo += $monto;
        $this->plus = 0;
        if ($monto == 510.15) $this->saldo += 81.93;
        if ($monto == 962.59) $this->saldo += 221.58;
        return true;
      }
      return false;
    }

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */
    public function obtenerSaldo() {
      return $this->saldo;
    }

    public function pagar($valor) {
      if ($this->saldo < 0) return false; // no se toleraran valores negativos
      if ($this->saldo < $valor) {
        if ($this->plus > 1) return false; // como maximo podra adeudar 2 viajes plus
        $this->plus ++;
      }
      else $this->saldo -= $valor;
      return true;
    }

}
