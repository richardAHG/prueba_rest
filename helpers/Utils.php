<?php

namespace app\helpers;

use app\modules\v1\constants\Globals;

class Utils
{
    /**
     * Genera y retorna un token
     *
     * @param [string] $algorithm. Ejem: "sha1"
     * @param [string] $input. Ejem: uniqid()
     * @param [int] $length. Número de caracteres del token. Ejem: 8
     * @return string
     * Utils::token("sha1", uniqid(), 8);
     */
    public static function token($algorithm, $input, $length)
    {
        $output = '';
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUFWXIZ0123456789';
        $input  = base64_encode(hash_hmac($algorithm, $input, Globals::SECRET, true));

        do {
            foreach (str_split($input, 8) as $chunk) {
                srand((int) hexdec($chunk));
                $output .= substr($charset, rand(0, strlen($charset)), 1);
            }
            $input = md5($input);
        } while (strlen($output) < $length);

        return substr($output, 0, $length);
    }

    public static function getError($model)
    {
        $message = '';
        foreach ($model->getErrors() as $key => $error) {
            $message = $error[0];
            break;
        }

        return $message;
    }

    public static function endTime($startTime)
    {
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $hours = (int)($duration / 60 / 60);
        $minutes = (int)($duration / 60) - $hours * 60;
        $seconds = (int)$duration - $hours * 60 * 60 - $minutes * 60;
        echo "Tiempo de ejecución {$hours} horas, {$minutes} minutos y {$seconds} segundos";
    }
}
