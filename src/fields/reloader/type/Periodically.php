<?php


namespace execut\crudFields\fields\reloader\type;


use execut\crudFields\fields\reloader\TypeInterface;

class Periodically implements TypeInterface
{
    public function getKey(): string
    {
        return 'periodically';
    }
}