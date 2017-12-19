<?php
/**
 */

namespace execut\crudFields;


use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

trait BehaviorStub
{
    public function search() {
        $dp = $this->getBehavior('fields')->search();

        return $dp;
    }

    protected function attributesLabelsStub() {
        return [];
    }

    public function attributeLabels()
    {
        $result = ArrayHelper::merge(parent::attributeLabels(), $this->getBehavior('fields')->attributesLabels(), $this->attributesLabelsStub());

        return $result;
    }

    public function rules()
    {
        $rules = ArrayHelper::merge(parent::rules(), $this->getBehavior('fields')->rules());

        return $rules;
    }
    
    public function getRelation($name, $throwException = true) {
        $relation = $this->getBehavior('fields')->getRelation($name);
        if ($relation) {
            /**
             * @var ActiveQuery $query
             */
            $query = $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple']);
            if (!empty($relation['via'])) {
                $query->via($relation['via']);
            } else if (!empty($relation['viaTable'])) {
                 $query->viaTable($relation['viaTable'], $relation['viaLink']);
            }

            return $query;
        }

        return parent::getRelation($name, $throwException);
    }

    public function __get($name)
    {
        $relation = $this->getBehavior('fields')->getRelation($name);
        if ($relation && !$this->isRelationPopulated($name)) {
            $relation = $this->getRelation($name);
            $relation = $relation->findFor($name, $this);
            $this->populateRelation($name, $relation);
        }

        return parent::__get($name);
    }

    public function getRowOptions() {
        return $this->getBehavior('fields')->getRowOptions();
    }

//    protected $_formName = null;
//
//    public function setFormName($formName) {
//        $this->_formName = $formName;
//
//        return $this;
//    }
//
//    public function formName() {
//        return $this->_formName;
//    }

//    public function attributes() {
//        $result = [];
//        foreach ($this->getBehavior('fields')->getFields() as $field) {
//            if ($field->attribute) {
//                $result[] = $field->attribute;
//            }
//        }
//
//        return $result;
//    }
}