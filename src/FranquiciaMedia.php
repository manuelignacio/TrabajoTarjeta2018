<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    protected $usosEnElDia = 0; // int
    protected $fechaUltimoViajeConFranquicia = 0; // int

    public function obtenerTipo() {
        return 'Medio';
    }

    protected function valorAPagar(string $lineaColectivo) {
        $valorAPagar = parent::valorAPagar($lineaColectivo);
        $ahora = $this->tiempo->actual();
        $mismoDia = ((int) date('d',$ahora) == (int) date('d',$this->fechaUltimoViajeConFranquicia));
        $diferenciaMenorA24hs = (($ahora - $this->fechaUltimoViajeConFranquicia) < (24 * 60 * 60));
        if ($mismoDia && $diferenciaMenorA24hs) {
            if ($this->usosEnElDia >= 2) return $valorAPagar;
        }
        else $this->usosEnElDia = 0;
        return $valorAPagar / 2;
    }

    public function pagar(string $lineaColectivo) {
        $precioViaje = $this->valorAPagar($lineaColectivo);
        $precioPlusEnDeuda = $this->plus * $this->valorViaje;
        $precioTotal = $precioViaje + $precioPlusEnDeuda;
        $pagaPlus = $this->saldo < $precioTotal;
        $ahora = $this->tiempo->actual();

        $aplicaFranquicia = !$pagaPlus && ($precioViaje < parent::valorAPagar($lineaColectivo));
        if ($aplicaFranquicia && ($ahora - $this->fechaUltimoViajeConFranquicia) < (5 * 60))
            return false; // no podran pasar menos de 5 min entre medio boleto y otro
        $paga = parent::pagar($lineaColectivo);
        if ($paga) {
            if ($aplicaFranquicia) {
                $this->usoFranquicia = true;
                $this->fechaUltimoViajeConFranquicia = $ahora;
            }
            if (!$pagaPlus) $this->usosEnElDia++;
        }
        return $paga;
    }

}
