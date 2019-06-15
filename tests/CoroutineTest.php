<?php

namespace tests\utils;

require __DIR__ . "/../lib/coroutines.php";

use PHPUnit\Framework\TestCase;

class CoroutineTest extends TestCase
{

    public function testPusher()
    {
        $store = array();

        $src = ['a', 'b', 'c'];

        # case when first argument is function
        source($src,
            mapper(function($x){ return mb_strtoupper($x);}, pusher($store)));

        $this->assertEquals(['A','B', 'C'], $store);

        # case when first argument is string
        $store = [];

        source($src,
            mapper('mb_strtoupper', pusher($store)));

        $this->assertEquals(['A','B', 'C'], $store);
    }
}
