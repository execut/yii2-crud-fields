<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/24/17
 * Time: 2:09 PM
 */

namespace execut\crudFields;


use execut\crudFields\fields\Action;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\Id;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class ModelsHelper extends Component
{
    public $standardFieldsDefault = [
        'id' => [
            'class' => Id::class,
        ],
        'visible' => [
            'class' => Boolean::class,
            'attribute' => 'visible',
        ],
        'name' => [
            'class' => Field::class,
            'attribute' => 'name',
            'required' => true,
        ],
        'created' => [
            'class' => Date::class,
            'attribute' => 'created',
        ],
        'updated' => [
            'class' => Date::class,
            'attribute' => 'updated',
        ],
        'actions' => [
            'class' => Action::class,
        ],
    ];

    public $exclude = [];
    public $other = [];

    public function getStandardFields() {
        $fields = $this->standardFieldsDefault;
        foreach ($this->exclude as $key) {
            unset($fields[$key]);
        }

        $fields = ArrayHelper::merge($fields, $this->other);

        return $fields;
    }
}