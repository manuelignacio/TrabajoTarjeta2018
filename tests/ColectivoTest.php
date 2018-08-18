<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    public function testPagoConSaldo() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(20);
        $boleto = $colectivo->pagarCon($tarjeta);
        $boletoEsperado = new Boleto(14.8,$colectivo,$tarjeta); // el boleto ejemplar que se espera obtener

        //$this->assertEquals($tarjeta->obtenerSaldo(),5.2); aun no implementado, comprueba que haya disminuido el saldo correctamente
        $this->assertEquals($boleto,$boletoEsperado);
    }

    public function testPagoSinSaldo() {
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(10);

        $this->assertFalse($colectivo->pagarCon($tarjeta));
    }

}
