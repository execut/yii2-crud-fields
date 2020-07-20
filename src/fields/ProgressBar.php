<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use yii\bootstrap\Progress;
class ProgressBar extends Field
{
    public $totalCountAttribute = null;
    public $currentCountAttribute = null;
    public $asWidget = false;
    public $displayOnly = true;
    public $scope = false;
    public function getColumn()
    {
        $column = parent::getColumn(); // TODO: Change the autogenerated stub
        $column['value'] = function($row) {
            $totalCountAttribute = $this->totalCountAttribute;
            $currentCountAttribute = $this->currentCountAttribute;
            if (!$row->$totalCountAttribute) {
                return;
            }

            return round($row->$currentCountAttribute / $row->$totalCountAttribute * 100) . '%';
        };
        $column['filter'] = false;

        return $column;
    }

    public function getField()
    {
        $column = parent::getField(); // TODO: Change the autogenerated stub
        $column['value'] = function() {
            $row = $this->model;
            $totalCountAttribute = $this->totalCountAttribute;
            $currentCountAttribute = $this->currentCountAttribute;
            if (!$row->$totalCountAttribute) {
                return '-';
            }

            $percent = round($row->$currentCountAttribute / $row->$totalCountAttribute * 100);
            if ($this->asWidget) {
                return Progress::widget([
                    'id' => 'progressbar-' . $this->attribute,
                    'percent' => $percent,
                    'label' => $percent . '%',
                ]);
            }

            return $percent . '%';
        };

        $column['format'] = 'raw';

        return $column;
    }

    protected function initDetailViewField(DetailViewField $field)
    {
        $field->setDisplayOnly(true);
    }
}