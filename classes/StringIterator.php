<?php
/**
 * Created by PhpStorm.
 * User: dmitrijm
 * Date: 11.11.2017
 * Time: 11:32
 */

class StringIterator implements Iterator
{

    private $target;
    private $position;
    private $length;
    private $step;
    /**
     * @var callable
     */
    private $lambda;

    /**
     * StringIterator constructor.
     * @param string $target
     * @param int $step
     * @param int $length
     */
    public function __construct($target, $step=1, $length=0)
    {
        $this->setTarget($target);
        $this->position = 0;
        $this->step = $step;
        $this->length = $length;
        $this->lambda = null;
    }

    /**
     * @param callable $lambda
     */
    public function setLambda(callable $lambda)
    {
        $this->lambda = $lambda;
    }

    /**
     * @param int $step
     * @return $this
     */
    public function setStep(int $step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength(int $length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target)
    {
        $this->target = $target;
    }

    public function next()
    {
        $this->position += $this->step;
    }

    public function current()
    {
        if ($this->length != 0) {
            $ret = substr($this->target, $this->position, $this->length);
        } else {
            $ret = substr($this->target, $this->position, 1);
        }

        if ($this->lambda) {
            return call_user_func_array($this->lambda, [$ret]);
        } else {
            return $ret;
        }
    }

    public function rewind()
    {
        $this->position=0;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return strlen($this->target) >= $this->position - 1;
    }

    /**
     * @param $value
     * @return $this
     */
    public function skip($value){
        $this->position += $value;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function step(){
        $ret = $this->current();
        $this->next();

        return $ret;
    }
}