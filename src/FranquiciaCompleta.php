<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta implements TarjetaInterface {

    public function obtenerTipo() {
        return "Franquicia Completa";
    }

    public function recargar($monto) {
        return false;
    }

    public function valorViaje(string $lineaColectivo) {
        return 0;
    }

    public function pagar(string $lineaColectivo) {
        $paga = parent::pagar($lineaColectivo);
        $this->usoTransbordo = false;
        return $paga;
    }

}
