<?php

namespace TrabajoTarjeta;

interface TiempoInterface {

    /**
     * Devuelve el tiempo actual en segundos sin
     * formateo de fecha.
     * En general se interpretará como la cantidad
     * de segundos transcurridos desde el 1 de enero
     * de 1970 a la 01:00:00
     * 
     * @return int
     *   El tiempo actual en segundos.
     */
    public function actual() : int;

}
