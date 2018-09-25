<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $id; // int
    protected $tiempo; // TiempoInterface
    protected $saldo = 0; // float
    protected $valorViaje = 14.8; // float
    protected $fechaUltimoViaje = 0; // int
    protected $usoPlus = false; // bool
    protected $plus = 0; // int
    protected $plusDevueltos = 0; // int
    protected $lineaUltimoColectivo = ""; // string
    protected $puedeTransbordo = false; // bool
    protected $usoTransbordo = false; // bool
    /**
    * valorViaje corresponde al valor sin franquicia aplicada
    * Luego el metodo obtenerValorViaje se encarga de aplicar las franquicias y el transbordo
    * 
    * plus indica cuantos viajes plus hay en deuda actualmente
    * 
    * plusDevueltos indica la cantidad de viajes plus que se devolvieron en
    * el ultimo viaje efectuado, incluso cuando no se devuelva ninguno y
    * entonces valga 0
    */

    public function __construct(int $id, TiempoInterface $tiempo) {
      $this->id = $id;
      $this->tiempo = $tiempo;
    }

    public function obtenerTipo() {
      return "Normal";
    }

    public function obtenerId() {
      return $this->id;
    }

    public function obtenerValor() {
      return $this->valorViaje;
    }

    public function recargar($monto) {
      if ($monto == 10 || $monto == 20 || $monto == 30 || $monto == 50 || $monto == 100 || $monto == 510.15 || $monto == 962.59) {
        $this->saldo += $monto;
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
      if ($this->puedeTransbordo) return 33 / 100 * $this->valorViaje;
      return $this->valorViaje;
    }

    public function pagar() {
      if ($this->saldo < 0) return false; // no se toleraran valores negativos
      
      $precioViaje = $this->obtenerValorViaje();
      $precioPlusEnDeuda = $this->plus * $this->valorViaje;
      $precioTotal = $precioViaje + $precioPlusEnDeuda;
      $pagaPlus = $this->saldo < $precioTotal;
      
      if ($pagaPlus) {
        if ($this->plus >= 2) return false; // como maximo podra adeudar 2 viajes plus
        ++$this->plus;
        $this->usoPlus = true;
        $this->plusDevueltos = 0;
      }
      else {
        $this->plusDevueltos = $this->plus;
        $this->plus = 0;
        $this->saldo -= $precioTotal;
        $this->usoPlus = false;
      }
      $this->fechaUltimoViaje = $this->tiempo->actual();
      return true;
    }

    public function pagarEn($colectivo) {
      $ahora = $this->tiempo->actual();
      $diaDeSemana = date("N", $ahora); // N devuelve el dia de la semana correspondiendo 1 para lunes y 7 para domingo
      $horaDeDia = date("H", $ahora);
      $rangoLunAVie6a22 = $diaDeSemana >= 1 && $diaDeSemana <= 5 && $horaDeDia >= 6 && $horaDeDia < 22;
      $rangoSab6a14 = $diaDeSemana == 6 && $horaDeDia >= 6 && $horaDeDia < 14;
      if ($rangoLunAVie6a22 || $rangoSab6a14) $lapso = 3600; // 60 min
      else $lapso = 5400; // 90 min

      $this->puedeTransbordo = ($ahora - $this->fechaUltimoViaje) <= $lapso && !$this->usoTransbordo && $colectivo->linea() != $this->lineaUltimoColectivo;

      if ($this->pagar()) {
        $this->lineaUltimoColectivo = $colectivo->linea();
        $this->usoTransbordo = !$this->usoPlus && $this->puedeTransbordo;
        return true;
      }
      return false;
    }
    
    public function obtenerFechaUltimoViaje() {
      return $this->fechaUltimoViaje;
    }

    public function obtenerUsoPlus() {
      return $this->usoPlus;
    }

    public function obtenerPlusEnDeuda() {
      return $this->plus;
    }

    public function obtenerPlusDevueltos() {
      return $this->plusDevueltos;
    }

    public function obtenerUsoTransbordo() {
      return $this->usoTransbordo;
    }

}
