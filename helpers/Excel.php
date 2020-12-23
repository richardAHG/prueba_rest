<?php

namespace app\helpers;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Permite validar datos de un archivo excel que seran guardados en la Base de datos
 * 
 * @author Richard Huamán <richard21hg92@gmail.com>
 */
class Excel
{
    public $file;
    public $data = [];

    /**
     * Inicializa la lectura de un archivo excel
     *
     * colocar el nombre del archivo que esta en el servidor
     * @param [string] $archivo , 
     * @return object IOFactory
     */
    public  function load($archivo)
    {
        $file_ = File::getPath() . '/' . $archivo;
        $this->file = IOFactory::load($file_);
        return $this->file;
    }

    /**
     * Retorna el total de hojas del archivo inicializado
     *
     * @return int
     */
    public function getSheetTotal()
    {
        return $this->file->getSheetCount();
    }

    /**
     * Retorna una hoja especifica indicandole el indice
     *
     * @param [int] $sheetNumber
     * @return object
     */
    public function getSheet($sheetNumber)
    {
        return $this->file->getSheet($sheetNumber);
    }

    /**
     * Retorna el numero total de filas de la hoja actual
     *
     * @param [object] $hojaActual, paramatro tipo IOFactory
     * @return int
     */
    public function getTotalRows($hojaActual)
    {
        return $hojaActual->getHighestDataRow();
    }

    /**
     * Retorna la ultima letra de la columna de la hoja actual
     *
     * @param [object] $hojaActual, paramatro tipo IOFactory
     * @return string
     */
    private function getTotalColumnLetter($hojaActual)
    {
        return $hojaActual->getHighestColumn();
    }

    /**
     * Retorna el numero total de columnas de la hoja actual
     *
     * @param [object] $hojaActual, paramatro tipo IOFactory
     * @return int
     */
    public function getTotalColumnNumber($hojaActual)
    {
        return  Coordinate::columnIndexFromString($this->getTotalColumnLetter($hojaActual));
    }

    /**
     * Retorna data de la lectura de todas las hojas
     *
     * @param [array] $requiredColumns, campos obligatorios ejm ['SUBPROCESOS', 'PROCESO']
     * @param [array] $plantilla, indicar el tipo de plantilla a validar
     * @param [int] $starRow, indicar el numero de fila como inicio de lectura
     * @return array
     */
    public function getReadAllSheets($requiredColumns, $plantilla, $starRow)
    {
        for ($i = 0; $i < $this->getSheetTotal(); $i++) {
            //obtener una hoja especifica
            $hojaActual = $this->getSheet($i);
            $totalFilas = $this->getTotalRows($hojaActual);
            $totalColumnas = $this->getTotalColumnNumber($hojaActual);
            //Validar cabeceras de la plantilla
            $HeadersLoad = $this->getStructureFileLoad($hojaActual, $totalColumnas);
            $arrayDiff = $this->validateStructure($plantilla, $HeadersLoad);
            if (count($arrayDiff) > 2) {
                return [
                    'data' => $arrayDiff,
                    'estado' => false
                ];
            }
            //llena el array con datos del excel
            $this->getFillArray($starRow, $hojaActual, $totalFilas, $plantilla);
            // valida campos obligatorios que esten vacios
            $emptyRows = $this->validateRowsEmpty($plantilla, $requiredColumns);
            if (!empty($emptyRows)) {
                return [
                    'data' => $emptyRows,
                    'estado' => false
                ];
            }
        }
        return [
            'data' => $this->data,
            'estado' => true
        ];
    }

    /**
     * Retorna Data de la lectura de una hoja
     *
     * @param [int] $starRow, indicar el numero de fila como inicio de lectura
     * @param [object] $hojaActual, reeferencia a clase IOFactory
     * @param [int] $totalFilas
     * @param [array] $plantilla, es la estrucutra del excel con la cual se validara
     * @return array
     */
    public function getFillArray($starRow, $hojaActual, $totalFilas, $plantilla)
    {
        for ($fil = $starRow; $fil <= $totalFilas; $fil++) {
            foreach ($plantilla as $key => $value) {
                //posicion
                $celda = $hojaActual->getCellByColumnAndRow($key, $fil);
                $this->data[$fil][$value] = trim($celda->getValue());
            }
        }
    }

