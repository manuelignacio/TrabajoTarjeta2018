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
        $this->assertFalse($tarjeta->abonoPlus()); // sin usar plus

        $this->assertTrue($tarjeta->pagar()); // se adeuda un viaje plus
        $this->assertTrue($tarjeta->abonoPlus()); // y se comprueba
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // pero el saldo no varia
        $tarjeta->recargar(10); // se recargan 10
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // pero se comprueba que la deuda de 1 plus fue saldada en la recarga

        $this->assertTrue($tarjeta->pagar()); // como el saldo es positivo sin plus pendientes se puede volver a adeudar
        $this->assertTrue($tarjeta->abonoPlus()); // y se comprueba
        $this->assertTrue($tarjeta->pagar()); // ahora se adeudan 2 viajes plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4); // sin variar el saldo
        $this->assertFalse($tarjeta->pagar()); // y como ya no se puede adeudar mas, no puede pagar otro viaje, aunque el saldo sea positivo
        $this->assertTrue($tarjeta->abonoPlus()); // pero se puede obtener que en el ultimo viaje efectuado abono plus

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
        $tiempoReal = new Tiempo; // testeamos con el tiempo real
        $tarjetaReal = new FranquiciaMedia($tiempoReal);

        $tarjetaReal->recargar(50); // saldo inicial: 50
        $this->assertTrue($tarjetaReal->pagar());
        $this->assertEquals($tarjetaReal->obtenerSaldo(), 42.6); // el viaje se cobra a mitad de precio
        $this->assertFalse($tarjetaReal->pagar()); // se intenta pagar otro pasaje
        $this->assertEquals($tarjetaReal->obtenerSaldo(), 42.6); // pero no pasaron 5 minutos del medio boleto anterior y no se viaja
        $this->assertFalse($tarjetaReal->pagar()); // por muchas veces que intentemos
        $this->assertFalse($tarjetaReal->pagar()); // porque el tiempo no pasa

        $tiempo = new TiempoFalso(time()); // creamos un tiempo manipulable para testear
        $tarjeta = new FranquiciaMedia($tiempo);

        $tarjeta->recargar(10); // saldo inicial: 10
        $this->assertTrue($tarjeta->pagar());
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // el saldo fue restado con franquicia media

        $this->assertTrue($tarjeta->pagar()); // se adeuda un viaje plus y no deberia contar como un uso de la franquicia en el dia
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // y el saldo no varia
        $tarjeta->recargar(50); // se recargan 50
        $this->assertEquals($tarjeta->obtenerSaldo(), 37.8); // y se comprueba que la deuda de 1 plus fue saldada en la recarga al valor sin franquicia

        $tiempo->avanzar(5 * 60); // hacemos avanzar el reloj 5 minutos para disponer de otro medio boleto
        $this->assertTrue($tarjeta->pagar()); // usamos el segundo y ultimo medio boleto del dia
        $this->assertEquals($tarjeta->obtenerSaldo(), 30.4); // y dejamos el saldo a 30.4
        $tiempo->avanzar(2 * 60); // ahora avanzamos 2 minutos
        $this->assertTrue($tarjeta->pagar()); // al no usar la franquicia, se puede viajar sin haber pasado 5 minutos
        $this->assertEquals($tarjeta->obtenerSaldo(), 15.6); // se tuvo que pagar sin franquicia en el 3er viaje
        $tarjeta->pagar();
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.8); // y a partir del 3er viaje

        $tarjeta->pagar();
        $tarjeta->pagar(); // se debieron usar 2 plus

        $tarjeta->recargar(10); // y si se recarga poco con tanta deuda
        $this->assertEquals($tarjeta->obtenerSaldo(), -18.8); // el saldo quedara negativo porque se saldan los plus
        $this->assertFalse($tarjeta->pagar()); // pero no se podra viajar hasta saldar la deuda

        $tarjeta->recargar(50); // se salda la deuda
        $tarjeta->pagar(); // y se puede volver a viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 16.4); // usando el precio sin franquicia

        $tiempo->avanzar(86400); // se avanza 1 dia exacto desde el ultimo viaje
        $tarjeta->pagar();
        $this->assertEquals($tarjeta->obtenerSaldo(), 9); // y ahora puede usar medio boleto otra vez
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
        $this->assertFalse($tarjeta->abonoPlus()); // y en ningun momento se abonaron plus

        $this->assertFalse($tarjeta->recargar(12)); // se intenta recargar saldo invalido
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // pero no se efectua
        $this->assertTrue($tarjeta->pagar()); // y aun asi podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // sin variar el saldo

        $this->assertFalse($tarjeta->recargar(510.15)); // y aunque la recarga sea valida, devuelve false
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // no variara su saldo
        $this->assertTrue($tarjeta->pagar()); // y podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // sin variar el saldo
    }

}
