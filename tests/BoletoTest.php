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

        /**
         * Existe un conflicto entre la interpretación de fecha en Travis y en PHPUnit instalado con PHP 7.2.4
         */
        $descripcion1 = "Linea: 102R\n";
        $fechaPHPUnit = "01/01/1970 01:00:00";
        $fechaTravis = "01/01/1970 00:00:00";
        $descripcion2 = "\nNormal\nTarros: \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$0\nID Tarjeta: 1";

        $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
        $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";

        $this->assertEquals($boleto->obtenerValor(), $tarjeta->obtenerValorViaje());
        $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
        $this->assertEquals($boleto->obtenerTarjeta(), $tarjeta);
        $this->assertTrue($boleto->obtenerFecha() === $fechaPHPUnit || $boleto->obtenerFecha() === $fechaTravis);
        $this->assertEquals($boleto->obtenerTarjetaTipo(), "Normal");
        $this->assertEquals($boleto->obtenerTarjetaID(), 1);
        $this->assertEquals($boleto->obtenerTarjetaSaldo(), 0);
        $this->assertEquals($boleto->obtenerTotalAbonado(), $tarjeta->obtenerValorViaje());
        $this->assertTrue($boleto->obtenerDescripcion() === $descripcionPHPUnit || $boleto->obtenerDescripcion() == $descripcionTravis);
    }

}
