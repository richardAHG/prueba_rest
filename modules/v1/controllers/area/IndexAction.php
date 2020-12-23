<?php

namespace app\modules\v1\controllers\area;

use Yii;
// use enmodel\iwasi\library\rest\Action;
use yii\data\ActiveDataProvider;
use yii\rest\Action;
use yii\web\BadRequestHttpException;

/**
 * IndexAction implementa el punto final de la API para enumerar varios modelos
 * 
 * @author Richard Huamán <richard21hg92@gmail.com>
 */
class IndexAction extends Action
{
    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $proyecto_ID = Yii::$app->getRequest()->get('proyecto_id', false);

        if (!$proyecto_ID) {
            throw new BadRequestHttpException("Bad Request");
        }

        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        $query = $modelClass::find()
            ->andWhere([
                "estado" => true,
                // "proyecto_id" => $proyecto_ID
            ]);

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }
}
