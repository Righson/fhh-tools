<?php
/**
 * Created by PhpStorm.
 * User: fhh
 * Date: 15.03.19
 * Time: 16:22
 */

namespace classes;


class StackType
{
    private $container;

    public function __construct()
    {
        $this->container = [];
    }

    public function push($value)
    {
        $this->container[] = $value;
    }

    public function pop()
    {
        return array_pop($this->container);
    }

    public function isEmpty(): bool
    {
        return count($this->container) == 0;
    }

    public function length(): int
    {
        return count($this->container);
    }
}
