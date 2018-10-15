<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $id; // int
    protected $tiempo; // TiempoInterface
    protected $saldo = 0; // float
    protected $valorViaje = 14.8; // float
    protected $plus = 0; // int
    protected $usoPlus = false; // bool
    protected $plusDevueltos = 0; // int
    protected $fechaUltimoViaje = 0; // int
    protected $ultimaLineaColectivo = ""; // string
    protected $lapsoTransbordo = 0; // int
    protected $puedeTransbordo = false; // bool
    protected $usoTransbordo = false; // bool
    /**
    * valorViaje corresponde al valor sin franquicia aplicada
    * Luego el metodo valorViaje se encarga de aplicar las
    * franquicias y el transbordo
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

    public function obtenerSaldo() {
      return $this->saldo;
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

    public function obtenerValorViaje() {
      return $this->valorViaje;
    }

    public function valorViaje() {
      if ($this->puedeTransbordo) return 33 / 100 * $this->valorViaje;
      return $this->valorViaje;
    }

    public function pagar() {
      if ($this->saldo < 0) return false; // no se toleraran valores negativos
      
      $precioViaje = $this->valorViaje();
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

    /**
     * Si se unieran ambos metodos (pagar y pagarEn) en uno,
     * podria simplificarse un poco la lógica usada, pero
     * también cambiaría el comportamiento en todos los
     * tests hechos hasta ahora.
     */
    public function pagarEn(string $lineaColectivo) {
      $ahora = $this->tiempo->actual();
      $this->puedeTransbordo = ($ahora - $this->fechaUltimoViaje) <= $this->lapsoTransbordo && !$this->usoTransbordo && $lineaColectivo != $this->ultimaLineaColectivo;

      if ($this->pagar()) {
        $diaDeSemana = date("N", $ahora); // N devuelve el dia de la semana correspondiendo 1 para lunes y 7 para domingo
        $horaDeDia = date("H", $ahora);
        $rangoLunAVie6a22 = $diaDeSemana >= 1 && $diaDeSemana <= 5 && $horaDeDia >= 6 && $horaDeDia < 22;
        $rangoSab6a14 = $diaDeSemana == 6 && $horaDeDia >= 6 && $horaDeDia < 14;
        if ($rangoLunAVie6a22 || $rangoSab6a14) $this->lapsoTransbordo = 3600; // 60 min
        else $this->lapsoTransbordo = 5400; // 90 min
        $this->ultimaLineaColectivo = $lineaColectivo;
        $this->usoTransbordo = !$this->usoPlus && $this->puedeTransbordo;
        return true;
      }
      return false;
    }

    public function obtenerPlus() {
      return $this->plus;
    }

    public function obtenerUsoPlus() {
      return $this->usoPlus;
    }

    public function obtenerPlusDevueltos() {
      return $this->plusDevueltos;
    }

    public function obtenerFechaUltimoViaje() {
      return $this->fechaUltimoViaje;
    }

    public function obtenerUsoTransbordo() {
      return $this->usoTransbordo;
    }

}
