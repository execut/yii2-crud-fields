<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help\text;

use execut\crudFields\fields\detailViewField\addon\help\Text;
use yii\helpers\Html;

/**
 * Text for displaying the description of the list of variables
 * @package execut\crudFields
 */
class VarsList implements Text
{
    /**
     * @var string Text before vars list
     */
    protected string $info;
    /**
     * @var string[] Variables list for rendering. Key-value array, where key - variable, value - description
     */
    protected array $varsList;

    /**
     * VarsList constructor.
     * @param string $info Text before vars list
     * @param array $varsList Variables list for rendering. Key-value array, where key - variable, value - description
     */
    public function __construct(string $info, array $varsList)
    {
        $this->info = $info;
        $this->varsList = $varsList;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $varsListParts = [];
        foreach ($this->varsList as $key => $description) {
            $varsListParts[] = $key . ' - ' . $description;
        }

        $helpText = $this->info . Html::ul($varsListParts);

        return $helpText;
    }

    /**
     * Return info value
     * @return string
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * Return vars list value
     * @return string[]
     */
    public function getVarsList() {
        return $this->varsList;
    }
}