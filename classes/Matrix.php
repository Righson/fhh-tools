<?php
/**
 * Created by PhpStorm.
 * User: fhh
 * Date: 14.05.19
 * Time: 14:32
 */

namespace classes;


class Matrix
{
    private ArrayType $container;

    public function __construct($collection)
    {
        if (is_array($collection)) {
            $this->container = new ArrayType($collection);
        } elseif (in_array("ArrayAccess", class_implements($collection))) {
            /**
             * @var $collection ArrayType
             */
            $this->container = $collection;
        }
    }

    public function get()
    {
            return $this->container->get();
    }

    public function merge(Matrix $matrix)
    {
        $newCont = new ArrayType();

        foreach($this->container->get() as $idx => $row) {
                foreach($matrix->get() as $mRow) {
                        if (all([is_array($row), is_array($mRow)])) {
                                $newCont->append(array_merge($row));
                                continue;
                        }

                        if (is_array($row)) {
                                $r = $row;
                                array_push($r, $mRow);
                                $newCont->append($r);
                                continue;
                        }

                        $newCont->append([$row, $mRow]);
                }
        }

        return new Matrix($newCont);
    }

    public function intersect(array $set)
    {
        $newCont = array_intersect($this->get(), $set);
        return new Matrix($newCont);
    }
}
