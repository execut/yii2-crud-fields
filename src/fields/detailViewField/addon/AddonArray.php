<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon;

/**
 * Wrapper for kartik-v ActiveField addons
 * @see https://demos.krajee.com/widget-details/active-field#option-addon
 * @package execut\crudFields\fields\detailViewField\addon
 */
class AddonArray implements AddonInterface
{
    /**
     * @var string Addon text content
     */
    protected ?string $content = null;
    /**
     * @var array Addon options
     */
    protected array $options = [];

    /**
     * AddonArray constructor.
     * @param array $options Options for addon
     * @param string|null $content Addon text content
     */
    public function __construct(array $options = [], string $content = null)
    {
        $this->setContent($content);
        $this->setOptions($options);
    }

    /**
     * Get addon content value
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set addon content value
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Get addon options
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set addon options
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [
            'append' => [
                'options' => $this->getOptions(),
                'content' => $this->getContent(),
            ],
        ];
    }
}
