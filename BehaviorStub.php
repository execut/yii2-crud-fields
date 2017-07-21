<?php
/**
 */

namespace execut\crudFields;


trait BehaviorStub
{
    public function search() {
        $dp = $this->getBehavior('fields')->search();

        return $dp;
    }

    public function attributeLabels()
    {
        return $this->getBehavior('fields')->attributesLabels();
    }

    public function rules()
    {
        $rules = $this->getBehavior('fields')->rules();

        return $rules;
    }
    
    public function getRelation($name, $throwException = true) {
        $relation = $this->getBehavior('fields')->getRelation($name);
        if ($relation) {
            return $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple']);
        }

        return parent::getRelation($name, $throwException);
    }

    public function __get($name)
    {
        $relation = $this->getBehavior('fields')->getRelation($name);
        if ($relation && !$this->isRelationPopulated($name)) {
            $this->populateRelation($name, $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple'])->findFor($name, $this));
        }

        return parent::__get($name);
    }
}