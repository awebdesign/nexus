<?php

use Aweb\Nexus\Support\IArr;

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

        dump(IArr::pluck($arr, 'foo'));
    }
}