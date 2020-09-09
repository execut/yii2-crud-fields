<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\models;

use execut\crudFields\models\AllFields\MultipleinputVia;
use execut\crudFields\models\AllFields\Nested;
use execut\crudFields\models\AllFields\Select2Via;
use execut\crudFields\Behavior;
use execut\crudFields\BehaviorStub;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Group;
use execut\crudFields\fields\HasManyMultipleInput;
use execut\crudFields\fields\HasManySelect2;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\fields\Id;
use execut\crudFields\fields\reloader\Reloader;
use execut\crudFields\fields\reloader\Target;
use execut\crudFields\fields\reloader\type\Dependent;
use execut\crudFields\fields\reloader\type\Periodically;
use execut\crudFields\fields\StringField;
use execut\crudFields\ModelsHelperTrait;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\db\ActiveRecord;

/**
 * AllFields model
 * @package execut\books
 */
class AllFields extends ActiveRecord
{
    use BehaviorStub, ModelsHelperTrait;

    /**
     * Model name label for translations
     */
    const MODEL_NAME = '{n,plural,=0{All fields} =1{All field} other{All fields}}';

    public $hasManyMultipleinput_hasRelation = null;

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'fields' => [
                'class' => Behavior::class,
                Behavior::RELATIONS_SAVER_KEY => [
                    'class' => SaveRelationsBehavior::class,
                    'relations' => [
                        'select2Via',
//                        'hasManySelect2Via',
                    ],
                ],
                'fields' => $this->getFields()
            ],
        ];
    }

    protected function getFields()
    {
        $hasOneField = new HasOneSelect2([
            'attribute' => 'has_one_id',
            'relation' => 'hasOne',
            'relationQuery' => $this->hasOne(self::class, [
                'id' => 'has_one_id',
            ]),
            'orderByAttribute' => false,
            'url' => [
                '/crudFields/fields',
            ],
        ]);

        $type = new Dependent();

        $target = new Target($hasOneField);
        $target->setValues([function () {
            return $this->findRecordForUpdateWhenSpecificValueSelected();
        }]);
        $onChangeSpecificValueReloader = new Reloader($type, [
            $target,
        ]);

        $target = new Target($hasOneField);
        $onChangeReloader = new Reloader($type, [
            $target,
        ]);

        $target = new Target($hasOneField);
        $target->setWhenIsEmpty(true);
        $onChangeEmptyReloader = new Reloader($type, [
            $target,
        ]);

        $target = new Target($hasOneField);
        $target->setWhenIsEmpty(false);
        $onChangeNotEmptyReloader = new Reloader($type, [
            $target,
        ]);

        $t = 'Updated Fields';
        if (YII_ENV === 'unit_test') {
            $t = \yii::t('execut/books', $t);
        }

        return [
            'id' => [
                'class' => Id::class,
            ],
            'name' => [
                'class' => StringField::class,
                'required' => true,
                'attribute' => 'name',
            ],
            'bool' => [
                'class' => Boolean::class,
                'attribute' => 'bool',
            ],
            'hasOne' => $hasOneField,
            'periodically_updated' => [
                'attribute' => 'periodically_updated',
                'field' => [
                    'displayOnly' => true,
                    'attribute' => 'periodically_updated',
                    'value' => function () {
                        return date('Y-m-d H:i:s') . '. Flag ' . ($this->bool ? 'yes' : 'no');
                    },
                ],
                'reloaders' => [new Reloader(new Periodically())],
            ],
            'periodicallyUpdatedWidget' => [
                'class' => HasOneSelect2::class,
                'attribute' => 'periodically_updated_widget_id',
                'relation' => 'periodicallyUpdatedWidget',
                'relationQuery' => $this->hasOne(self::class, [
                    'id' => 'periodically_updated_widget_id',
                ]),
                'orderByAttribute' => false,
                'reloaders' => [new Reloader(new Periodically())],
            ],
            'record_for_update_when_a_specific_value_selected' => [
                'class' => Boolean::class,
                'attribute' => 'record_for_update_when_a_specific_value_selected',
            ],
            'updated_fields' => [
                'class' => Group::class,
                'label' => $t,
            ],
            'change_updated' => [
                'class' => Date::class,
                'attribute' => 'change_updated',
                'isTime' => true,
                'defaultValue' => date('Y-m-d H:i:s'),
                'reloaders' => [$onChangeReloader],
            ],
            'specific_value_selected_updated' => [
                'class' => Date::class,
                'attribute' => 'specific_value_selected_updated',
                'isTime' => true,
                'defaultValue' => date('Y-m-d H:i:s'),
                'reloaders' => [$onChangeSpecificValueReloader],
            ],
            'empty_updated' => [
                'class' => Date::class,
                'attribute' => 'empty_updated',
                'isTime' => true,
                'defaultValue' => date('Y-m-d H:i:s'),
                'reloaders' => [$onChangeEmptyReloader],
            ],
            'not_empty_updated' => [
                'class' => Date::class,
                'attribute' => 'not_empty_updated',
                'isTime' => true,
                'defaultValue' => date('Y-m-d H:i:s'),
                'reloaders' => [$onChangeNotEmptyReloader],
            ],
            'hasManySelect2' => [
                'class' => HasManySelect2::class,
                'attribute' => 'hasManySelect2',
                'relation' => 'hasManySelect2',
                'relationQuery' => $this->hasMany(self::class, ['has_many_select2_id' => 'id']),
                'url' => [
                    '/crudFields/fields'
                ],
            ],
            'hasManySelect2Via' => [
                'class' => HasManySelect2::class,
                'attribute' => 'hasManySelect2Via',
                'relation' => 'hasManySelect2Via',
                'relationQuery' => $this->hasMany(self::class, ['id' => 'example_all_field_to_id'])
                    ->via('select2Via'),
                'url' => [
                    '/crudFields/fields'
                ],
            ],
            'hasManyMultipleinput' => [
                'class' => HasManyMultipleInput::class,
                'attribute' => 'hasManyMultipleinput',
                'relation' => 'hasManyMultipleinput',
                'isGridForOldRecords' => true,
                'relationQuery' => $this->hasMany(Nested::class, ['has_many_multipleinput_id' => 'id']),
            ],
            'hasManyMultipleinputVia' => [
                'class' => HasManyMultipleInput::class,
                'attribute' => 'hasManyMultipleinputVia',
                'relation' => 'hasManyMultipleinputVia',
                'relationQuery' => $this->hasMany(Nested::class, ['id' => 'example_all_field_to_id'])
                    ->via('multipleinputVia'),
                'url' => [
                    '/crudFields/fields'
                ],
            ],
        ];
    }

    public function getSelect2Via()
    {
        return $this->hasMany(Select2Via::class, ['example_all_field_from_id' => 'id']);
    }

    public function getMultipleinputVia()
    {
        return $this->hasMany(MultipleinputVia::class, ['example_all_field_from_id' => 'id']);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors); // TODO: Change the autogenerated stub
    }

    /**
     * Returns record for update when specific value selected
     */
    public function findRecordForUpdateWhenSpecificValueSelected()
    {
        return self::find()->andWhere('record_for_update_when_a_specific_value_selected')->select('id')->scalar();
    }

    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'example_all_fields';
    }
}
