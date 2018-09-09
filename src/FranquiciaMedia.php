<?php

namespace TrabajoTarjeta;

class FranquiciaMedia extends Tarjeta implements TarjetaInterface {

    protected $franquiciaEnElDia = 0; // int
    protected $tiempo; // TiempoInterface
    protected $tiempoUltimoViaje = 0; // int

    public function __construct(TiempoInterface $tiempo) {
        $this->tiempo = $tiempo;
    }

    protected function estaEnDiaUltimoViaje() {
        $ahora = $this->tiempo->actual();
        $diaAhora = (int)date("d",$ahora);
        $diaUltimoViaje = (int)date("d",$this->tiempoUltimoViaje);
        $diferencia = $ahora - $this->tiempoUltimoViaje;
        return ($diferencia < 86400 && $diaAhora == $diaUltimoViaje);
    }

    public function obtenerValorViaje() {
        if (!$this->estaEnDiaUltimoViaje()) $this->franquiciaEnElDia = 0;
        else if ($this->franquiciaEnElDia >= 2) return $this->valorViaje; // solo se pueden 2 medio boletos por dia
        $this->franquiciaEnElDia++;
        return ($this->valorViaje / 2); // precio de medio boleto
    }

}
