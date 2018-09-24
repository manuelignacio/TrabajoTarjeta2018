<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    /**
     * Comprueba que el constructor y los metodos de obtener en Boleto funcionen correctamente
     */
    public function testObtenerDatos() {
        $tiempo = new TiempoFalso(100);
        $tarjeta = new Tarjeta(1, $tiempo);
        $colectivo = new Colectivo("102R","Semtur",120);

        $boleto = new Boleto($tarjeta->obtenerValorViaje(), $colectivo, $tarjeta);

        $descripcion = "Linea: 102R\n01/01/1970 01:00:00\nNormal\nTarros: \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$0\nID Tarjeta: 1";

        $this->assertEquals($boleto->obtenerValor(), $tarjeta->obtenerValorViaje());
        $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
        $this->assertEquals($boleto->obtenerTarjeta(), $tarjeta);
        $this->assertEquals($boleto->obtenerFecha(), "01/01/1970 01:00:00");
        $this->assertEquals($boleto->obtenerTarjetaTipo(), "Normal");
        $this->assertEquals($boleto->obtenerTarjetaID(), 1);
        $this->assertEquals($boleto->obtenerTarjetaSaldo(), 0);
        $this->assertEquals($boleto->obtenerTotalAbonado(), $tarjeta->obtenerValorViaje());
        $this->assertEquals($boleto->obtenerDescripcion(), $descripcion);
    }

}
