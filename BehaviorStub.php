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
}