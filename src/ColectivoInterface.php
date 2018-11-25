<?php

namespace TrabajoTarjeta;

interface ColectivoInterface {

  /**
   * Devuelve el nombre de linea y bandera del colectivo.
   * Ejemplo '142 Negro'
   *
   * @return string
   *   La linea y bandera del colectivo.
   */
  public function linea() : string;

  /**
   * Devuelve el nombre de la empresa del colectivo.
   * Ejemplo 'Semtur'
   *
   * @return string
   *   El nombre de la empresa que representa al colectivo.
   */
  public function empresa() : string;

  /**
   * Devuelve el número de unidad, con el cual el colectivo
   * se identifica.
   * Ejemplo: 12
   *
   * @return int
   *   El número de unidad.
   */
  public function numero() : int;

  /**
   * Con una tarjeta particular, si ésta se lo permite efectúa el pago
   * de un viaje y genera un boleto correspondiente; si no devuelve
   * false.
   *
   * @param TarjetaInterface $tarjeta
   *   La tarjeta con la que se intentará viajar.
   *
   * @return BoletoInterface|false
   *   El boleto generado al efectuarse el viaje,
   *   o false si no hay saldo suficiente en la tarjeta.
   */
  public function pagarCon(TarjetaInterface $tarjeta);

}
