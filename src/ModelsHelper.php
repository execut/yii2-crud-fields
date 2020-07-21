<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use execut\crudFields\fields\Action;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Id;
use execut\crudFields\fields\StringField;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class ModelsHelper
 * @package execut\crudFields
 */
class ModelsHelper extends Component
{
    public $standardFieldsDefault = [
        'id' => [
            'class' => Id::class,
            'order' => -20,
            'column' => [
                'visible' => false,
            ],
        ],
        'visible' => [
            'class' => Boolean::class,
            'order' => 75,
            'attribute' => 'visible',
            'defaultValue' => true,
        ],
        'name' => [
            'class' => StringField::class,
            'order' => -10,
            'attribute' => 'name',
            'required' => true,
        ],
        'created' => [
            'class' => Date::class,
            'order' => 80,
            'attribute' => 'created',
            'isTime' => true,
            'column' => [
                'visible' => false,
            ],
        ],
        'updated' => [
            'class' => Date::class,
            'order' => 90,
            'attribute' => 'updated',
            'isTime' => true,
            'column' => [
                'visible' => false,
            ],
        ],
        'actions' => [
            'class' => Action::class,
            'view' => false,
        ],
    ];

    public $exclude = [];
    public $other = [];

    public function getStandardFields()
    {
        $fields = $this->standardFieldsDefault;
        foreach ($this->exclude as $key) {
            unset($fields[$key]);
        }

        $other = $this->other;
//        foreach ($other as $key => &$value) {
//            if (is_array($value) && empty($value['attribute'])) {
//                $value['attribute'] = $key;
//            }
//        }

        $fields = ArrayHelper::merge($fields, $other);

        return $fields;
    }
}
