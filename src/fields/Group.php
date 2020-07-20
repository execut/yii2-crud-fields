<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;
class Group extends Field
{
    public $scope = false;
    protected function getDetailViewFieldConfig()
    {
        $config = parent::getDetailViewFieldConfig();
        return ArrayHelper::merge([
            'group'=>true,
            'label'=> $this->getLabel(),
            'rowOptions'=>['class'=>DetailView::TYPE_SUCCESS]
        ], $config);
    }

    public function getColumn()
    {
        return false;
    }

    public function getDisplayOnly() {
        return true;
    }
}