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

/**
 * Field for grouping form fields
 * @package execut\crudFields\fields
 */
class Group extends Field
{
    /**
     * {@inheritdoc}
     */
    public $scope = false;
    /**
     * {@inheritdoc}
     */
    protected function getDetailViewFieldConfig()
    {
        $config = parent::getDetailViewFieldConfig();
        return ArrayHelper::merge([
            'group'=>true,
            'label'=> $this->getLabel(),
            'rowOptions'=>['class'=>DetailView::TYPE_SUCCESS]
        ], $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayOnly()
    {
        return true;
    }
}
