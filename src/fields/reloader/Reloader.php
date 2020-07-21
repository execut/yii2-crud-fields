<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\reloader;

use execut\crudFields\fields\ReloaderInterface;

/**
 * Reloader for DetailView fields
 * @package execut\crudFields\fields\reloader
 */
class Reloader implements ReloaderInterface
{
    /**
     * @var TypeInterface Reloader type
     */
    protected TypeInterface $type;

    /**
     * @var Target[] Targets list for reload
     */
    protected array $targets = [];

    /**
     * Reloader constructor
     * @param TypeInterface $type Type interface
     * @param array $targets Targets list for reload
     */
    public function __construct(TypeInterface $type, array $targets = [])
    {
        $this->type = $type;
        $this->targets = $targets;
    }

    /**
     * Returns targets list
     * @return Target[]
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Set reloader targets
     * @param Target[] $targets
     */
    public function setTargets($targets): void
    {
        $this->targets = $targets;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey():string
    {
        return $this->type->getKey();
    }
    /**
     * Returns reloader type instance
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }
}