    /**
     * Retorna la estructura del archivo cargado
     *
     * @param [object] $hojaActual, reeferencia a clase IOFactory
     * @param [int] $totalColumnas
     * @return void
     */
    public function getStructureFileLoad($hojaActual, $totalColumnas)
    {
        $invalidHeader = [];
        for ($fil = 1; $fil <= 1; $fil++) {
            for ($col = 1; $col <= $totalColumnas; $col++) {
                //posicion
                $celda = $hojaActual->getCellByColumnAndRow($col, $fil);
                $invalidHeader[$col] = trim($celda->getValue());
            }
        }
        return $invalidHeader;
    }

    /**
     * Valida la diferencia de estructura de la plantilla y el  archivo cargado
     *
     * @param [array] $arrayStructure,
     * @param [array] $arrayLoad,
     * @return array
     */
    public function validateStructure($arrayStructure, $arrayLoad)
    {
        $data = [];
        $data['estructura_plantilla'] = array_diff($arrayStructure, $arrayLoad);
        $data['estructura_excel'] = array_diff($arrayLoad, $arrayStructure);
        return $data;
    }

    /**
     * Valida campos vacios de columnas obligatorias
     *
     * @param [array] $plantilla,
     * @param [array] $requiredFields, ejemplo ['SUBPROCESOS', 'PROCESO']
     * @return array
     */
    public function validateRowsEmpty($plantilla, $requiredFields)
    {
        $dataErrors = [];
        $validateStructure = array_diff($requiredFields, $plantilla);
        if (empty($requiredFields)) {
            return;
        }
        if (!empty($validateStructure)) {
            return $validateStructure;
        }
        foreach ($requiredFields as $key => $value) {
            // echo $value;
            $validar = array_column($this->data, $value);
            foreach ($validar as $keyx => $valuex) {
                if (empty($valuex)) {
                    //agregamos 2 posiciones, 
                    //porque inicia en 0 y se lee a partir del indice 1
                    //esto permitira presentar las posiciones correctas
                    $dataErrors['columna ' . $value][] = 'fila -' . ($keyx + 2);
                }
            }
        }
        return  $dataErrors;
    }

    /** Funciones para subida de archivo a la carpeta media/temp*/

    /**
     * Inicializa la lectura de un archivo excel,csv
     *
     * colocar el nombre del archivo de la carpeta temp que esta en el servidor
     * @param [string] $archivo , 
     * @return object IOFactory
     */
    public  function loadTemp($archivo)
    {
        $file_ = File::getPathTemp() . '/' . $archivo;
        $this->file = IOFactory::load($file_);
        return $this->file;
    }

    /**
     * Retorna las cabecera de la hoja actual
     *
     * @param [object] $hojaActual, reeferencia a clase IOFactory
     * @param [int] $totalColumnas
     * @return array
     */
    public function getHeaders($hojaActual, $totalColumnas)
    {
        $estructura = [];
        for ($col = 1; $col <= $totalColumnas; $col++) {
            //posicion
            $celda = $hojaActual->getCellByColumnAndRow($col, 1);
            $estructura[$col] = trim($celda->getValue());
        }
        return $estructura;
    }

    /**
     * Lee todas las hojas de un archivo
     *
     * @param [int] $starRow, numero de la fila que servira como inicio de lectrua del archivo
     * @return array
     */
    public function getReadAllSheetsLoaded($starRow)
    {
        for ($i = 0; $i < $this->getSheetTotal(); $i++) {
            //obtener una hoja especifica
            $hojaActual = $this->getSheet($i);
            $totalFilas = $this->getTotalRows($hojaActual);
            $totalColumnas = $this->getTotalColumnNumber($hojaActual);
            $headers=$this->getHeaders($hojaActual, $totalColumnas);
            //llena el array con datos del excel
            $this->getFillArrayLoaded($starRow, $hojaActual, $totalFilas, $headers);
        }
        return [
            'data' => $this->data,
            'headers'=>$headers,
            'estado' => true
        ];
    }

    /**
     * llena el array con la informacion de la hoja leida
     *
     * @param [int] $starRow
     * @param [object] $hojaActual, reeferencia a clase IOFactory
     * @param [int] $totalFilas
     * @param [array] $headers,array de columnas obligatorias
     * @return array
     */
    public function getFillArrayLoaded($starRow, $hojaActual, $totalFilas, $headers)
    {
        for ($fil = $starRow; $fil <= $totalFilas; $fil++) {
            foreach ($headers as $key => $value) {
                //posicion
                $celda = $hojaActual->getCellByColumnAndRow($key, $fil);
                $this->data[$fil][$value] = trim($celda->getValue());
            }
        }
    }

}
