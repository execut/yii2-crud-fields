<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/24/17
 * Time: 2:23 PM
 */

namespace execut\crudFields;


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
}