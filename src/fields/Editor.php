<?php
/**
 */

namespace execut\crudFields\fields;

use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;
class Editor extends Field
{
    protected $detailViewFieldClass = \execut\crudFields\fields\detailViewField\Editor::class;
    public function getColumn()
    {
        $column = parent::getColumn();
        if ($column === false) {
            return $column;
        }

        return array_merge($column, [
            'value' => function ($row) {
                $attribute = $this->attribute;

                return substr(strip_tags($row->$attribute), 0, 60);
            },
        ]);
    }
}