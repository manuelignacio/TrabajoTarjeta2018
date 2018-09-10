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
        $diferenciaMenorA24hs = (($ahora - $this->tiempoUltimoViajeMedio) < (24 * 60 * 60));
        if ($mismoDia && $diferenciaMenorA24hs) {
            if ($this->usosEnElDia >= 2) return $this->valorViaje;
        }
        else $this->usosEnElDia = 0;
        return ($this->valorViaje / 2);
    }

    public function pagar() {
        $valor = $this->obtenerValorViaje();
        $pagaPlus = ($valor > $this->obtenerSaldo());
        $pagaMedio = !$pagaPlus && ($this->obtenerValorViaje() < parent::obtenerValorViaje());
        if ($pagaMedio && ($this->tiempo->actual() - $this->tiempoUltimoViajeMedio) < (5 * 60))
            return false; // no podran pasar menos de 5 min entre medio boleto y otro
        $paga = parent::pagar();
        $usaFranquicia = ($paga && $pagaMedio);
        if ($usaFranquicia) $this->tiempoUltimoViajeMedio = $this->tiempo->actual();
        if ($paga && !$pagaPlus) $this->usosEnElDia++;
        return $paga;
    }

}
