<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga cualquiera de los montos validos.
     */
    public function testCargaSaldo() {
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
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido() {
      $tarjeta = new Tarjeta;

      $this->assertFalse($tarjeta->recargar(15));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que el saldo reste al pagar un viaje.
     * Ademas prueba el correcto funcionamiento del concepto 'viaje plus' al pagar y recargar
     */
    public function testViajes() {
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(20)); // saldo inicial: 20
        $this->assertTrue($tarjeta->pagar());
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo fue restado

        $this->assertTrue($tarjeta->pagar()); // se adeuda un viaje plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // pero el saldo no varia
        $this->assertTrue($tarjeta->recargar(10)); // se recargan 10
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // pero se comprueba que la deuda de 1 plus fue saldada en la recarga

        $this->assertTrue($tarjeta->pagar()); // como el saldo es positivo sin plus pendientes se puede volver a adeudar
        $this->assertTrue($tarjeta->pagar()); // ahora se adeudan 2 viajes plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // sin variar el saldo
        $this->assertFalse($tarjeta->pagar()); // y como ya no se puede adeudar mas, no puede pagar otro viaje, aunque el saldo sea positivo

        $this->assertTrue($tarjeta->recargar(10)); // si se recarga poco con tanta deuda
        $this->assertEquals($tarjeta->obtenerSaldo(), -19.2); // el saldo quedara negativo porque se saldan los plus
        $this->assertFalse($tarjeta->pagar()); // pero no se podra viajar hasta saldar la deuda

        $this->assertTrue($tarjeta->recargar(50)); // se salda la deuda
        $this->assertTrue($tarjeta->pagar()); // y se puede volver a viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 16); // saldo final: 16
    }

}
