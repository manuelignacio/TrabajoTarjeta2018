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
        $valor = $tarjeta->valorViaje($colectivo->linea());

        $boleto = new Boleto($valor, $colectivo, $tarjeta);

        /**
         * Existe un conflicto entre la interpretación de fecha en Travis y en PHPUnit instalado con PHP 7.2.4
         */
        $descripcion1 = "Linea: 102R\n";
        $fechaPHPUnit = "01/01/1970 01:00:00";
        $fechaTravis = "01/01/1970 00:00:00";
        $descripcion2 = "\nNormal \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$0\nTarjeta: 1";

        $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
        $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";

        $this->assertEquals($boleto->obtenerValor(), $valor);
        $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
        $this->assertEquals($boleto->obtenerTarjeta(), $tarjeta);
        $this->assertContains($boleto->obtenerFecha(), [$fechaPHPUnit, $fechaTravis]);
        $this->assertEquals($boleto->obtenerTarjetaTipo(), "Normal");
        $this->assertEquals($boleto->obtenerTarjetaID(), 1);
        $this->assertEquals($boleto->obtenerTarjetaSaldo(), 0);
        $this->assertEquals($boleto->obtenerAbonado(), $valor);
        $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);
    }

    public function testVariaciones() {
        $tiempo = new TiempoFalso(0);
        $colectivo = new Colectivo("133N","Semtur",120);

        $tarjeta = new Tarjeta(1, $tiempo);

            $tarjeta->recargar(20);
            $tiempo->avanzar(1433338200);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion1 = "Linea: 133N\n";
            $fechaPHPUnit = "03/06/2015 15:30:00";
            $fechaTravis = "03/06/2015 13:30:00";
            $descripcion2 = "\nNormal \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nViaje Plus 1 \$0.00\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nViaje Plus 2 \$0.00\nSaldo(S.E.U.O): \$5.2\nTarjeta: 1";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $tarjeta->recargar(100); // saldo: 105.2
            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nAbona 2 Viajes Plus \$29.6 y\nNormal \$14.8\nTotal abonado: \$44.4\nSaldo(S.E.U.O): \$60.8\nTarjeta: 1";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

        $tarjeta = new FranquiciaMedia(2, $tiempo);

            $tarjeta->recargar(10);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nMedio \$7.4\nTotal abonado: \$7.4\nSaldo(S.E.U.O): \$2.6\nTarjeta: 2";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nViaje Plus 1 \$0.00\nSaldo(S.E.U.O): \$2.6\nTarjeta: 2";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nViaje Plus 2 \$0.00\nSaldo(S.E.U.O): \$2.6\nTarjeta: 2";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $tarjeta->recargar(100); // saldo: 102.6
            $tiempo->avanzar(360); // 6 minutos
            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $fechaPHPUnit = "03/06/2015 15:36:00";
            $fechaTravis = "03/06/2015 13:36:00";
            $descripcion2 = "\nAbona 2 Viajes Plus \$29.6 y\nMedio \$7.4\nTotal abonado: \$37\nSaldo(S.E.U.O): \$65.6\nTarjeta: 2";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $tiempo->avanzar(360); // 6 minutos
            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $fechaPHPUnit = "03/06/2015 15:42:00";
            $fechaTravis = "03/06/2015 13:42:00";
            $descripcion2 = "\nNormal \$14.8\nTotal abonado: \$14.8\nSaldo(S.E.U.O): \$50.8\nTarjeta: 2";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

        $tarjeta = new FranquiciaCompleta(3, $tiempo);

            $tarjeta->recargar(10); // no recarga por ser franquicia completa

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $descripcion2 = "\nFranquicia Completa \$0\nTotal abonado: \$0\nSaldo(S.E.U.O): \$0\nTarjeta: 3";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $tarjeta->recargar(100); // saldo: 0
            $tiempo->avanzar(360); // 6 minutos
            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $fechaPHPUnit = "03/06/2015 15:48:00";
            $fechaTravis = "03/06/2015 13:48:00";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

            $tiempo->avanzar(360); // 6 minutos
            $valor = $tarjeta->valorViaje($colectivo->linea());
            $tarjeta->pagar($colectivo->linea());
            $boleto = new Boleto($valor, $colectivo, $tarjeta);
            $fechaPHPUnit = "03/06/2015 15:54:00";
            $fechaTravis = "03/06/2015 13:54:00";
            $descripcionPHPUnit = "{$descripcion1}{$fechaPHPUnit}{$descripcion2}";
            $descripcionTravis = "{$descripcion1}{$fechaTravis}{$descripcion2}";
            $this->assertContains($boleto->obtenerDescripcion(), [$descripcionPHPUnit, $descripcionTravis]);

    }

}
