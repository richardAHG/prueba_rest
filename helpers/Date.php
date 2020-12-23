<?php

namespace app\helpers;

use DateTime;
use DateInterval;

class Date
{

    /**
     * @const Fecha por defecto
     */
    const DEFAULT_DATE = "01-01-1900";


    /**
     * Obtiene la fecha actual en un formato dado
     * @param string $formato formato de fecha en php.
     * @return string
     */
    public static function getCurrentDate($formato = "Y-m-d")
    {
        $fecha = new DateTime("now");
        return $fecha->format($formato);
    }

    /**
     * Obtiene la hora actual en un formato dado
     * @param string $formato formato de tiempo en php
     * @return string
     */
    public static function getTime($formato = "H:i:s")
    {
        $hora = new DateTime("now");
        return $hora->format($formato);
    }

    /**
     * Obtiene la fecha y hora actual.
     * @param string $formatoFecha Formato de fecha en php
     * @param string $formatoHora Formato de hora en php
     * @return string
     */
    public static function getDateTime($formatoFecha = "Y-m-d", $formatoHora = "H:i:s")
    {
        return self::getCurrentDate($formatoFecha) . " " . self::getTime($formatoHora);
    }

    /**
     * Formatea una fecha
     * @param string $fecha Fecha a ser formateada
     * @param type $nuevoFormato formato php de fecha
     * @return date
     */
    public static function format($fecha = null, $nuevoFormato = null)
    {
        $fecha = new DateTime($fecha);
        return $fecha->format($nuevoFormato);
    }

    /**
     * Obtiene todos los meses.
     * @param boolean $zerofield Si se quiere tener el campo del mes con 0 al inicio
     * @param boolean $abrev Si se quiere obtener el mes abreviado
     * @return array
     */
    public static function getMonthAll($zerofield = false, $abrev = false)
    {
        $months = [
            1  => "ENERO",
            2  => "FEBRERO",
            3  => "MARZO",
            4  => "ABRIL",
            5  => "MAYO",
            6  => "JUNIO",
            7  => "JULIO",
            8  => "AGOSTO",
            9  => "SEPTIEMBRE",
            10 => "OCTUBRE",
            11 => "NOVIEMBRE",
            12 => "DICIEMBRE",
        ];

        if ($zerofield) {
            foreach ($months as $idMonth => $month) {
                $idZero          = ($idMonth < 10) ? "0" . $idMonth : $idMonth;
                unset($months[$idMonth]);
                $months[$idZero] = $month;
            }
        }

        if ($abrev) {
            return self::getMonthsAbreviatures($months);
        }

        return $months;
    }

    /**
     * Retorna las primeras 3 letras del nombre del mes
     * @param string $month Nombre del mes
     * @return string
     */
    public static function getMonthAbreviature($month)
    {
        return substr($month, 0, 3);
    }

    /**
     * Retorna los meses abreviados.
     * @param array $months Arreglo de meses
     * @return array
     */
    public static function getMonthsAbreviatures($months = [])
    {
        $month_abrev = [];

        foreach ($months as $id => $month) {
            $month_abrev[$id] = self::getMonthAbreviature($month);
        }

        return $month_abrev;
    }

    /**
     * Obtiene el mes mediante su numero.
     * @param int $id Numero del mes
     * @param boolean $abrev Si quiere el mes abreviado o no.
     * @return string
     */
    public static function getMonthById($id, $abrev = false)
    {
        $months = self::getMonthAll();

        if (!isset($months[$id])) {
            return "";
        }

        if ($abrev) {
            return self::getMonthAbreviature($months[$id]);
        }

        return $months[$id];
    }


    /**
     * Obtiene el ultimo dia del mes
     * @param int $mes numero del mes
     * @param int $anio anio a ser calculado
     * @return int
     */
    public static function getLastDayofMonth($mes = false, $anio = false)
    {
        $mes       = ($mes === false) ? date('n') : $mes;
        $anio      = ($anio === false) ? date('Y') : $anio;
        $ultimoDia = date('t', strtotime("{$anio}-{$mes}"));
        return $ultimoDia;
    }

    /**
     * Retorna los nombres del dia de la semana
     * @param boolean $iso Indica si va a utilizar el formato ISO
     * @param int $numDay numero del dia.
     * @return array|string
     */
    public static function getNameWeekDays($iso = false, $numDay = false)
    {
        $days = [
            0 => "Domingo",
            1 => "Lunes",
            2 => "Martes",
            3 => "Miercoles",
            4 => "Jueves",
            5 => "Viernes",
            6 => "Sabado",
        ];
        if ($iso) {
            $dayIso = [];
            foreach ($days as $key => $day) {
                $dayIso[$key + 1] = $day;
            }
            return $dayIso;
        }

        if (is_numeric($numDay)) {
            return $days[$numDay];
        }

        return $days;
    }

    /**
     * Obtiene los nombres de la semana abreviados
     * @return array
     */
    public static function getAbrevNameWeekDays()
    {
        $weekDays      = self::getNameWeekDays();
        $abrevWeekDays = [];

        foreach ($weekDays as $numDay => $day) {
            $abrevWeekDays[$numDay] = substr($day, 0, 2);
        }

        return $abrevWeekDays;
    }



    public static function diff($date_start, $date_end, $same_day = false)
    {
        $datetimestart = new DateTime(self::format($date_end, "Y-m-d"));
        $datetimeend   = new DateTime(self::format($date_start, "Y-m-d"));
        if ($same_day) {
            $datetimestart->add(new DateInterval('P1D'));
        }
        $interval = $datetimestart->diff($datetimeend);
        return $interval->format("%a");
    }

    public static function getYearsTop(int $top = 100)
    {
        $years = [];
        $year = date('Y');

        for ($i = $year; $i > ($year - $top); $i--) {
            $years[$i] = $i;
        }

        return $years;
    }

    /**
     * Valida si la fecha es fin de mes
     *
     * @param [DateTime] $date
     * @return Bool
     */
    public static function validarFinMes($fecha)
    {
        $totalDiaMes = $fecha->format('t');
        return $fecha->format('d') == $totalDiaMes ? true : false;
    }

    /**
     * Devuelve la diferencia de meses
     *
     * @param [DateTime] $fechaFinal
     * @param [DateTime] $fechaInicial
     * @return Int
     */
    public static function getDiffMonth($fechaFinal, $fechaInicial)
    {
        $diff = $fechaFinal->diff($fechaInicial);
        return ($diff->y * 12) + $diff->m;
    }

    /**
     * Devuelve la diferencia de días entre un rango de fechas
     *
     * @param [date] $fecha1
     * @param [date] $fecha2
     * @return int
     */
    public static function getDiffDays($fecha1, $fecha2)
    {
        $diff = $fecha1->diff($fecha2);
        return $diff->days;
    }

    /**
     * Resta días, meses, años, etc a una fecha
     *
     * @param [date] $fecha
     * @param [string] $interval: P1D: resta un día. Ver: https://www.php.net/manual/es/class.dateinterval.php
     * @param [string] $format
     * @return void
     */
    public static function subtractDate($fecha, $interval, $format)
    {
        $fecha = new DateTime($fecha);
        $fecha->sub(new DateInterval($interval));
        return $fecha->format($format);
    }
}
