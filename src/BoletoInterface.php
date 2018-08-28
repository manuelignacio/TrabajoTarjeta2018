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

}
