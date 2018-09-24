<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    /**
     * Comprueba que el constructor y los metodos de obtener en Boleto funcionen correctamente
     */
    public function testObtenerDatos() {
        $valor = 14.80;
        $colectivo = new Colectivo("102R","Semtur",120);
        $tarjeta = new Tarjeta(1, new Tiempo);
        $boleto = new Boleto($valor, $colectivo, $tarjeta);

        $this->assertEquals($boleto->obtenerValor(), $valor);
        $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
        $this->assertEquals($boleto->obtenerTarjeta(), $tarjeta);
    }
}
