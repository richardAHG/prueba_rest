<?php

namespace app\modules\v1\controllers;

// use enmodel\iwasi\library\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

/**
 * CRUD de las Áreas
 * 
 * @author Richard Huaman <richard21hg92@gmail.com>
 * 
 */
class AreaController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\AreaModel';

    public function actions()
    {
        $actions = parent::actions();

        $actions['create']['class'] = 'app\modules\v1\controllers\area\CreateAction';
        $actions['update']['class'] = 'app\modules\v1\controllers\area\UpdateAction';
        $actions['index']['class'] = 'app\modules\v1\controllers\area\IndexAction';

        return $actions;
    }
}
