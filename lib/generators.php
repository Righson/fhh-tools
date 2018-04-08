<?php
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 17.12.2017
 * Time: 1:41
 */

/**
 * @param string $data
 * @param string $delimiter
 * @param string $pairDelimiter
 * @return Generator
 */
function getPair($data, $delimiter, $pairDelimiter)
{
    $myData = explode($delimiter, $data);
    foreach ($myData as $value) {
        list($key,$val) = explode($pairDelimiter, $value);
        yield $key=>$val;
    }
}