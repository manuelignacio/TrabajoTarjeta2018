<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Devuelve el nombre de la franquicia a la que pertenece la tarjeta.
     * 
     * @return string
     *   La franquicia a la que pertenece la tarjeta.
     */
    public function obtenerTipo() : string;

    /**
     * Devuelve el ID de la tarjeta.
     * 
     * @return int
     *   El ID de la tarjeta.
     */
    public function obtenerId() : int;

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     *   El saldo actual de la tarjeta.
     */
    public function obtenerSaldo() : float;

    /**
     * Recarga una tarjeta con cierto valor de dinero.
     * Devuelve true si el monto a cargar es válido,
     * o false en caso contrario.
     *
     * @param float $monto
     *   El monto a cargar.
     *
     * @return bool
     *   Si el monto es válido o no lo es.
     */
    public function recargar(float $monto) : bool;

    /**
     * Devuelve siempre con 2 decimales el precio constante
     * que representa un viaje normal sin franquicia
     * aplicada.
     * 
     * @return float
     *   El valor de un viaje normal.
     */
    public function obtenerValorViaje() : float;

    /**
     * Devuelve el valor del ultimo viaje efectuado,
     * incluyendo transbordo y franquicias aplicadas,
     * pero exluyendo el concepto de viaje plus.
     * 
     * @return float
     *   El valor del último viaje efectuado.
     */
    public function obtenerValorUltimoViaje() : float;

    /**
     * Si el saldo es suficiente, lo resta para efectuar un viaje.
     * Si no es suficiente, pero puede endeudar un plus, también efectúa el viaje,
     * pero sin restar el saldo y adeudando el plus.
     * Si no cumple las condiciones anteriores, no efectúa el viaje.
     * Tiene en cuenta la linea y bandera del colectivo en la que esta siendo
     * usada la tarjeta para aplicar transbordo si corresponde.
     * Devuelve true o false de acuerdo a si efectúa o no el viaje respectivamente.
     * 
     * @param string $lineaColectivo
     *   La linea y bandera del colectivo donde se viajará.
     * 
     * @return bool
     *   Si efectúa el viaje o no lo hace.
     */
    public function pagar(string $lineaColectivo) : bool;

    /**
     * Devuelve la cantidad de viajes plus almacenados en deuda.
     * 
     * @return int
     *   La cantidad de viajes plus adeudados actualmente.
     */
    public function obtenerPlus() : int;

    /**
     * Devuelve true o false de acuerdo a si en el último viaje realizado
     * se aplicó franquicia.
     * 
     * @return bool
     *   Si en el último viaje efectuado aplicó franquicia o no lo hizo.
     */
    public function obtenerUsoFranquicia() : bool;
    
    /**
     * Devuelve true o false de acuerdo a si en el último viaje realizado
     * se aplicó plus.
     * 
     * @return bool
     *   Si en el último viaje efectuado aplicó plus o no lo hizo.
     */
    public function obtenerUsoPlus() : bool;

    /**
     * Devuelve true o false de acuerdo a si en el último viaje realizado
     * se aplicó transbordo.
     * 
     * @return bool
     *   Si en el último viaje efectuado aplicó transbordo o no lo hizo.
     */
    public function obtenerUsoTransbordo() : bool;

    /**
     * Devuelve la cantidad de viajes plus abonados (devueltos) en el
     * último viaje efectuado. Por lo tanto si en el viaje anterior al
     * último no se aplicó un plus, si y solo si éste método devolverá 0.
     * 
     * @return int
     *   La cantidad de plus devueltos en el último viaje efectuado.
     */
    public function obtenerPlusDevueltos() : int;

    /**
     * Devuelve la fecha en la que se viajó por última vez, sin formatear.
     * Se interpreta como la cantidad de segundos transcurridos desde el 1
     * de enero de 1970 a la 01:00:00
     * 
     * @return int
     *   La cantidad de segundos transcurridos desde el 1/1/1970 a la 1hs
     *   y hasta el último viaje efectuado.
     */
    public function obtenerFechaUltimoViaje() : int;

}
