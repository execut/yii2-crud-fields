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
 * Field for rendering DetailView::INPUT_RADIO_BUTTON_GROUP input
 * @package execut\crudFields
 */
class RadiobuttonGroup extends DropDown
{
    /**
     * {@inheritDoc}
     */
    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        $field['items'] = array_filter($field['items']);

        return ArrayHelper::merge($field, [
            'type' => DetailView::INPUT_RADIO_BUTTON_GROUP,
        ]);
    }
}
