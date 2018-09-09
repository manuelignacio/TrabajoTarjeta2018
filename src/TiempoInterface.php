<?php

namespace TrabajoTarjeta;

interface TiempoInterface {

    /**
     * Devuelve el tiempo actual sin formateo de fecha.
     * 
     * @return int
     */
    public function actual();

}
