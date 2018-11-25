<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface {

    protected $time; // int

    /**
     * Inicializa el tiempo actual de acuerdo al valor indicado.
     *
     * @param int $inicio = 0
     *   El valor de inicio para el tiempo actual.
     *   Por defecto es 0.
     */
    public function __construct(int $inicio = 0) {
        $this->time = $inicio;
    }

    public function actual() : int {
        return $this->time;
    }

    /**
     * Suma el tiempo actual con la cantidad de segundos indicada.
     *
     * @param int $segundos
     *   La cantidad de segundos a sumar al tiempo actual.
     */
    public function avanzar(int $segundos) {
        $this->time += $segundos;
    }

}
