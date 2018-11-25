<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {

    protected $id; // int
    protected $saldo = 0; // float
    protected $valorViaje = 14.8; // float
    protected $plus = 0; // int
    // Factor tiempo:
    protected $tiempo; // TiempoInterface
    // Historial:
    protected $valorUltimoViaje = 0;
    protected $usoFranquicia = false; // bool
    protected $usoPlus = false; // bool
    protected $usoTransbordo = false; // bool
    protected $plusDevueltos = 0; // int
    protected $fechaUltimoViaje = 0; // int
    protected $lapsoTransbordo = 0; // int
    protected $ultimaLineaColectivo = ""; // string
    protected $puedeTransbordo = false; // bool
    /**
    * valorViaje corresponde al valor sin franquicia aplicada
    * Luego el metodo valorAPagar se encarga de aplicar las
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

    public function recargar(float $monto) {
      $montosValidos = array(
        10,
        20,
        30,
        50,
        100,
        510.15,
        962.59);
      if (in_array($monto, $montosValidos)) {
        $this->saldo += $monto;
        if ($monto == 510.15) {
          $this->saldo += 81.93;
        }
        if ($monto == 962.59) {
          $this->saldo += 221.58;
        }
        return true;
      }
      return false;
    }

    public function obtenerValorViaje() {
      return round($this->valorViaje, 2, PHP_ROUND_HALF_DOWN);
    }

    public function obtenerValorUltimoViaje() {
      return $this->valorUltimoViaje;
    }

    protected function valorAPagar(string $lineaColectivo) {
      $ahora = $this->tiempo->actual();
      $this->puedeTransbordo = ($ahora - $this->fechaUltimoViaje) <= $this->lapsoTransbordo && !$this->usoTransbordo && $lineaColectivo != $this->ultimaLineaColectivo;
      if ($this->puedeTransbordo) {
        return 33 / 100 * $this->valorViaje;
      }
      return $this->valorViaje;
    }

    public function pagar(string $lineaColectivo) {
      if ($this->saldo < 0) return false; // no se toleraran valores negativos

      $ahora = $this->tiempo->actual();
      $precioViaje = round($this->valorAPagar($lineaColectivo), 2, PHP_ROUND_HALF_DOWN);
      $precioPlusEnDeuda = round($this->plus * $this->valorViaje, 2, PHP_ROUND_HALF_DOWN);
      $precioTotal = $precioViaje + $precioPlusEnDeuda;
      $pagaPlus = $this->saldo < $precioTotal;

      if ($pagaPlus) {
        if ($this->plus >= 2) {
          return false; // como maximo podra adeudar 2 viajes plus
        }
        $this->plusDevueltos = 0;
        ++$this->plus;
        $this->usoPlus = true;
        $this->usoTransbordo = false;
      }
      else {
        $this->saldo -= $precioTotal;
        $this->plusDevueltos = $this->plus;
        $this->plus = 0;
        $this->usoPlus = false;
        $this->usoTransbordo = $this->puedeTransbordo;
      }

      $diaDeSemana = (int) date('N', $ahora); // N devuelve el dia de la semana correspondiendo 1 para lunes y 7 para domingo
      $horaDeDia = (int) date('G', $ahora); // G devuelve la hora del dia entre 0 y 23
      $rangoLunAVie6a22 = $diaDeSemana >= 1 && $diaDeSemana <= 5 && $horaDeDia >= 6 && $horaDeDia < 22;
      $rangoSab6a14 = $diaDeSemana == 6 && $horaDeDia >= 6 && $horaDeDia < 14;
      $noEsFeriado = !in_array(
        date('d-m', $ahora),
        array(
          '01-01', // Ano Nuevo
          '24-03', // Dia Nacional de la Memoria por la Verdad y la Justicia
          '02-04', // Dia del Veterano y de los Caidos en la Guerra de Malvinas
          '01-05', // Dia del trabajador
          '25-05', // Dia de la Revolucion de Mayo
          '17-06', // Dia Paso a la Inmortalidad del General Martin Miguel de Guemes
          '20-06', // Dia Paso a la Inmortalidad del General Manuel Belgrano
          '09-07', // Dia de la Independencia
          '17-08', // Paso a la Inmortalidad del Gral. Jose de San Martin
          '12-10', // Dia del Respeto a la Diversidad Cultural
          '20-11', // Dia de la Soberania Nacional
          '08-12', // Inmaculada Concepcion de Maria
          '25-12', // Navidad
        )
      );
      if ($noEsFeriado && ($rangoLunAVie6a22 || $rangoSab6a14)) {
        $this->lapsoTransbordo = 3600; // 60 min
      }
      else {
        $this->lapsoTransbordo = 5400; // 90 min
      }

      $this->valorUltimoViaje = $precioViaje;
      $this->usoFranquicia = false;
      $this->ultimaLineaColectivo = $lineaColectivo;
      $this->fechaUltimoViaje = $ahora;

      return true;
    }

    public function obtenerPlus() {
      return $this->plus;
    }

    public function obtenerUsoFranquicia() {
      return $this->usoFranquicia;
    }

    public function obtenerUsoPlus() {
      return $this->usoPlus;
    }

    public function obtenerUsoTransbordo() {
      return $this->usoTransbordo;
    }

    public function obtenerPlusDevueltos() {
      return $this->plusDevueltos;
    }

    public function obtenerFechaUltimoViaje() {
      return $this->fechaUltimoViaje;
    }

}
