<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    public function testPagoConSaldo() {
        $colectivo = new Colectivo("102N","Semtur",23);
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(20);
        $boleto = $colectivo->pagarCon($tarjeta);
        $boletoEsperado = new Boleto(14.8,$colectivo,$tarjeta); // el boleto ejemplar que se espera obtener

        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo se resta correctamente
        $this->assertEquals($boleto,$boletoEsperado); // el boleto emitido es correcto
    }

    public function testPagoSinSaldo() {
        $colectivo = new Colectivo("102R","Semtur",120);
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(10);

        $this->assertNotFalse($colectivo->pagarCon($tarjeta)); // no se usa assertTrue ya que lo que devuelve es un Boleto
        $this->assertNotFalse($colectivo->pagarCon($tarjeta)); // puede viajar 2 veces con los viajes plus
        $this->assertFalse($colectivo->pagarCon($tarjeta)); // imposible viajar por saldo insuficiente
    }

}
