<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 06.11.2018
 * Time: 11:28
 */

namespace classes;

require_once "../lib/common.php";


use ArrayAccess;

class ArrayType implements ArrayAccess
{
    const SEND_OPT_ARGS = 0;
    const SEND_OPT_FULL = 1;
    const SEND_OPT_KEY = 2;
    protected array $container;
    protected int $pos;

    public function __construct(array $array = [])
    {
        $this->reset();

        if (!empty($array)) {
            $this->container = $array;
        } else {
            $this->container = [];
        }
    }

    public function reset()
    {
        $this->pos = 0;
    }

    /**
     * @param string $newKey
     * @param array ...$pieces
     * @return ArrayType
     */
    public static function newFromArrays(string $newKey, ...$pieces): ArrayType
    {
        $at = new ArrayType();

        foreach ($pieces as $idx => $piece) {
            if (count($piece) === 0) continue;
            if ($idx == 0) {
                $basis = $piece;
                $basisKey = $basis[0][$newKey];

                foreach ($basis as $key => $value) {
                    $at->append([$basisKey => $value]);
                }
                continue;
            }

            $at->join($piece, $newKey);
        }

        return $at;
    }

    public function append($value, $offset = null)
    {
        if (empty($offset)) $offset = count($this->container);

        $this->offsetSet($offset, $value);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            $this->reset();
        }
        $this->container[$offset] = $value;
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function join(array $add, string $key)
    {
        $newCont = array();
        $cntr = 0;

        foreach ($this->container as $idx_ => $contValue) {
            foreach ($add as $idx => $value) {
                $arr = [$value[$key] => $value];
                $newCont[$cntr] = $this->container[$idx_] + $arr;
                $cntr++;
            }
        }

        $this->container = $newCont;
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (is_numeric($offset) && $offset < 0 && abs($offset) < count($this->container)) {
            return $this->container[count($this->container) + $offset];
        }

        if ($this->offsetExists($offset)) {
            return $this->container[$offset];
        } else {
            return false;
        }
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->container[$offset]);
        }
    }

    public function KeyIsEmpty($keys): bool
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (!empty($this->container[$key])) return false;
            }
            return true;
        } else {
            return empty($this->container[$keys]);
        }
    }

    public function unpack(array $from, array $bind): bool
    {
        $on = true;
        $off = false;

        $restMode = $off;
        $restKey = '';

        foreach ($from as $idx => $item) {
            if (!is_numeric($idx)) return false;

            if (!$restMode && isset($bind[$idx]) && starts_with($bind[$idx], '&rest:')) {

                $rest = explode(':', $bind[$idx]);
                if (empty($rest[1])) return false;

                $restKey = $rest[1];
                $restMode = $on;
            }

            if (is_numeric($item)) $item = $item + 0;

            if ($restMode) {
                $this->container[$restKey][] = $item;
                continue;
            }

            if (isset($bind[$idx])) {
                $this->container[$bind[$idx]] = $item;
                continue;
            }

            return false;
        }

        return true;
    }

    public function all($keys = []): bool
    {
        if (empty($keys)) {
            foreach ($this->container as $value) {
                if (empty($value)) return false;
            }
        } else {
            foreach ($keys as $key) {
                if (empty($this->container[$key])) return false;
            }
        }

        return true;
    }

    public function any(...$keys): bool
    {
        if ($this->length() == 0) return true;
        if (empty($keys)) {
            foreach ($this->container as $value) {
                if (!empty($value)) return true;
            }
        } else {
            foreach ($keys as $key) {
                if (isset($this->container[$key]) && $this->container[$key]) return true;
            }
        }

        return false;
    }

    public function sortBy($filed)
    {
        $dataToSort = array_column($this->container, $filed);
        array_multisort($dataToSort, SORT_ASC, $this->container);

        return $this;
    }

    public function get()
    {
        return $this->container;
    }

    public function haveKeys(...$keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->container)) return False;
        }

        return True;
    }

    public function notEmpty(...$keys)
    {
        foreach ($keys as $key) {
            if (empty($key)) return False;
        }

        return True;
    }

    public function sliceK(...$keys): array
    {
        $ret = array();

        foreach ($keys as $key) {
            array_push($ret, $this->container[$key]);
        }

        return $ret;
    }

    public function length(): int
    {
        return count($this->container);
    }

    public function take(int $n)
    {
        $ret = [];

        while ($n > 0) {
            if (isset($this->container[$this->pos])) {
                $item = $this->container[$this->pos];
                if (!test($item)) break;

                array_push($ret, $item);
            }
            $this->pos++;
            $n--;

        }

        return $ret;
    }

    public function drop()
    {
        $this->container = [];
        $this->pos = 0;
    }

    public function apply(array $basis)
    {
        $this->container = $basis;
    }

    public function implode(string $sep = ''): string
    {
        return implode($sep, $this->container);
    }

    public function have($value)
    {
        return in_array($value, $this->container, true);
    }

    public function map(\Closure $closure, $sendArgs = self::SEND_OPT_ARGS)
    {
        $ret = [];
        foreach ($this->container as $idx => $item) {
            switch ($sendArgs) {
                case self::SEND_OPT_ARGS:
                    array_push($ret, $closure($item));
                    break;
                case self::SEND_OPT_FULL:
                    array_push($ret, $closure($idx, $item));
                    break;
                case self::SEND_OPT_KEY:
                    array_push($ret, $closure($idx));
                    break;
                default:
                    return [];
            }
        }

        return $ret;
    }

    public function reduce(\Closure $closure, $target)
    {

        foreach ($this->container as $item) {
            $target = $closure($target, $item);
        }

        return $target;
    }

    public function del($offset)
    {
        if (isset($this->container[$offset])) {
            unset($this->container[$offset]);
            return true;
        }
        $pos = array_search($offset, $this->container);
        unset($this->container[$pos]);
        return true;
    }

    public function slice(int $start, int $end)
    {
        $finish = $end - $start + 1;
        if ($end < 0) $finish = count($this->container) + $end;

        return new ArrayType(array_slice($this->container, $start, $finish));
    }

    public function head()
    {
        $firstK = array_key_first($this->container);
        return new ArrayType($this->container[$firstK]);
    }

    public function values()
    {
        return array_values($this->container);
    }

    public function getDefault(string $key, $default=false)
    {
        if(isset($this->container[$key])) {
            return $this->container[$key];
        }
        return $default;
    }
}