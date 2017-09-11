<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 9/7/17
 * Time: 5:48 PM
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;

class Group extends Field
{
    public function getField()
    {
        return [
            'group'=>true,
            'label'=> $this->getLabel(),
            'rowOptions'=>['class'=>DetailView::TYPE_SUCCESS]
        ];
    }

    public function getColumn()
    {
        return false;
    }
}