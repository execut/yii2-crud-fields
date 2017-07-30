<?php
/**
 */

namespace execut\crudFields\fields;


use detalika\requests\helpers\DateTimeHelper;
use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class Date extends Field
{
    public $isTime = false;
    public $displayOnly = true;
    public function getColumn()
    {
        $widgetOptions = $this->getWidgetOptions();

        return ArrayHelper::merge([
            'filter' => DateRangePicker::widget($widgetOptions),
        ], parent::getColumn());
    }

    public function getField()
    {
        if ($this->displayOnly) {
            return array_merge(parent::getField(), [
                'displayOnly' => true,
            ]);
        }

        if ($this->isTime) {
            $type = DetailView::INPUT_DATETIME;
        } else {
            $type = DetailView::INPUT_DATE;
        }

        return [
            'type' => $type,
            'attribute' => $this->attribute,
            'widgetOptions' => [
                'pluginOptions' => [
                    'format' => $this->getFormat(true),
                    'todayHighlight' => true
                ],
            ],
        ];
    }

    public function applyScopes(ActiveQuery $query)
    {
        $modelClass = $query->modelClass;
        $attribute = $this->attribute;
        $t = $modelClass::tableName();
        $value = $this->model->$attribute;
        if (!empty($value)) {
            $timeFormat = 'H:i:s';
            $dateTimeFormat = 'Y-m-d '. $timeFormat;

            list($from, $to) = explode(' - ', $value);
            if (!$this->isTime) {
                $from = $from . ' 00:00:00';
                $to = $to . ' 23:59:59';
            }

            $fromUtc = self::convertToUtc($from, $dateTimeFormat);
            $toUtc = self::convertToUtc($to, $dateTimeFormat);

            $query->andFilterWhere(['>=', $t . '.' . $attribute, $fromUtc])
                ->andFilterWhere(['<=', $t . '.' . $attribute, $toUtc]);
        }

        return $query;
    }

    public static function convertToUtc($dateTimeStr, $format)
    {
        $dateTime = \DateTime::createFromFormat(
            $format,
            $dateTimeStr,
            self::getApplicationTimeZone()
        );
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime->format($format);
    }

    private static function getApplicationTimeZone()
    {
        return (new \DateTimeZone(\Yii::$app->timeZone));
    }

    /**
     * @return array
     */
    protected function getWidgetOptions(): array
    {
        $format = $this->getFormat();
        $pluginOptions = [
            'locale' => ['format' => $format, 'separator' => ' - ']
        ];

        if ($this->isTime) {
            $pluginOptions = ArrayHelper::merge($pluginOptions, [
                'timePicker' => true,
                'timePickerIncrement' => 15,
            ]);
        }

        $widgetOptions = [
            'attribute' => $this->attribute,
            'model' => $this->model,
            'convertFormat' => true,
            'pluginOptions' => $pluginOptions
        ];
        return $widgetOptions;
    }

    /**
     * @return string
     */
    protected function getFormat($forJs = false): string
    {
        if ($forJs) {
            $format = 'yyyy-mm-dd';
        } else {
            $format = 'Y-m-d';
        }

        if ($this->isTime) {
            if ($forJs) {
                $format .= ' H:i:s';
            } else {
                $format .= ' H:i:s';
            }
        }
        return $format;
    }
}