<?php

namespace TrabajoTarjeta;

interface BoletoInterface {

  /**
   * Devuelve el valor de cuánto costó el viaje sin considerar si se
   * abonaron o gastaron viajes plus.
   *
   * @return float
   *   El valor del boleto.
   */
  public function obtenerValor() : float;

  /**
   * Devuelve un objeto que es el colectivo donde se viajó.
   *
   * @return ColectivoInterface
   *   El colectivo donde se viajó.
   */
  public function obtenerColectivo() : ColectivoInterface;

  /**
   * Devuelve un objeto que es la tarjeta con la que se abonó.
   *
   * @return TarjetaInterface
   *   La tarjeta con la que se abonó el viaje.
   */
  public function obtenerTarjeta() : TarjetaInterface;

  /**
   * Devuelve la fecha en la que se emitió el boleto en formato d/m/Y H:i:s
   * 
   * @return string
   *   La fecha en la que se emitió el boleto.
   */
  public function obtenerFecha() : string;

  /**
   * Devuelve el tipo de la tarjeta con la que se abonó, en el momento de
   * emisión del boleto.
   * 
   * @return string
   *   El tipo de la tarjeta con la que se abonó.
   */
  public function obtenerTarjetaTipo() : string;

  /**
   * Devuelve el ID de la tarjeta con la que se abonó, en el momento de
   * emisión del boleto.
   * 
   * @return int
   */
  public function obtenerTarjetaID() : int;

  /**
   * Devuelve el saldo de la tarjeta con la que se abonó, en el momento de
   * emisión del boleto.
   * 
   * @return float
   */
  public function obtenerTarjetaSaldo() : float;

  /**
   * Devuelve el valor real y neto de cuánto costó el viaje, es decir,
   * considerando viajes plus, transbordo y franquicias.
   * 
   * @return float
   *   El total abonado por la tarjeta usada en el viaje.
   */
  public function obtenerAbonado() : float;

  /**
   * Devuelve el tipo de boleto emitido.
   * 
   * @return string
   *   El tipo del boleto emitido.
   */
  public function obtenerTipo() : string;

  /**
   * Devuelve la descripción a mostrar o imprimir del boleto.
   * 
   * @return string
   *   La descripción del boleto emitido.
   */
  public function obtenerDescripcion() : string;

}
