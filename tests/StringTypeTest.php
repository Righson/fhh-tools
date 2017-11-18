<?php
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 18.11.2017
 * Time: 12:06
 */

use PHPUnit\Framework\TestCase;

class StringTypeTest extends TestCase
{
    public function testSimpleUseStringType()
    {
        $str = new StringType('foobar');
        # $this->assertEquals('bar',$str['3:']);
        $this->assertTrue(isset($str[1]));
        $this->assertFalse(isset($str[1000]));
        $this->assertEquals('f', $str[0]);
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        $this->assertEquals('foo', $str[[0,3]]);
    }

    public function testAdvancedUseStringType()
    {
        $str = new StringType('foobar');
        $this->assertEquals('bar', $str['3:']);
    }

    public function testSimpleSetStringType()
    {
        $str = new StringType('Привет мир!');
        $str[0] = 'Щ';
        $this->assertEquals('Щривет мир!', $str);
    }

    public function testAdvancedSetUseStringType()
    {
        $str = new StringType('Привет мир!');
        $str['7:'] = 'вселенная!';
        $this->assertEquals('Привет вселенная!', $str);
        $str[':6'] = 'Прощай';
        $this->assertEquals('Прощай вселенная!', $str);
    }

}
