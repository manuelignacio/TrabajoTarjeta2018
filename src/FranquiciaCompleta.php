<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta implements TarjetaInterface {

    public function obtenerTipo() {
        return "Franquicia Completa";
    }

    public function recargar(float $monto) {
        return false;
    }

    protected function valorAPagar(string $lineaColectivo) {
        $this->puedeTransbordo = false;
        return 0;
    }

    public function pagar(string $lineaColectivo) {
        $ahora = $this->tiempo->actual();
        $precioViaje = $this->valorAPagar($lineaColectivo);
        $this->saldo = 0;
        $this->plusDevueltos = 0;
        $this->plus = 0;
        $this->usoPlus = false;
        $this->usoTransbordo = $this->puedeTransbordo;
        $this->lapsoTransbordo = 0;
        $this->valorUltimoViaje = $precioViaje;
        $this->usoFranquicia = true;
        $this->ultimaLineaColectivo = $lineaColectivo;
        $this->fechaUltimoViaje = $ahora;
        return true;
    }

}
