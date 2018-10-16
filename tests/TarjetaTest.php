<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga cualquiera de los montos validos.
     */
    public function testCargaMontoValido() {
        $tarjeta = new Tarjeta(1, new Tiempo);

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
        $tarjeta = new Tarjeta(1, new Tiempo);

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
        $tarjeta = new Tarjeta(1, new Tiempo);
        $colectivo1 = new Colectivo("102R","Semtur",120);
        $colectivo2 = new Colectivo("102N","Semtur",119);

        $tarjeta->recargar(20); // saldo inicial: 20
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo fue restado
        $this->assertFalse($tarjeta->obtenerUsoPlus()); // sin usarse plus

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // se adeuda un viaje plus
        $this->assertTrue($tarjeta->obtenerUsoPlus());
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // pero el saldo no varia

        $tarjeta->recargar(20); // se recargan 20
        $this->assertEquals($tarjeta->obtenerSaldo(), 25.2); // y en principio el plus queda pendiente
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // de devuelve el plus y se viaja con transbordo
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 1); // verifica que se devolvio el plus...
        $this->assertFalse($tarjeta->obtenerUsoPlus()); // ...que no uso otro plus...
        $this->assertTrue($tarjeta->obtenerUsoTransbordo()); // ...y que se uso un tranbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.52);

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // como el saldo no es suficiente para viajar y devolver el plus, se adeuda otro plus
        $this->assertTrue($tarjeta->obtenerUsoPlus()); // verifica que uso plus
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y adeuda otro
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.52); // y el saldo sigue igual

        $tarjeta->recargar(50); // se recarga suficiente
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y con este viaje se pagan todos los plus
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 2); // podemos comprobar que se devolvieron 2 plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 15.916); // saldo final: 15.916
    }

    /**
     * Comprueba los mismos comportamientos del test anterior, pero en una tarjeta con franquicia media.
     * El valor de un viaje medio boleto se considera de 14.8/2 = 7.4
     */
    public function testViajesFranquiciaMedia() {
        $tiempoReal = new Tiempo; // testeamos con el tiempo real
        $tarjetaReal = new FranquiciaMedia(1, $tiempoReal);

        $tarjetaReal->recargar(50); // saldo inicial: 50
        $this->assertTrue($tarjetaReal->pagar($colectivo1->linea()));
        $this->assertEquals($tarjetaReal->obtenerSaldo(), 42.6); // el viaje se cobra a mitad de precio
        $this->assertFalse($tarjetaReal->pagar($colectivo1->linea())); // se intenta pagar otro pasaje
        $this->assertEquals($tarjetaReal->obtenerSaldo(), 42.6); // pero no pasaron 5 minutos del medio boleto anterior y no se viaja
        $this->assertFalse($tarjetaReal->obtenerUsoPlus());
        $this->assertFalse($tarjetaReal->pagar($colectivo1->linea())); // por muchas veces que intentemos
        $this->assertFalse($tarjetaReal->pagar($colectivo1->linea())); // porque el tiempo no pasa

        $tiempo = new TiempoFalso(time()); // creamos un tiempo manipulable para testear
        $tarjeta = new FranquiciaMedia(2, $tiempo);

        $tarjeta->recargar(10); // saldo inicial: 10
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // el saldo fue restado con franquicia media

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // se adeuda un viaje plus y no deberia contar como un uso de la franquicia en el dia
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.6); // y el saldo no varia
        $this->assertTrue($tarjeta->obtenerUsoPlus());
        $tarjeta->recargar(50); // se recargan 50
        $this->assertEquals($tarjeta->obtenerSaldo(), 52.6);

        $tiempo->avanzar(5 * 60); // hacemos avanzar el reloj 5 minutos para disponer de otro medio boleto
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // usamos el segundo y ultimo medio boleto del dia, ademas de devolver el plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 30.4); // y dejamos el saldo a 30.4
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 1);
        $tiempo->avanzar(2 * 60); // ahora avanzamos 2 minutos
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // al gastarse los 2 medios boletos del dia, se puede viajar sin haber pasado 5 minutos
        $this->assertEquals($tarjeta->obtenerSaldo(), 15.6); // se tuvo que pagar sin franquicia en el 3er viaje
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 0);
        $tarjeta->pagar($colectivo1->linea()); // es innecesario testear
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.8); // y sigue igual a partir del 3er viaje

        $tarjeta->pagar($colectivo1->linea());
        $tarjeta->pagar($colectivo1->linea());
        $this->assertTrue($tarjeta->obtenerUsoPlus());
        // se debieron usar 2 plus

        $tarjeta->recargar(30);
        $tarjeta->recargar(10); // se recargan 40
        $this->assertEquals($tarjeta->obtenerSaldo(), 40.8); // este saldo alcanza para devolver 1 plus y viajar sin franquicia
        $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // pero no se podra viajar por el 2do plus
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 0);

        $tiempo->avanzar(86400); // pero si se avanza 1 dia exacto desde el ultimo viaje
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // si alcanza el saldo para devolver 2 plus mas un medio boleto
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 2);
        $this->assertEquals($tarjeta->obtenerSaldo(), 3.8); // saldo final: 3.8
    }

    /**
     * Comprueba el correcto funcionamiento de una tarjeta con franquicia completa.
     * Esta tarjeta siempre debe poder viajar.
     */
    public function testViajesFranquiciaCompleta() {
        $tarjeta = new FranquiciaCompleta(1, new Tiempo);

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // puede viajar desde el comienzo
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // tantas veces como quiera
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));

        $this->assertFalse($tarjeta->recargar(12)); // se intenta recargar saldo invalido
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // pero no se efectua
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y aun asi podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // sin variar el saldo

        $this->assertFalse($tarjeta->recargar(510.15)); // y aunque la recarga sea valida, devuelve false
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // no variara su saldo
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y podra viajar
        $this->assertEquals($tarjeta->obtenerSaldo(), 0); // sin variar el saldo
        $this->assertFalse($tarjeta->obtenerUsoPlus());
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 0); // y sin recurrir a viajes plus
    }

}
