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

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), $this->getBehavior('fields')->attributesLabels());
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
            $this->populateRelation($name, $this->getRelation($name)->findFor($name, $this));
        }

        return parent::__get($name);
    }
}