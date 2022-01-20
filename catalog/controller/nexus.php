<?php

use Aweb\Nexus\Config;
use Aweb\Nexus\Request;
use Aweb\Nexus\Support\Arr;
use Aweb\Nexus\Support\Str;

class ControllerNexus extends Controller {

    public function index()
    {

        $this->request->post['nested'] = [
            'nested1' => [
                'nested2' => [
                    'nested3' => 123
                ]
            ]
        ];

        $arr = [
            'foo' => [
                'bar' => 1
            ]
        ];

        dump(Arr::get($arr, 'foo.bar'));

        $arr = [
            ['foo' => 1],
            ['foo' => 2],
        ];

        dump(Arr::pluck($arr, [], 'foo'));

        dump(Str::plural('car'));
        dump(Str::snake('IAmASnake'));
        dump(Str::camel('i-am-a-camel'));
        dump(Str::slug('IAmASlug'));

        dump(collect($arr)->sort(function($x, $y) {
            return $y['foo'] - $x['foo'];
        }));

        dump(Request::method());
        Request::set('foo', 123);
        dump(Request::get('foo', 'bar'));
        dump(Request::get('nested.nested1.nested2.nested3', 'bar'));
        dump(Request::post('nested.nested1.nested2.nested3', 'bar'));
        dump(Request::post('x', 'bar'));
        dump(Request::getScheme());
        dump(Request::isSecure());
        dump(Request::getPort());
        dump(Request::getQuery());
        dump(Request::getQueryString());
        dump(Request::getHost());
        Request::setMethod('post');
        dump($this->request->server, Request::getMethod());
        dump(Request::server('PWD'));
        dump(Request::has('foo'));
        dump(Request::has('baz'));
        dump(Request::hasAny(['baz', 'foo']));
        dump(Request::filled(['baz', 'foo']));
        dump(Request::filled(['bar', 'foo']));
        dump(Request::anyFilled(['bar', 'sasd']));
        dump(Request::keys());
        dump(Request::all());
        dump(Request::input('nested'));
        dump(Request::only(['nested', 'bar', 'sdasd']));
        dump(Request::except(['nested', 'bar']));
        dump(Request::query('bar'));

        Config::set('foo.bar.bam', 123);
        dump(Config::get('foo'));
        dump(Config::get('foo.bar.bam'));
        dump(Config::get('foo.bar.baz', 'default'));
        dump(Config::set([
            'a' => 1,
            'b' => 2,
        ]));
        dump(Config::get('a'));
        dump(Config::get('b'));


    }
}