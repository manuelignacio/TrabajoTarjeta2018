<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface {

    protected $time = 0;

    public function actual() {
        return $this->time;
    }

    public function avanzar($segundos) {
        $this->time += $segundos;
    }

}
