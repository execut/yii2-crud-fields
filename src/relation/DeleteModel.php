<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/13/19
 * Time: 11:53 AM
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