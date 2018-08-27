<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Recarga una tarjeta con un cierto valor de dinero.
     *
     * @param float $monto
     *
     * @return bool
     *   Devuelve TRUE si el monto a cargar es válido, o FALSE en caso de que no
     *   sea valido.
     */
    public function recargar($monto);

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */
    public function obtenerSaldo();

    /**
     * Si el saldo es suficiente, lo resta por pagar un boleto del valor pedido
     * 
     * @param float $valor
     * 
     * @return bool
     * Devuelve TRUE si el saldo es suficiente, o FALSE en caso contrario
     */
    public function pagar($valor);

}
