<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    protected $usosEnElDia = 0; // int
    protected $tiempo; // TiempoInterface
    protected $tiempoUltimoViajeConFranquicia = 0; // int

    public function __construct(TiempoInterface $tiempo) {
        $this->tiempo = $tiempo;
    }

    public function obtenerValorViaje() {
        $ahora = $this->tiempo->actual();
        $mismoDia = ((int)date("d",$ahora) == (int)date("d",$this->tiempoUltimoViajeConFranquicia));
        $diferenciaMenorA24hs = (($ahora - $this->tiempoUltimoViajeConFranquicia) < (24 * 60 * 60));
        if ($mismoDia && $diferenciaMenorA24hs) {
            if ($this->usosEnElDia >= 2) return $this->valorViaje;
        }
        else $this->usosEnElDia = 0;
        return ($this->valorViaje / 2);
    }

    public function pagar() {
        $valor = $this->obtenerValorViaje();
        $pagaPlus = ($valor > $this->obtenerSaldo());
        $aplicaFranquicia = !$pagaPlus && ($valor < parent::obtenerValorViaje());
        if ($aplicaFranquicia && ($this->tiempo->actual() - $this->tiempoUltimoViajeConFranquicia) < (5 * 60))
            return false; // no podran pasar menos de 5 min entre medio boleto y otro
        $paga = parent::pagar();
        if ($paga) {
            if ($aplicaFranquicia) $this->tiempoUltimoViajeConFranquicia = $this->tiempo->actual();
            if (!$pagaPlus) $this->usosEnElDia++;
        }
        return $paga;
    }

}
