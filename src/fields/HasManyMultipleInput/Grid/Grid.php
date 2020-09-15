<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\HasManyMultipleInput\Grid;

use execut\crudFields\Relation;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecordInterface;

interface Grid
{
    public function render(Relation $relation, ActiveRecordInterface $model): ?string;
}
