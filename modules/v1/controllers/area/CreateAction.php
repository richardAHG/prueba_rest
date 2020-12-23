<?php

namespace app\modules\v1\controllers\area;

use app\modules\v1\constants\Params;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\rest\Action;

/**
 * * CreateAction registra información de las áreas
 * 
 * Se realiza el registro de una área con las 
 * validaciones requeridas
 * 
 * @author Richard Huamán <richard21hg92@gmail.com>
 */
class CreateAction extends Action
{
    /**
     * @var string el escenario que se asignará al modelo antes de que sea validado y registrado.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    
    /**
     * @var string el nombre de la acción view. Esta propiedad es necesaria para crear la URL cuando el modelo se crea correctamente.
     */
    public $viewAction = 'view';

    /**
     * Crea un nuevo registro
     * 
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        // proyectoId permite validar que el nombre no existan en los registros
        $proyectoId = Params::getProyectoId();

        if (!$proyectoId) {
            throw new BadRequestHttpException("Bad Request");
        }

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        
        if (isset($requestParams['nombre'])) {
            $areaModel = $this->modelClass::find()
                ->where(['proyecto_id' => $proyectoId])
                ->andWhere(['estado' => true])
                ->andWhere(['nombre' => $requestParams['nombre']])
                ->one();

            if (isset($areaModel->id)) {
                throw new HttpException(500, 'El nombre ya se encuentra registrado');
            }
        }

        $requestParams['proyecto_id'] = $proyectoId;
        $requestParams['creado_por'] = Params::getAudit();
        $model->load($requestParams, '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
