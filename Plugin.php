<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/28/17
 * Time: 1:46 PM
 */

namespace execut\crudFields;


interface Plugin
{
    public function getFields();
    public function rules();
    public function getRelations();
}