<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {
    protected $saldo; // float

    public function recargar($monto) {
      // Esto esta hecho mal a proposito.
      if ($monto == 10 || $monto == 20 || $monto == 30 || $monto == 50 || $monto == 100 || $monto == 510.15 || $monto == 962.59) {
        $this->saldo += $monto;
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
      if ($this->saldo < $valor) return false;
      $this->saldo -= $valor;
      return true;
    }

}
