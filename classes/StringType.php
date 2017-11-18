<?php
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 18.11.2017
 * Time: 12:04
 */



class StringType implements ArrayAccess
{
    private $content;

    public function __construct($value)
    {
        $this->content = "$value";
    }

    public function offsetExists($offset)
    {
        $offsetInt = (int) $offset;
        return mb_strlen($this->content) > $offsetInt;
    }

    /**
     * @param mixed $offset
     * @return bool|string
     */
    public function offsetGet($offset)
    {
        if (is_array($offset) && count($offset) == 2) {
            return mb_substr($this->content, $offset[0], $offset[1]);
        } elseif (is_string($offset)) {
            list($start, $length) = explode(':', $offset);
            $start = (int) $start;
            $length = (int) $length;
            return ($length) ? mb_substr($this->content, $start, $length - $start) : mb_substr($this->content, $start);
        } else {
            $offsetInt = (int) $offset;
            return mb_substr($this->content, $offsetInt, 1);
        }
    }

    public function offsetSet($offset, $value)
    {
        $offsetInt = (int)$offset;
        if (is_integer($offset)) {
            $length = mb_strlen($this->content);
            $before = ($offsetInt) ? mb_substr($this->content, 0, $offsetInt) : '';
            $after = ($offsetInt == $length) ? '' : mb_substr($this->content, $offsetInt + 1);

            $this->content = $before . $value . $after;
        } elseif (is_string($offset)) {
            list($start, $length) = explode(':', $offset);
            $start = (int) $start;
            $length = (int) $length;

            $before = ($start) ? mb_substr($this->content, 0, $start) : '';
            $after = ($length) ? mb_substr($this->content, $length) : '';

            # print_r([$start, $length, $value]);

            $this->content = $before . $value . $after;
        }
        return $this->content;
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function __toString()
    {
        return $this->content;
    }
}