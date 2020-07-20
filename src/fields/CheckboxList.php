<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use execut\iconsCheckboxList\IconsCheckboxList;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;
class CheckboxList extends HasManySelect2
{
    protected static $icons = [
        'phone' => 'glyphicon glyphicon-phone-alt',
        'whatsapp' => 'fa fa-whatsapp',
        'email' => 'glyphicon glyphicon-envelope',
        'sms' => 'glyphicon glyphicon-phone',
        'cabinet' => 'glyphicon glyphicon-user',
    ];

    public function getField() {
        $items = $this->getItems();
        return [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $this->attribute,
            'label' => $this->getLabel(),
            'format' => 'raw',
            'value' => '',
            'widgetOptions' => [
                'class' => IconsCheckboxList::class,
                'items' => $items,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getItems(): array
    {
        $models = $this->getRelationObject()->getRelatedModels();
        $items = ArrayHelper::map($models, 'id', function ($model) {
            $icon = self::$icons[$model->key];
            return [
                'name' => $model->name,
                'iconClass' => $icon,
            ];
        });
        return $items;
    }
}