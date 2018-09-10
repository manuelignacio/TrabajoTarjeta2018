<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface {

    public function actual() {
        return time();
    }

}
