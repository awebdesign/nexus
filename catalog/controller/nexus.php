<?php

use Aweb\Nexus\Support\IArr;

class ControllerNexus extends Controller {

    public function index()
    {
        IArr::get(['foo' => [
            'bar' => 1
        ]], 'foo.bar');
    }
}