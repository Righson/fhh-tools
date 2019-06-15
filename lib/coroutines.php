<?php

use exceptions\IncorrectValue;

require __DIR__ . "/../exceptions/IncorrectValue.php";
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 24.04.2018
 * Time: 14:54
 */

/* -- Источники -- */

/**
 * @param array $source
 * @param Generator $gn
 */
function source(array $source, Generator $gn)
{
    foreach ($source as $item) {
        $gn->send($item);
    }
}

function sourceKV(array $source, Generator $gn)
{
    foreach ($source as $key => $item) {
        $gn->send([$key, $item]);
    }
}

/* -- Стоки -- */

/**
 * @param Generator $gn
 * @return Generator
 */
function genQuotes(Generator $gn)
{
    while (true) {
        $data = yield;
        $gn->send("_$data");
    }
}

function genExplode(string $delimiter, Generator $gn)
{
    while (true) {
        $data = yield;
        foreach (explode($delimiter, $data) as $element) {
            $gn->send($element);
        }
    }
}

function genWrap(string $wrap, Generator $gn)
{
    while (true) {
        $data = yield;
        $gn->send("{$wrap}$data{$wrap}");
    }
}

function implodeKV(string $glue, bool $filleted, Generator $gn)
{
    while (true) {
        list($k, $v) = yield;
        if ($filleted && $v == '') {
            continue;
        }
        $gn->send($k . $glue . $v);
    }
}

function filtererKV(callable $predicate, Generator $gn)
{
    while (true) {
        list($k, $v) = yield;
        if ($predicate($k, $v)) {
            $gn->send([$k, $v]);
        }
    }
}

function mapEach(callable $fn, Generator $gn)
{
    while (true) {
        list($idx, $data) = yield;

        $gn->send([$idx, $fn($data)]);
    }
}

function mapperKV(callable $fn, Generator $gn)
{
    while (true) {
        list($k, $v) = yield;

        $gn->send([$k, $fn($v)]);
    }
}

function broadMapperKV(array $fns, Generator $gn)
{
    while (true) {
        list($idx, $data) = yield;

        $send = [];

        foreach ($data as $key => $value) {
            $ret = $value;

            if (isset($fns[$key]) and is_callable($fns[$key])) {
                $ret = $fns[$key]($value);
            }

            $send[$key] = $ret;
        }

        $gn->send([$idx, $send]);
    }
}

/**
 * @param $element
 * @param Generator $gn
 * @return Generator
 * @throws IncorrectValue
 */
function sendElement($element, \Generator $gn)
{
    while (true) {
        $data = yield;
        if (is_array($data)) {
            $gn->send($data[$element]);
        } else {
            throw new IncorrectValue('Array exepted');
        }
    }
}

function sendElementWithMapFnResultKV(string $element, callable $fn, \Generator $gn)
{
    while (true) {
        list($idx, $data) = yield;

        $data[$element] = $fn($data);

        $gn->send([$idx, $data]);
    }
}

/* -- Приемники -- */

function printer()
{
    while (true) {
        println(yield);
    }
}

/**
 * @param array $arr
 */
function pusher(array &$arr)
{
    while (true) {
        array_push($arr, yield);
    }
}

function counter(&$int)
{
    while (true) {
        yield;
        $int++;
    }
}

function joinString(&$string, $glue)
{
    while (true) {
        if (!$string) $string = yield;
        else $string .= $glue . yield;
    }
}

/**
 * @param array $arr
 */
function pusherByKey(array &$arr)
{
    while (true) {
        list($key, $value) = yield;
        $arr[$key] = $value;
    }
}

function pusherByMap($map, array &$arr, $useIndex = false)
{
    while (true) {
        list($idx, $data) = yield;

        $first = null;
        $second = null;


        foreach ($data as $key => $value) {
            $mapKeys = [];

            foreach ($map as $mKey => $mValue) {

                if (in_array($key, $mValue)) {
                    $mapKeys[] = $mKey;
                }
            }

            if (count($mapKeys)) {

                if (isset($map[':alias'])) {
                    foreach ($map[':alias'] as $oldKey => $alias) {
                        if ($key == $oldKey) {
                            $key = $alias;
                            break;
                        }
                    }
                }

                foreach ($mapKeys as $mapKey) {

                    if (in_array($mapKey, [':alias', ':defaults'])) continue;

                    list($first, $second) = setPairs($useIndex, $mapKey, $idx);
                    $arr = insertValue($arr, $first, $second, $key, $value);
                }

            }
        }
        if (all([(string) $first, (string) $second, isset($map[':defaults'])])) {

            foreach ($map[':defaults'] as $defaultKey => $defaultValue) {
                $arr = insertValue($arr, $first, $second, $defaultKey, $defaultValue);
            }
        }
    }
}


function printerKV()
{
    while (true) {
        $data = yield;
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                println("~> $key => ");
                print_r($val);
            } else {
                println("~> $key => $val");
            }
        }
    }
}

# helpers
/**
 * helper
 * @param $arr
 * @param $first
 * @param $second
 * @param $key
 * @param $value
 * @return mixed
 */
function insertValue($arr, $first, $second, $key, $value)
{
    if (!isset($arr[$first][$second])) $arr[$first][$second] = [];
    if (!isset($arr[$first][$second][$key])) $arr[$first][$second][$key] = '';

    $arr[$first][$second][$key] = $value;
    return $arr;
}

/**
 * @param $useIndex
 * @param $mapKey
 * @param $idx
 * @return array
 */
function setPairs($useIndex, $mapKey, $idx): array
{
    $first = $mapKey;
    $second = $idx;
    if ($useIndex) {
        $first = $idx;
        $second = $mapKey;
    }
    return array($first, $second);
}

