<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use kartik\detail\DetailView;
class Textarea extends StringField
{
    public $maxLength = false;
    protected $_field = [
        'type' => DetailView::INPUT_TEXTAREA,
    ];
}