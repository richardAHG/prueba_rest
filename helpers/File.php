<?php

namespace app\helpers;

use Yii;
use Exception;
use yii\helpers\FileHelper;
use app\modules\v1\constants\Globals;
use DateTime;
use yii\web\HttpException;

/**
 * Clase que ayuda a gestionar archivos
 * @author Eder Marquina <edermarsud@gmail.com>
 */
class File
{
    public static function generateNameFile($file = null)
    {
        // return uniqid(rand(), false) . '.' . $file->getExtension();
        return uniqid(rand(), false);
    }

    public static function createDirectory($directorio)
    {
        if (!file_exists($directorio)) {
            $creado = FileHelper::createDirectory($directorio, 0777, true);

            if (!$creado) {
                throw new Exception("Error al crear directorio");
            }
        }

        return $directorio;
    }

    public static function getPath()
    {
        $ruta = Yii::$app->basePath . '/media';
        return self::createDirectory($ruta);
    }

    public static function getPathTemp()
    {
        $ruta = Yii::$app->basePath . '/media/temp';
        return self::createDirectory($ruta);
    }

    /**
     * Sube archivo al servidor
     *
     * @param [file] $file, debe ser instanciado con UploadedFile::getInstanceByName();
     * @param [string] $ruta, ejemplo: '/proyecto/compromiso/miArchivo'
     * @return void
     */
    public static function upload($file, $ruta)
    {
        $dataFile = $file->saveAs($ruta);

        if (!$dataFile) {
            throw new HttpException(400, "Error al momento de subir el archivo al servidor");
        }
    }

    public static function delete($file)
    {
        if (is_file($file)) {
            return unlink($file);
        }
        return false;
    }

    public static function rename($oldNameFile, $newNameFile)
    {
        if (is_file($oldNameFile)) {
            return rename($oldNameFile, $newNameFile);
        }
        return false;
    }

    public static function move($origin, $destination)
    {
        return self::rename($origin, $destination);
    }

    public static function allowedExtensions($extension)
    {
        $extensiones = ['pdf', 'jpg', 'png', 'doc', 'docx', 'xlsx', 'csv'];

        if (!in_array($extension, $extensiones)) {
            throw new HttpException(400, "Archivo no pemitido");
        }

        return true;
    }

    public static function validate($file)
    {
        if (empty($file)) {
            throw new HttpException(400, "Debe cargar un archivo");
        }

        if ($file->error != 0) {
            throw new HttpException(400, "Error al leer el archivo");
        }

        self::validaNameLength($file->name);

        $extension = self::getExtension($file->name);
        self::allowedExtensions($extension);

        return true;
    }

    public static function validaNameLength($fileName, $length = Globals::FILE_LENGTH)
    {
        if (strlen($fileName) > $length) {
            throw new HttpException(400, "Nombre de archivo debe ser menor a {$length} caracteres");
        }

        return true;
    }

    public static function getExtension($fileName)
    {
        $extension = explode('.', $fileName);
        return end($extension);
    }

    // id del archivo, obtener ruta 
}
