<?php


namespace execut\crudFields\example\controllers;

use execut\crud\params\Crud;
use execut\crudFields\example\models\AllFields;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class AllFieldsController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [$this->module->getAdminRole()],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        $crud = new Crud([
            'modelClass' => AllFields::class,
            'modelName' => AllFields::MODEL_NAME,
        ]);
        return $crud->actions();
    }
}