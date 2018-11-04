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
     * Comprueba casos de viajes empleando el tiempo real,
     * por lo que no avanza, dada la velocidad de ejecucion
     * de los tests.
     */
    public function testViajesConTiempoReal() {
        $tiempoReal = new Tiempo; // testeamos con el tiempo real
        $colectivo1 = new Colectivo("102R","Semtur",120);
        $colectivo2 = new Colectivo("102N","Semtur",119);

        $tarjeta = new Tarjeta(1, $tiempoReal);

            $tarjeta->recargar(50); // saldo inicial: 50
            $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
            $this->assertEquals($tarjeta->obtenerSaldo(), 35.2); // el viaje se cobra normal
            $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // se paga otro viaje
            $this->assertEquals($tarjeta->obtenerSaldo(), 20.4); // y se cobra normal nuevamente
            $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // y si probamos en otra linea de colectivo se cobra al 33%
            $this->assertTrue($tarjeta->obtenerUsoTransbordo()); // ya que se aplico transbordo
            $this->assertEquals($tarjeta->obtenerSaldo(), 15.52); // saldo final: 15.52

        $tarjeta = new FranquiciaMedia(2, $tiempoReal);

            $tarjeta->recargar(50); // saldo inicial: 50
            $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
            $this->assertEquals($tarjeta->obtenerSaldo(), 42.6); // el viaje se cobra a mitad de precio
            $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // se intenta pagar otro pasaje
            $this->assertEquals($tarjeta->obtenerSaldo(), 42.6); // pero no pasaron 5 minutos del medio boleto anterior y no se viaja
            $this->assertFalse($tarjeta->obtenerUsoPlus()); // sin tampoco haber usado plus
            $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // por muchas veces que intentemos
            $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // porque el tiempo no pasa
            $this->assertEquals($tarjeta->obtenerSaldo(), 42.6); // saldo final: 42.6
    }

    /**
     * Comprueba el correcto funcionamiento de los metodos usados
     * para viajar en una tarjeta sin franquicia:
     * - Prueba que el saldo se reste al pagar un viaje
     * - Ademas prueba el correcto funcionamiento del concepto 'viaje plus' al pagar y recargar
     * El metodo recargar se efectúa sin ser testeado para no
     * redundar con los tests anteriores.
     * El valor de un viaje sin franquicia se consideran de 14.8
     * El tiempo es falso (manipulable).
     */
    public function testViajesSinFranquicia() {
        $tiempo = new TiempoFalso(time());
        $tarjeta = new Tarjeta(1, $tiempo);
        $colectivo1 = new Colectivo("102R","Semtur",120);
        $colectivo2 = new Colectivo("102N","Semtur",119);

        $tarjeta->recargar(20); // saldo inicial: 20
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // el saldo fue restado
        $this->assertFalse($tarjeta->obtenerUsoPlus()); // sin usarse plus

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // se adeuda un viaje plus
        $this->assertTrue($tarjeta->obtenerUsoPlus());
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); // pero el saldo no varia

        $tiempo->avanzar(120); // se avanzan 2 minutos (dentro del rango para hacer transbordo)
        $tarjeta->recargar(20); // se recargan 20
        $this->assertEquals($tarjeta->obtenerSaldo(), 25.2); // y en principio el plus queda pendiente
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // de devuelve el plus y se viaja con transbordo
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 1); // verifica que se devolvio el plus...
        $this->assertFalse($tarjeta->obtenerUsoPlus()); // ...que no uso otro plus...
        $this->assertTrue($tarjeta->obtenerUsoTransbordo()); // ...y que se uso un transbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.52);

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // como el saldo no es suficiente para viajar y devolver el plus, se adeuda otro plus
        $this->assertTrue($tarjeta->obtenerUsoPlus()); // verifica que uso plus
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y adeuda otro
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.52); // y el saldo sigue igual

        $tarjeta->recargar(50); // se recarga suficiente
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y con este viaje se pagan todos los plus
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 2); // podemos comprobar que se devolvieron 2 plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 11.12);

        $tiempo->avanzar(7200); // se avanzan 2 horas (fuera del rango para hacer transbordo)
        $tarjeta->recargar(10);
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // se viaja en otra linea...
        $this->assertFalse($tarjeta->obtenerUsoTransbordo()); // ...pero sin transbordo, a precio normal
        $this->assertEquals($tarjeta->obtenerSaldo(), 6.32); // saldo final: 13.68
    }

    /**
     * Comprueba los mismos comportamientos del test anterior,
     * pero en una tarjeta con franquicia media.
     * El valor de un viaje medio boleto se considera de 14.8/2 = 7.4
     * El tiempo es falso (manipulable).
     */
    public function testViajesFranquiciaMedia() {
        $tiempo = new TiempoFalso(time());
        $tarjeta = new FranquiciaMedia(2, $tiempo);
        $colectivo1 = new Colectivo("102R","Semtur",120);
        $colectivo2 = new Colectivo("102N","Semtur",119);

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
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // como se cambia de colectivo
        $this->assertTrue($tarjeta->obtenerUsoTransbordo()); // deberia aplicarse transbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), 10.72); // pero sin aplicarse la media franquicia

        $tiempo->avanzar(86400); // se avanza 1 dia para disponer otra vez de la franquicia
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // primero, se aplicara solo la franquicia
        $this->assertEquals($tarjeta->obtenerSaldo(), 3.32);
        $tiempo->avanzar(6 * 60); // se avanzan 6 minutos
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y luego, vemos que se aplican franquicia
        $this->assertTrue($tarjeta->obtenerUsoTransbordo()); // y transbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.88);

        $tarjeta->pagar($colectivo1->linea());
        $tarjeta->pagar($colectivo1->linea());
        $this->assertTrue($tarjeta->obtenerUsoPlus());
        // se debieron usar 2 plus

        $tarjeta->recargar(30);
        $tarjeta->recargar(10); // se recargan 40
        $this->assertEquals($tarjeta->obtenerSaldo(), 40.88); // este saldo alcanza para devolver 1 plus y viajar sin franquicia
        $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // pero no se podra viajar porque hay 2 plus
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 0);

        $tiempo->avanzar(86400); // pero si se avanza 1 dia desde el ultimo viaje
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // si alcanza el saldo para devolver 2 plus mas un medio boleto
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 2);
        $this->assertEquals($tarjeta->obtenerSaldo(), 3.88);

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // se usa un plus, aunque no hayan pasado 5 minutos por el uso de franquicia
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // y otro plus, ya que aunque pueda usar transbordo al cambiar de linea, no se puede devolver el plus anterior
        $tarjeta->recargar(30); // se recargan 30
        $this->assertEquals($tarjeta->obtenerSaldo(), 33.88);
        $this->assertFalse($tarjeta->pagar($colectivo1->linea())); // no se puede viajar ya que se debe usar la franquicia y no pasaron 5 minutos desde su ultima aplicacion
        $tiempo->avanzar(6 * 60); // entonces se avanzan 6 minutos
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // y de esta forma, se consiguen devolver 2 plus y pagar con transbordo y franquicia
        $this->assertEquals($tarjeta->obtenerSaldo(), 1.84); // saldo final: 1.84
    }

    /**
     * Comprueba el correcto funcionamiento de una tarjeta con franquicia completa.
     * Esta tarjeta siempre debe poder viajar.
     * El tiempo es falso (manipulable).
     */
    public function testViajesFranquiciaCompleta() {
        $tiempo = new TiempoFalso(time());
        $tarjeta = new FranquiciaCompleta(1, $tiempo);
        $colectivo1 = new Colectivo("102R","Semtur",120);
        $colectivo2 = new Colectivo("102N","Semtur",119);

        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // puede viajar desde el comienzo
        $this->assertTrue($tarjeta->pagar($colectivo1->linea())); // tantas veces como quiera
        $this->assertTrue($tarjeta->pagar($colectivo1->linea()));

        $tiempo->avanzar(600); // se avanzan 10 minutos para probar un transbordo
        $this->assertTrue($tarjeta->pagar($colectivo2->linea())); // pero debe poder viajar
        $this->assertFalse($tarjeta->obtenerUsoTransbordo()); // sin usar transbordo, por ser franquicia completa
        $this->assertFalse($tarjeta->obtenerUsoPlus());
        $this->assertEquals($tarjeta->obtenerPlusDevueltos(), 0); // ni viajes plus

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
