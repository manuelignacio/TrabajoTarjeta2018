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
     * Devuelve TRUE si el monto a cargar es válido,
     * o FALSE en caso contrario.
     *
     * @param float $monto
     *
     * @return bool
     */
    public function recargar(float $monto);

    /**
     * Devuelve siempre con 2 decimales el valor constante
     * que representa un viaje normal sin franquicia
     * aplicada.
     * 
     * @return float
     */
    public function obtenerValorViaje();

    /**
     * Devuelve el valor del ultimo viaje efectuado,
     * incluyendo transbordo y franquicias aplicadas,
     * pero exluyendo el concepto de viaje plus.
     * 
     * @return float
     */
    public function obtenerValorUltimoViaje();

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
     * Devuelve true si en el último viaje realizado se aplicó transbordo.
     */
    public function obtenerUsoFranquicia();
    
    /**
     * Devuelve true si el último viaje realizado fue hecho con plus.
     * 
     * @return bool
     */
    public function obtenerUsoPlus();

    /**
     * Devuelve true si el último viaje realizado fue hecho con transbordo.
     * 
     * @return bool
     */
    public function obtenerUsoTransbordo();

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

}
