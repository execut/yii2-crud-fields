<?php
/**
 */

namespace execut\crudFields;


abstract class Plugin
{
    /**
     * @var Behavior
     */
    public $owner = null;
    public function getFields() {
        return [];
    }

    public function getRelations() {
        return [];
    }
}