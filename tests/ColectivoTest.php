<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {

    /**
     * Prueba que el constructor y los metodos de obtencion en un colectivo funcionen correctamente
     */
    public function testConstructor() {
        $linea = "133N";
        $empresa = "Semtur";
        $numero = 250;
        $colectivo = new Colectivo("133N","Semtur",250);
        
        $this->assertEquals($colectivo->linea(), $linea);
        $this->assertEquals($colectivo->empresa(), $empresa);
        $this->assertEquals($colectivo->numero(), $numero);
    }

    /**
     * Prueba que al viajar se reste saldo en la tarjeta y se emita un boleto correctamente
     */
    public function testPagoConSaldo() {
        $colectivo = new Colectivo("102N","Semtur",23);
        $tarjeta = new Tarjeta(1, new Tiempo);
        $tarjeta->recargar(20);
        $boleto = $colectivo->pagarCon($tarjeta);
        $boletoEsperado = new Boleto($colectivo, $tarjeta); // el boleto ejemplar que se espera obtener

        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo se resta correctamente
        $this->assertEquals($boleto,$boletoEsperado); // el boleto emitido es correcto
    }

    /**
     * Prueba que si una tarjeta no puede pagar, no se emitira boleto al querer viajar
     */
    public function testPagoSinSaldo() {
        $colectivo = new Colectivo("102R","Semtur",120);
        $tarjeta = new Tarjeta(1, new Tiempo);
        $tarjeta->recargar(10);

        $this->assertNotFalse($colectivo->pagarCon($tarjeta)); // no se usa assertTrue ya que lo que devuelve es un Boleto
        $this->assertNotFalse($colectivo->pagarCon($tarjeta)); // puede viajar 2 veces con los viajes plus
        $this->assertFalse($colectivo->pagarCon($tarjeta)); // imposible viajar por saldo insuficiente
    }

}
