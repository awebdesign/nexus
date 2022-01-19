<?php

use Aweb\Nexus\IArr;
use Aweb\Nexus\IRequest;
use Aweb\Nexus\IStr;

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

        dump(IRequest::method());
        IRequest::set('foo', 123);
        dump(IRequest::get('foo', 'bar'));
        dump(IRequest::get('nested.nested1.nested2.nested3', 'bar'));
        dump(IRequest::post('nested.nested1.nested2.nested3', 'bar'));
        dump(IRequest::post('x', 'bar'));
        dump(IRequest::getScheme());
        dump(IRequest::isSecure());
        dump(IRequest::getPort());
        dump(IRequest::getQuery());
        dump(IRequest::getQueryString());
        dump(IRequest::getHost());
        IRequest::setMethod('post');
        dump($this->request->server, IRequest::getMethod());
        dump(IRequest::server('PWD'));
        dump(IRequest::has('foo'));
        dump(IRequest::has('baz'));
        dump(IRequest::hasAny(['baz', 'foo']));
        dump(IRequest::filled(['baz', 'foo']));
        dump(IRequest::filled(['bar', 'foo']));
        dump(IRequest::anyFilled(['bar', 'sasd']));
        dump(IRequest::keys());
        dump(IRequest::all());
        dump(IRequest::input('nested'));
        dump(IRequest::only(['nested', 'bar', 'sdasd']));
        dump(IRequest::except(['nested', 'bar']));
        dump(IRequest::query('bar'));
    }
}