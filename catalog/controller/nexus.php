<?php

use Aweb\Nexus\IArr;
use Aweb\Nexus\IStr;

class ControllerNexus extends Controller {

    public function index()
    {
        $arr = [
            'foo' => [
                'bar' => 1
            ]
        ];

        dump(IArr::get($arr, 'foo.bar'));

        $arr = [
            ['foo' => 1],
            ['foo' => 2],
        ];

        dump(IArr::pluck($arr, [], 'foo'));

        dump(IStr::plural('car'));
        dump(IStr::snake('IAmASnake'));
        dump(IStr::camel('i-am-a-camel'));
        dump(IStr::slug('IAmASlug'));

        dump(collect($arr)->sort(function($x, $y) {
            return $y['foo'] - $x['foo'];
        }));


    }
}