<?php


namespace execut\crudFields\fields\reloader;


use execut\crudFields\fields\ReloaderInterface;
use execut\crudFields\fields\reloader\Target;

class Reloader implements ReloaderInterface
{
    protected $type = null;
    protected $targets = null;
    public function __construct(TypeInterface $type, array $targets = [])
    {
        $this->type = $type;
        $this->targets = $targets;
    }

    /**
     * @return null
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @param null $target
     */
    public function setTargets($target): void
    {
        $this->targets = $target;
    }

    public function getKey():string {
        return $this->type->getKey();
    }

    public function getType() {
        return $this->type;
    }
}