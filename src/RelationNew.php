<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/13/19
 * Time: 11:41 AM
 */

namespace execut\crudFields;


use execut\crudFields\relation\DeleteModel;
use yii\base\BaseObject;

class RelationNew extends BaseObject
{
    public $name = null;
    public $owner = null;
    public $uniqueKeys = [];
    public function getLabel() {
        return $this->owner->getAttributeLabel($this->name);
    }
    public function getDeleteModel() {
        $deleteModel = new DeleteModel([
            'label' => $this->getLabel(),
            'is_delete' => false,
        ]);

        return $deleteModel;
    }
}