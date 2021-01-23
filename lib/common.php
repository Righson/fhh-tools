<?php


use exceptions\EmptyValue;

function array_repair(array $arr): array
{
    return array_combine(range(0, count($arr) - 1), array_values($arr));
}


/**
 * @param string $msg
 * @param string $errCode
 */
function handleMainError(string $msg, string $errCode)
{
    $errors = [
        '400' => 'HTTP/1.0 400 Bad Request',
        '401' => 'HTTP/1.0 401 Unauthorized',
        '403' => 'HTTP/1.0 403 Forbidden',
        '405' => 'HTTP/1.0 405 Method Not Allowed'
    ];
    if (headers_sent()) {
        header_remove();
    }
    header('Content-Type: application/json');
    header($errors[$errCode], false);
    echo json_encode(['error' => $msg]);
    exit(1);
}

function println($_str)
{
    echo "$_str\n";
}

/**
 * @param $arr
 * @param $key
 * @param bool $default
 * @return mixed
 */
function get_default($arr, $key, $default = false)
{
    // butthurt! почему "0" == false!
    if(!isset($arr[$key])) return $default;

    $testTestValue = $arr[$key];

    switch (gettype($testTestValue)) {
        case 'string':
            return (strlen($arr[$key])) ? $arr[$key] : $default;
        default:
            return (!empty($arr[$key])) ? $arr[$key] : $default;
    }
}

function test($value)
{
    switch (gettype($value)) {
        case 'string':
            return (strlen($value) !== 0);
        case 'integer':
        case 'double':
            return $value !== 0;
        default:
            return !empty($value);
    }
}

function select(array $variants, array $haystack, string $default = ''): string
{
    foreach ($variants as $variant) {
        if (isset($haystack[$variant]) && $haystack[$variant] != '') return $variant;
    }

    return ($default) ?? '';
}

function not_empty($value)
{
    return !empty($value);
}

function smart_backslashes($string): string
{
    return (preg_replace("/([^\\\\])(')/", "$1\\\\$2", $string)) ?? $string;
}

function require_keys($for, ...$keys)
{
    $for_keys = array_keys($for);
    $filter_res = count(array_filter($for_keys, function ($x) use ($keys) {
        return in_array($x, $keys);
    }));

    return $filter_res == count($keys);
}

function all($data)
{
    return count(array_filter($data, function ($x) {
            return test($x);
        })) == count($data);
}

/**
 * @param $data
 * @return bool
 */
function any(array $data)
{
    return (bool)count(array_filter($data, function ($x) {
        return !empty($x);
    }));
}

function booleanToHumanReadable($value)
{
    return ($value) ? "yes" : "no";
}

function os_path(...$path_elements)
{
    return implode(DIRECTORY_SEPARATOR, $path_elements);
}

function is_not($x)
{
    return $x === false;
}

function is($x)
{
    return !($x === false);
}

/**
 * @param array $haystackList
 * @param array $needleList
 * @return array
 */
function filterByArray(array $haystackList, array $needleList)
{
    return array_filter($haystackList, function ($x) use ($needleList) {
        foreach ($needleList as $banElement) {
            if (is(strstr($x, $banElement))) {
                return false;
            }
        }
        return true;
    }
    );
}

function str_replace_lnk($search, $replace, &$subject, $count = null)
{
    $subject = str_replace($search, $replace, $subject, $count);
}

function starts_with($haystack, $needle)
{
    return (substr_compare($haystack, $needle, 0, strlen($needle)) === 0);
}

/**
 * @param $obj
 * @param $name
 * @return mixed
 * @throws EmptyValue
 */
function get_const($obj, $name)
{
    $cnst = get_class($obj) . "::$name";
    if (defined($cnst)) {
        return constant($cnst);
    } else {
        throw new EmptyValue('Undefined constant ' . $cnst);
    }
}

/**
 * @param string $target
 * @param string $delimiters
 * @return string|int|float;
 */
function get_last_exploded_rec($target, $delimiters)
{
    $delimiter = $delimiters[0];
    $arr = explode($delimiter, $target);

    if (strlen($delimiters) > 1) {
        return get_last_exploded_rec($arr[count($arr) - 1], substr($delimiters, 1));
    } else {
        return $arr[count($arr) - 1];
    }
}

function get_last_exploded($target, $delimiter)
{
    $arr = explode($delimiter, $target);
    return $arr[count($arr) - 1];
}

function scanString(string $str, string $explodeBy, array $mapping = []): array
{
    $matches = preg_split("/[" . $explodeBy . "]/", $str);
    if (count($matches) == 0) return $matches;

    $res = [];

    foreach ($matches as $idx => $match) {
        if ($idx >= count($mapping))
            break;
        $res[$mapping[$idx]] = $match;
    }

    return $res;
}

/**
 * Перващает многомерный массив в одномерный
 * @param $arr
 * @return array
 */
function array_flat($arr)
{
    $ret = [];
    if (count($arr)) {

        foreach ($arr as $val) {
            if (is_array($val)) {
                $ret = array_merge($ret, array_flat($val));
            } else {
                array_push($ret, $val);
            }
        }
    }

    return $ret;
}

/**
 * Возвращает процент совпадений в массиве
 * @param $arrA
 * @param $arrB
 * @return float
 */
function array_correlate($arrA, $arrB)
{
    list($searchArray, $haystackArray) = (count($arrA) > count($arrB)) ? [$arrB, $arrA] : [$arrA, $arrB];
    $comp = count(array_filter(array_map(function ($x, $y) {
        return $x == $y;
    }, $searchArray, array_slice($haystackArray, 0, count($searchArray)))));
    return ($comp / count($haystackArray)) * 100.0;
}

function kpop(&$array, $key)
{
    $value = $array[$key];
    unset($array[$key]);
    return $value;
}

function glue_arrays(array $asKey, array $asValue)
{
    return iterator_to_array(fetch_pair($asKey, $asValue));
}
/**
 * @param array $array
 * @param string $key
 * @param string $sep
 * @param bool $quotes
 * @return string
 */
function implodeKeys(array $array, $key, $quotes = true, $sep = ',')
{
    return implode($sep, array_unique(array_map(function ($x) use ($key, $quotes) {
        return ($quotes) ? "'{$x[$key]}'" : "{$x[$key]}";
    }, $array
    )));
}

function implodeValues(array $array, $quotes = true, $sep = ',')
{
    return implode($sep, array_unique(array_map(function ($x) use ($quotes) {
        return ($quotes) ? "'$x'" : "$x";
    }, $array
    )));
}

/**
 * @param array $asKey
 * @param array $asValue
 * @return Generator
 *
 * Возращает генератор => лучше всего засовывать в циклы, например foreach
 */
function fetch_pair(array $asKey, array $asValue)
{
    foreach ($asKey as $indx => $key) {
        if (isset($asValue[$indx])) {
            yield $key => $asValue[$indx];
        }
    }
}

function typeToString($value): string
{
    switch (gettype($value)){
        case 'array':
            $ret = implode(',',array_map(function($k,$v){return "$k=>$v";}, array_keys($value), array_values($value)));
            break;
        default:
            $ret = (string) $value;
            break;
    }

    return $ret;
}


function readStringAsDict(string $delimiter, string $target)
{
    $tick = 0;
    $key = '';

    foreach (explode($delimiter, $target) as $item) {
        if ($tick) {
            $tick = 0;

            yield $key => $item;
            continue;
        }

        $key = $item;
        $tick = 1;
    }
}

