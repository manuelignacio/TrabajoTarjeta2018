<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Devuelve el tipo de franquicia a la que pertenece la tarjeta.
     * 
     * @return string
     */
    public function tipo();

    /**
     * Devuelve siempre el valor entero de un viaje normal sin franquicia
     * 
     * @return float
     */
    public function valor();

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
     * Devuelve el valor del boleto a pagar
     * 
     * @return float
     */
    public function obtenerValorViaje();

    /**
     * Si el saldo es suficiente, lo resta por pagar un boleto del valor pedido
     * Si el saldo es insuficiente, suma un viaje plus de deuda, hasta el maximo permitido
     * 
     * @return bool
     * Devuelve TRUE si puede pagar (si el saldo es suficiente o se abona con plus), o FALSE en caso contrario
     * Devuelve TRUE si el saldo es suficiente, o FALSE en caso contrario
     */
    public function pagar();

    /**
     * Devuelve la fecha en la que se viajó por última vez.
     * 
     * @return int
     */
    public function obtenerFechaUltimoViaje();

    /**
     * Devuelve la cantidad de viajes plus abonados (devueltos) en el último viaje efectuado.
     * Si en el último viaje se usó un viaje plus, no se ha devuelto ningún plus.
     * 
     * @return int
     */
    public function plusDevueltos();
    
    /**
     * Devuelve true si el último viaje realizado fue hecho con plus.
     * 
     * @return bool
     */
    public function usoPlus();

}
