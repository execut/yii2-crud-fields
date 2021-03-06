<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use execut\crudFields\widgets\RadioListWithSubform;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

/**
 * Radio list field for has one relations
 * @package execut\crudFields
 */
class HasOneRadioList extends HasOneSelect2
{
    /**
     * {@inheritdoc}
     */
    public $isRenderRelationFields = true;
    /**
     * @var array RadioListWithSubform widget options
     */
    public $fieldWidgetOptions = [];

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        if ($this->_field === false) {
            return false;
        }

        $data = $this->getRelationObject()->getData(true);
        unset($data['']);
        if (empty($data)) {
            return false;
        }

        if ($this->getDisplayOnly()) {
            return parent::getField();
        }

        if (count($data) == 1) {
            $value = key($data);
        } else {
            $value = null;
        }

        if ($this->isRenderRelationFields) {
            $data[''] = 'Новый автомобиль';
        }

        return [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $this->attribute,
            'widgetOptions' => ArrayHelper::merge([
                'class' => RadioListWithSubform::class,
                'clientOptions' => [
                    'relatedSelector' => 'tr:has(.related-' . $this->relation . ')',
                    'value' => $value,
                ],
                'data' => $data,
            ], $this->fieldWidgetOptions),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFields($isWithRelationsFields = true)
    {
        $field = $this->getField();
        if ($field !== false) {
            $fields = [$this->attribute => $field];
        } else {
            $fields = [];
        }

        $fields = array_merge($fields, parent::getFields());

        return $fields;
    }
}
