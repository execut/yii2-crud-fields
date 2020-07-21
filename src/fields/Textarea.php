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
 * Field for textarea input
 * @package execut\crudFields\fields
 */
class Textarea extends StringField
{
    /**
     * @var integer Max text length. False if infinity
     */
    public $maxLength = false;
    /**
     * {@inheritDoc}
     */
    protected $_field = [
        'type' => DetailView::INPUT_TEXTAREA,
    ];
}
