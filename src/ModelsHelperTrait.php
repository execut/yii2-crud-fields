<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;
use yii\behaviors\TimestampBehavior;
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

    public function getStandardBehaviors($fields, $otherBehaviors = []) {
        return ArrayHelper::merge([
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => date('Y-m-d H:i:s'),
            ],
            Behavior::KEY => [
                'class' => Behavior::class,
                'fields' => $fields,
            ],
        ], $otherBehaviors);
    }
}