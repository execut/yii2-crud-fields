<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\backend;

use execut\crud\params\Crud;
use execut\crudFields\models\AllFields;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Class for rendering execut/yii2-crud-fields demo
 * @package execut\books
 */
class FieldsController extends Controller
{
    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function actions()
    {
        $crud = new Crud([
            'modelClass' => AllFields::class,
            'modelName' => AllFields::MODEL_NAME,
        ]);
        return $crud->actions();
    }
}
