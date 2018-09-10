<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    protected $usosEnElDia = 0; // int
    protected $tiempo; // TiempoInterface
    protected $tiempoUltimoViajeMedio = 0; // int

    public function __construct(TiempoInterface $tiempo) {
        $this->tiempo = $tiempo;
    }

    public function obtenerValorViaje() {
        $ahora = $this->tiempo->actual();
        $mismoDia = ((int)date("d",$ahora) == (int)date("d",$this->tiempoUltimoViajeMedio));
        $diferenciaMenorAUnDia = (($ahora - $this->tiempoUltimoViajeMedio) < 86400);
        if ($mismoDia && $diferenciaMenorAUnDia) {
            if ($this->usosEnElDia >= 2) return $this->valorViaje;
        }
        else $this->usosEnElDia = 0;
        return ($this->valorViaje / 2);
    }

    public function pagar() {
        $paga = parent::pagar();
        $pagaConFranquicia = ($paga && !$this->abonoPlus() && ($this->obtenerValorViaje() < parent::obtenerValorViaje()));
        if ($pagaConFranquicia) $this->tiempoUltimoViajeMedio = $this->tiempo->actual();
        if ($paga && !$this->abonoPlus()) $this->usosEnElDia++;
        return $paga;
    }

}
