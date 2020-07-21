<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\detail\DetailView;

/**
 * Field for raw data inside DetailView form
 * @package execut\crudFields
 */
class RawField extends Field
{
    /**
     * @var string|callable Raw data
     */
    public $value  = null;

    /**
     * {@inheritDoc}
     */
    public function getField()
    {
        return [
            'type' => DetailView::INPUT_STATIC,
            'value' => $this->value,
            'label' => $this->getLabel(),
            'displayOnly' => true,
            'format' => 'raw',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn()
    {
        return false;
    }
}
