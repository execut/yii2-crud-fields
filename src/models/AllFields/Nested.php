<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\models\AllFields;


use execut\crudFields\models\AllFields;

class Nested extends AllFields
{
    protected function getFields()
    {
        $fields = parent::getFields();
        unset($fields['hasManyMultipleinput']);
        unset($fields['hasManyMultipleinputVia']);

        return $fields;
    }
}