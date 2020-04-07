<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon;


use yii\helpers\ArrayHelper;

class AddonArray
{
    protected $content = null;
    protected $options = [];

    public function __construct($options = [], $content = null)
    {
        $this->setContent($content);
        $this->setOptions($options);
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param null $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getConfig() {
        return [
            'append' => [
                'options' => $this->getOptions(),
                'content' => $this->getContent(),
            ],
        ];
    }
}