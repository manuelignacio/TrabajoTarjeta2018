<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    protected $usosEnElDia = 0; // int
    protected $fechaUltimoViajeConFranquicia = 0; // int

    public function obtenerTipo() {
        return "Medio Boleto";
    }

    public function obtenerValorViaje() {
        $ahora = $this->tiempo->actual();
        $mismoDia = ((int)date("d",$ahora) == (int)date("d",$this->fechaUltimoViajeConFranquicia));
        $diferenciaMenorA24hs = (($ahora - $this->fechaUltimoViajeConFranquicia) < (24 * 60 * 60));
        if ($mismoDia && $diferenciaMenorA24hs) {
            if ($this->usosEnElDia >= 2) return parent::obtenerValorViaje();
        }
        else $this->usosEnElDia = 0;
        return (parent::obtenerValorViaje() / 2);
    }

    public function pagar() {
        $precioViaje = $this->obtenerValorViaje();
        $precioPlusEnDeuda = $this->plus * $this->valorViaje;
        $precioTotal = $precioViaje + $precioPlusEnDeuda;
        $pagaPlus = $this->saldo < $precioTotal;

        $aplicaFranquicia = !$pagaPlus && ($precioViaje < parent::obtenerValorViaje());
        if ($aplicaFranquicia && ($this->tiempo->actual() - $this->fechaUltimoViajeConFranquicia) < (5 * 60))
            return false; // no podran pasar menos de 5 min entre medio boleto y otro
        $paga = parent::pagar();
        if ($paga) {
            if ($aplicaFranquicia) $this->fechaUltimoViajeConFranquicia = $this->tiempo->actual();
            if (!$pagaPlus) $this->usosEnElDia++;
        }
        return $paga;
    }

}
