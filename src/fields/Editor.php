<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

/**
 * WYSIWYG HTML editor field
 * @package execut\crudFields\fields
 */
class Editor extends Field
{
    /**
     * {@inheritdoc}
     */
    protected $detailViewFieldClass = \execut\crudFields\fields\detailViewField\Editor::class;

    /**
     * {@inheritdoc}
     */
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
