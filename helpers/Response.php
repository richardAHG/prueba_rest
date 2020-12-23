<?php

namespace app\helpers;

use Yii;

/**
 * Clase para formatear respuestas al fronEnd
 * 
 *  @author Eder Marquina <edermarsud@gmail.com>
 */
class Response
{
    public $data;

    public static function JSON($status = 500, $message = null, $data = [],$tipo_error=0,$token=false)
    {
        $headers = Yii::$app->response->headers;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->statusCode = $status;
        $response->data = [
            'token'=>$token,
            'tipo'=>$tipo_error,
            'message' => $message,
            'data' => $data,
            'code' => 0,
            'status' => $status,
        ];
        $response->send();

        Yii::$app->end();
    }
}
