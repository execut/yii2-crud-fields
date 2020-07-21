<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\detail\DetailView;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;

/**
 * Field for hidden html input
 * @package execut\crudFields
 */
class Hidden extends Field
{
    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        return ArrayHelper::merge(parent::getField(), [
            'type' => DetailView::INPUT_HIDDEN,
            'rowOptions' => [
                'class' => 'hidden kv-edit-hidden kv-view-hidden',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleInputField()
    {
        return ArrayHelper::merge(parent::getMultipleInputField(), [
            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return false;
    }
}
