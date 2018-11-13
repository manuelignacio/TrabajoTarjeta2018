<?php

namespace TrabajoTarjeta;

interface BoletoInterface {

    /**
     * Devuelve el valor del boleto.
     *
     * @return float
     */
    public function obtenerValor();

    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */
    public function obtenerColectivo();

    /**
     * Devuelve un objeto que respresenta la tarjeta con la que se abonó.
     *
     * @return TarjetaInterface
     */
    public function obtenerTarjeta();

    /**
     * Devuelve la fecha en la que se emitió el boleto en formato d/m/Y H:i:s
     * 
     * @return int
     */
    public function obtenerFecha();

    /**
     * Devuelve el tipo de la tarjeta con la que se abonó.
     * 
     * @return string
     */
    public function obtenerTarjetaTipo();

    /**
     * Devuelve el ID de la tarjeta con la que se abonó.
     * 
     * @return int
     */
    public function obtenerTarjetaID();

    /**
     * Devuelve el saldo de la tarjeta con la que se abonó.
     * 
     * @return float
     */
    public function obtenerTarjetaSaldo();

    /**
     * Devuelve el total abonado en el viaje.
     * 
     * @return float
     */
    public function obtenerAbonado();

    /**
     * Devuelve el tipo de boleto emitido, pudiendo ser actualmente:
     * - Normal
     * - Normal Transbordo
     * - Medio
     * - Medio Transbordo
     * - Viaje Plus
     * - Franquicia Completa
     * 
     * @return string
     */
    public function obtenerTipo();

    /**
     * Devuelve la descripción a mostrar en el boleto.
     * 
     * @return string
     */
    public function obtenerDescripcion();

}
