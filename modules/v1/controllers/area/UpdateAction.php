<?php

namespace app\modules\v1\controllers\area;

use app\modules\v1\constants\Params;
use Yii;
use yii\base\Model;
use yii\rest\Action;
use yii\web\ServerErrorHttpException;
use yii\web\HttpException;

/**
 * UpdateAction actualizará información de un id en especifico.
 * 
 * Al heredar de la clase Action se ajusta a las restriciones dadas 
 * por la clase padre.
 * 
 * @author Richard Huamán <richard21hg92@gmail.com>
 */
class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is 
     * validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $proyectoId = Params::getProyectoId();

        $requestParams = Yii::$app->getRequest()->getBodyParams();

        if (isset($requestParams['nombre'])) {
            $areaModel = $this->modelClass::find()
                ->where(['proyecto_id' => $proyectoId])
                ->andWhere(['estado' => true])
                ->andWhere(['nombre' => $requestParams['nombre']])
                ->andWhere(['NOT',['id'=> $id]])
                ->one();

            if (isset($areaModel->id)) {
                throw new HttpException(500, 'El nombre ya se encuentra registrado');
            }
        }

        $model->scenario = $this->scenario;
        $requestParams['actualizado_por'] = Params::getAudit();
        $model->load($requestParams, '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
