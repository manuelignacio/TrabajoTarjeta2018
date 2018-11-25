<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface {

    public function actual() : int {
        return time();
    }

}
