<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta implements TarjetaInterface {

    public function obtenerTipo() {
        return "Franquicia Completa";
    }

    public function recargar($monto) {
        return false;
    }

    public function valorViaje() {
        return 0;
    }

    public function pagarEn(string $lineaColectivo) {
        $paga = parent::pagarEn($lineaColectivo);
        $this->usoTransbordo = false;
        return $paga;
    }

}
