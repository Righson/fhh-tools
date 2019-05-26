<?php
namespace classes\utils\types;
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 2019-03-07
 * Time: 23:14
 */

class GroupedListType extends \classes\utils\ArrayType
{
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            array_push($this->container[$offset], $value);
            return;
        }
        $this->container[$offset] = array();
        $this->offsetSet($offset, $value);
        return;
    }
}