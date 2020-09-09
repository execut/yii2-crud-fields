<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\models\AllFields;


class MultipleinputVia extends Select2Via
{
    public static function tableName()
    {
        return 'example_all_fields_has_many_multipleinput';
    }
}