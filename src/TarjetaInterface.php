<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Devuelve el tipo de franquicia a la que pertenece la tarjeta.
     * 
     * @return string
     */
    public function obtenerTipo();

    /**
     * Devuelve el ID de la tarjeta.
     * 
     * @return int
     */
    public function obtenerId();

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */
    public function obtenerSaldo();

    /**
     * Recarga una tarjeta con un cierto valor de dinero.
     *
     * @param float $monto
     *
     * @return bool
     * Devuelve TRUE si el monto a cargar es válido, o FALSE en caso de que no
     * sea valido.
     */
    public function recargar($monto);

    /**
     * Devuelve siempre el valor entero de un viaje normal sin franquicia
     * 
     * @return float
     */
    public function obtenerValorViaje();

    /**
     * Devuelve el valor del boleto a pagar
     * 
     * @return float
     */
    public function valorViaje();

    /**
     * Si el saldo es suficiente, lo resta por pagar un boleto del valor pedido
     * Si el saldo es insuficiente, suma un viaje plus de deuda, hasta el maximo permitido
     * Tiene en cuenta la linea y bandera en la que esta siendo usada la tarjeta.
     * 
     * @param string $lineaColectivo
     * 
     * @return bool
     */
    public function pagar(string $lineaColectivo);

    /**
     * Devuelve la cantidad de viajes plus almacenados en deuda.
     * 
     * @return int
     */
    public function obtenerPlus();
    
    /**
     * Devuelve true si el último viaje realizado fue hecho con plus.
     * 
     * @return bool
     */
    public function obtenerUsoPlus();

    /**
     * Devuelve la cantidad de viajes plus abonados (devueltos) en el último viaje efectuado.
     * Si en el último viaje se usó un viaje plus, considera que no se ha devuelto ningún plus.
     * 
     * @return int
     */
    public function obtenerPlusDevueltos();

    /**
     * Devuelve la fecha en la que se viajó por última vez.
     * 
     * @return int
     */
    public function obtenerFechaUltimoViaje();

    /**
     * Devuelve true si el último viaje realizado fue hecho con transbordo.
     * 
     * @return bool
     */
    public function obtenerUsoTransbordo();

}
