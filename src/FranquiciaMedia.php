<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    public function obtenerValorViaje() {
        return ($this->valorViaje / 2);
    }

}
