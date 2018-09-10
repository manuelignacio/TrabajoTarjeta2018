<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface {

    protected $time;

    public function __construct($inicio) {
        $this->time = $inicio;
    }

    public function actual() {
        return $this->time;
    }

    public function avanzar($segundos) {
        $this->time += $segundos;
    }

}
