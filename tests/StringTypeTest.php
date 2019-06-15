<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 05.10.2018
 * Time: 12:40
 */

namespace tests\utils;

require __DIR__ . "/../classes/StringType.php";

use classes\StringType;
use PHPUnit\Framework\TestCase;


class StringTypeTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testFormat()
    {
        $str = new StringType('17117593454');
        $this->assertEquals('17 11 7 593 454', $str->format('2 2 1 3 3'));

        $str = new StringType('17117593');
        $this->assertEquals('17 11 7 593 ', $str->format('2 2 1 3 3'));

        $str = new StringType('2313120P10');
        $this->assertEquals('23131-20P10', $str->format('5-5'));

        $str = new StringType('6851XA');
        $this->assertEquals('68-51XA', $str->format('2-'));
    }

    /**
     * @throws \Exception
     */
    public function testReplace()
    {
        $str = new StringType('17117593454');
        $str[2] = '-';
        $this->assertEquals('17-17593454', $str->toString());

        $str = new StringType('17117593454');
        $str['2:4'] = '__';
        $this->assertEquals('17__7593454', $str->toString());
    }

    /**
     * @throws \Exception
     */
    public function testTake()
    {
        $str = new StringType('A8DG0EK8FML3T6Y2W04A34R45ZE6A08T28WD9T1P1BP1DC0YH4B1AC1G31H01NL1PCB0DB34E0AF0AJ1LJ65L0LN0WQ1AU0NV0L0AL0A10BC0CU0FA0F20G20KA0L00NB0N10RB0SC0TD1C21D01EB1E41GD1JC1KS1MF1N11SA1WB1X01ZG1Z22B72C52D12G72JG2LC2V53B13FA3H93LR3L33NB3P03Q13U13ZM4E24GF4KC4K04L24P04Q14SD4TD4UB4X15C15J05K15RR5SH6E06FE6PA6P26Q16R06SC6W36XD7A17E07L07P07Q08BL8CC8E28F18GD8KA8K48M08N48QE8Q08RL8R18SA8S18TA8W08X08YA8Z79AB9D09FB9JA9P19Q09TC9V39W0');
        $arr = array();
        while ($pr = $str->take(3)) {
            array_push($arr, "'$pr'");
        }

        $this->assertSame(137, count($arr));
    }
}
