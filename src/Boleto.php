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
  public function __construct(ColectivoInterface $colectivo, TarjetaInterface $tarjeta) {
    $this->valor = $tarjeta->obtenerValorUltimoViaje();
    $this->colectivo = $colectivo;
    $this->tarjeta = $tarjeta;
    $this->fecha = date('d/m/Y H:i:s', $tarjeta->obtenerFechaUltimoViaje());
    $this->tarjetaTipo = $tarjeta->obtenerTipo();
    $this->tarjetaID = $tarjeta->obtenerId();
    $this->tarjetaSaldo = $tarjeta->obtenerSaldo();
    $usoPlus = $tarjeta->obtenerUsoPlus();
    $plusDevueltos = $tarjeta->obtenerPlusDevueltos();
    $plusAbonados = $plusDevueltos * $tarjeta->obtenerValorViaje();
    $this->abonado = (int) (!$usoPlus) * ($this->valor + $plusAbonados);
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
      $this->descripcion .= "Abona {$plusDevueltos} Viaje";
      $this->descripcion .= ($plusDevueltos != 1) ? "s " : " ";
      $this->descripcion .= "Plus \${$plusAbonados} y\n";
    }
    $this->descripcion .= "{$this->tipo} ";
    if ($usoPlus) {
      $this->descripcion .= "{$tarjeta->obtenerPlus()} \$0.00\n";
    }
    else {
      $this->descripcion .= "\${$this->valor}\nTotal abonado: \${$this->abonado}\n";
    }
    $this->descripcion .= "Saldo(S.E.U.O): \${$this->tarjetaSaldo}\nTarjeta: {$this->tarjetaID}";
  }

  public function obtenerValor() : float {
    return $this->valor;
  }

  public function obtenerColectivo() : ColectivoInterface {
    return $this->colectivo;
  }

  public function obtenerTarjeta() : TarjetaInterface {
    return $this->tarjeta;
  }

  public function obtenerFecha() : string {
    return $this->fecha;
  }

  public function obtenerTarjetaTipo() : string {
    return $this->tarjetaTipo;
  }

  public function obtenerTarjetaID() : int {
    return $this->tarjetaID;
  }

  public function obtenerTarjetaSaldo() : float {
    return $this->tarjetaSaldo;
  }

  public function obtenerAbonado() : float {
    return $this->abonado;
  }

  /**
   * Actualmente los tipos de boleto posibles son:
   * - Normal
   * - Normal Transbordo
   * - Medio
   * - Medio Transbordo
   * - Viaje Plus
   * - Franquicia Completa
   */
  public function obtenerTipo() : string {
    return $this->tipo;
  }

  public function obtenerDescripcion() : string {
    return $this->descripcion;
  }

}
