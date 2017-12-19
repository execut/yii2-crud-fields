<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/24/17
 * Time: 2:23 PM
 */

namespace execut\crudFields;


use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

trait ModelsHelperTrait
{
    public function getStandardFields($exclude = null, $other = null) {
        $helper = new ModelsHelper();
        if ($exclude !== null) {
            $helper->exclude = $exclude;
        }

        if ($other !== null) {
            $helper->other = $other;
        }

        return $helper->getStandardFields();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getStandardBehaviors($fields, $otherFields = []) {
        return ArrayHelper::merge([
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('now()'),
            ],
            'fields' => [
                'class' => Behavior::class,
                'fields' => $fields,
            ],
        ], $otherFields);
    }
}