<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\relation;
use yii\base\Model;
class DeleteModel extends Model
{
    public $label = null;
    public $is_delete = null;
    public function rules()
    {
        return [
            ['is_delete', 'safe'],
        ];
    }
}