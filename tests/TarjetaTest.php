<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga cualquiera de los montos validos.
     */
    public function testCargaMontoValido() {
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(10));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);

        $this->assertTrue($tarjeta->recargar(20));
        $this->assertEquals($tarjeta->obtenerSaldo(), 30);

        $this->assertTrue($tarjeta->recargar(30));
        $this->assertEquals($tarjeta->obtenerSaldo(), 60);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->obtenerSaldo(), 110);

        $this->assertTrue($tarjeta->recargar(100));
        $this->assertEquals($tarjeta->obtenerSaldo(), 210);

        $this->assertTrue($tarjeta->recargar(510.15));
        $this->assertEquals($tarjeta->obtenerSaldo(), 802.08); // se suma el beneficio tambien

        $this->assertTrue($tarjeta->recargar(962.59));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1986.25); // se suma el beneficio tambien
    }

    /**
     * Comprueba que la tarjeta no puede cargar montos invalidos.
     */
    public function testCargaMontoInvalido() {
      $tarjeta = new Tarjeta;

      $this->assertFalse($tarjeta->recargar(0));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);

      $this->assertFalse($tarjeta->recargar(15));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);

      $this->assertFalse($tarjeta->recargar(-10));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba el correcto funcionamiento de los metodos usados para viajar en una tarjeta sin franquicia
     * - Prueba que el saldo se reste al pagar un viaje
     * - Ademas prueba el correcto funcionamiento del concepto 'viaje plus' al pagar y recargar
     * El metodo recargar se efectúa sin ser testeado para no redundar con los tests anteriores.
     * El valor de un viaje sin franquicia se consideran de 14.8
     */
    public function testViajesSinFranquicia() {
        $tarjeta = new Tarjeta;

        $tarjeta->recargar(20); // saldo inicial: 20
        $this->assertTrue($tarjeta->pagar());
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo fue restado

        $this->assertTrue($tarjeta->pagar()); // se adeuda un viaje plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // pero el saldo no varia
        $tarjeta->recargar(10); // se recargan 10
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // pero se comprueba que la deuda de 1 plus fue saldada en la recarga

        $this->assertTrue($tarjeta->pagar()); // como el saldo es positivo sin plus pendientes se puede volver a adeudar
        $this->assertTrue($tarjeta->pagar()); // ahora se adeudan 2 viajes plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // sin variar el saldo
        $this->assertFalse($tarjeta->pagar()); // y como ya no se puede adeudar mas, no puede pagar otro viaje, aunque el saldo sea positivo

        $tarjeta->recargar(10); // si se recarga poco con tanta deuda
        $this->assertEquals($tarjeta->obtenerSaldo(), -19.2); // el saldo quedara negativo porque se saldan los plus
        $this->assertFalse($tarjeta->pagar()); // pero no se podra viajar hasta saldar la deuda

        $tarjeta->recargar(50); // se salda la deuda
        $this->assertTrue($tarjeta->pagar()); // y se puede volver a viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 16); // saldo final: 16
    }

    /**
     * Comprueba los mismos comportamientos del test anterior, pero en una tarjeta con franquicia media.
     * El valor de un viaje medio boleto se considera de 14.8/2 = 7.4
     */
    public function testViajesFranquiciaMedia() {
        $tarjeta = new FranquiciaMedia;

        $tarjeta->recargar(10); // saldo inicial: 10
        $this->assertTrue($tarjeta->pagar());
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // el saldo fue restado con franquicia media

        $this->assertTrue($tarjeta->pagar()); // se adeuda un viaje plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // pero el saldo no varia
        $tarjeta->recargar(20); // se recargan 20
        $this->assertEquals($tarjeta->obtenerSaldo(), 7.8); // pero se comprueba que la deuda de 1 plus fue saldada en la recarga al valor sin franquicia

        $tarjeta->pagar(); // con esto dejamos el saldo a 0.4
        $this->assertTrue($tarjeta->pagar()); // como el saldo es positivo sin plus pendientes se puede volver a adeudar
        $this->assertTrue($tarjeta->pagar()); // ahora se adeudan 2 viajes plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // sin variar el saldo
        $this->assertFalse($tarjeta->pagar()); // y como ya no se puede adeudar mas, no puede pagar otro viaje, aunque el saldo sea positivo

        $tarjeta->recargar(10); // si se recarga poco con tanta deuda
        $this->assertEquals($tarjeta->obtenerSaldo(), -19.2); // el saldo quedara negativo porque se saldan los plus
        $this->assertFalse($tarjeta->pagar()); // pero no se podra viajar hasta saldar la deuda

        $tarjeta->recargar(50); // se salda la deuda
        $this->assertTrue($tarjeta->pagar()); // y se puede volver a viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 23.4); // saldo final: 23.4
    }

    /**
     * Comprueba el correcto funcionamiento de una tarjeta con franquicia completa.
     * Esta tarjeta siempre debe poder viajar.
     */
    public function testViajesFranquiciaCompleta() {
        $tarjeta = new FranquiciaCompleta;

        $this->assertTrue($tarjeta->pagar()); // puede viajar desde el comienzo
        $this->assertTrue($tarjeta->pagar()); // tantas veces como quiera
        $this->assertTrue($tarjeta->pagar());
        $this->assertTrue($tarjeta->pagar());

        $tarjeta->recargar(10); // se prueba a hacer una recarga
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);
        $this->assertTrue($tarjeta->pagar()); // pero aun asi podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 10); // sin descontarle el saldo

        $tarjeta->recargar(510.15); // y aunque el monto sea muy alto
        $this->assertEquals($tarjeta->obtenerSaldo(), 602.08); // y reciba bonificacion
        $this->assertTrue($tarjeta->pagar()); // podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 602.08); // sin descontarle el saldo
    }

}
