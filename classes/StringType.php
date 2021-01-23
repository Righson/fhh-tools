<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 18.11.2017
 * Time: 12:04
 */
namespace classes;


use exceptions\IncorrectValue;

class StringType implements \ArrayAccess
{
    private string $content;

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

            $this->content = $before . $value . $after;
        }
        return $this->content;
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function before($needle) {
        $pos = strpos($this->content, $needle);
        if(is($pos)) {
            return mb_substr($this->content, 0, $pos);
        } else {
            return $this->content;
        }
    }

    public function toString()
    {
        return $this->content;
    }

    public function iter($by=1)
    {
        foreach (str_split($this->content, $by) as $char) {
            yield $char;
        }
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function format(string $string): string
    {
        $tempStr = '';
        $resultStr = '';
        $tmpContent = $this->content;

        foreach (str_split($string) as $char) {
            if ($this->content === '') break;

            if (is_numeric($char)) {
                $tempStr .= $char;
            } else {
                $resultStr .= $this[":$tempStr"] . $char;
                $this->content = $this["$tempStr:"];
                $tempStr = '';
            }
        }

        $resultStr .= $this->content;

        $this->content = $tmpContent;

        return $resultStr;
    }

    public function take(int $chunk)
    {
        if (strlen($this->content) <= $chunk) {
            $ret = $this->content;
            $this->content = '';
        } else {
            $ret = $this[":$chunk"];
            $this->content = $this["$chunk:"];
        }
        return $ret;
    }

    public function length(): int
    {
        return strlen($this->content);
    }

    public function count(string $string): int
    {
        return substr_count($this->content, $string);
    }

    /**
     * @param string $delimiter
     * @param int $take
     * @return array
     * @throws IncorrectValue
     *
     */
    public function split(string $delimiter, int $take=0)
    {
        $t = explode($delimiter, $this->content);

        if(abs($take) > count($t)) throw new IncorrectValue("Out of range");

        if ($take < 0) {
            return $t[count($t) + $take];
        } elseif ( $take > 0) {
            return $t[$take];
        } else {
            return $t;
        }
    }

    public function join(string $joinStr)
    {
        $this->content .= $joinStr;
    }

    /**
     * @param callable $fn
     * @return StringType
     * @throws IncorrectValue
     */
    public function apply(callable $fn): StringType
    {
        $cont = $this->content;
        $res = call_user_func_array($fn,[$cont]);
        if(is_string($res)) {
            return new StringType($res);
        }
        throw new IncorrectValue("apply function returns not a string!");
    }
}