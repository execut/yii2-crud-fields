<?php
/**
 */

namespace execut\crudFields;


trait BehaviorStub
{
    public function search() {
        return $this->getBehavior('fields')->search();
    }

    public function rules()
    {
        return $this->getBehavior('fields')->rules();
    }
    
    public function getRelation($name, $throwException = true) {
        $relation = $this->getBehavior('fields')->getRelation($name);
        if ($relation) {
            return $this->createRelationQuery($relation['class'], $relation['link'], $relation['multiple']);
        }

        return parent::getRelation($name, $throwException);
    }
}