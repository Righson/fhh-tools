<?php
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 11.11.2017
 * Time: 11:05
 */

require __DIR__ . '/../classes/StringIterator.php';

use PHPUnit\Framework\TestCase;

class TestStringIterator extends TestCase
{
    public function testStringIteratorWithDefaultValues()
    {
        $it = new StringIterator('foo_bar');

        $this->assertEquals($it->current(), 'f');
    }

    public function testStringIteratorSetLength(){
        $it = new StringIterator('foo_bar');
        $it->next();

        $this->assertEquals($it->current(), 'o');
        $this->assertEquals($it->setLength(3)->step(),'oo_');

    }

    public function testStringIteratorStepWithDefaultValues()
    {
        $it = new StringIterator('foo_bar');

        $this->assertEquals($it->step(), 'f');
    }

    public function testStringIteratorManyStepsWithDefaultValues()
    {
        $string = 'foo_bar';

        $it = new StringIterator($string);

        for ($i=0; $i != strlen($string) - 2; $i++) {
            $it->step();
        }

        $this->assertEquals($it->step(), 'a');
    }

    public function testStringIteratorWithCustomValues()
    {
        $it = new StringIterator('foo_bar', 3);
        $this->assertEquals($it->current(), 'f');

        $it = new StringIterator('foo_bar', 3, 3);
        $this->assertEquals($it->current(), 'foo');
    }

    public function testStringIteratorStepWithCustomValues()
    {
        $it = new StringIterator('foo_bar', 3);
        $this->assertEquals($it->step(), 'f');

        $it = new StringIterator('foo_bar', 3, 3);
        $this->assertEquals($it->step(), 'foo');
        $this->assertEquals($it->skip(1)->step(), 'bar');
        $this->assertEquals($it->step(), false);

        $it = new StringIterator('foo_bar', 1, 3);
        $this->assertEquals($it->step(), 'foo');
        $this->assertEquals($it->step(), 'oo_');
        # $this->assertEquals($it->step(), false);
    }

    public function testStringIteratorAfterStepWithCustomValues()
    {
        $it = new StringIterator('foo_bar', 3);
        $it->step();

        $this->assertEquals($it->step(), '_');

        $it->step();
        $this->assertEquals($it->step(), false);
    }
}
