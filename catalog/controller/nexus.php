<?php

use Aweb\Nexus\Config;
use Aweb\Nexus\Request;
use Aweb\Nexus\Session;
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

        dump(Session::has('foo'));
        Session::set('foo', 123);
        dump(Session::has('foo'));
        Session::put('foo.bar', 234);
        dump(Session::has('foo.bar'));
        dump(Session::get('foo.bar'));
        dump(Session::pull('foo'));
        dump(Session::get('foo'));
        dump(Session::increment('foo'));
        dump(Session::increment('foo'));
        dump(Session::increment('foo'));
        dump(Session::decrement('foo'));
        dump(Session::all());
        Session::forget('foo');
        dump(Session::all());
        // Session::flush();
        dump(Session::all());
        dump(Session::getId());
        dump(Session::getName());
        dump(Session::getFlashBag());
        Session::flash('foo', 'bar');
        dump(Session::getFlashBag());
        dump(Session::all());
        dump(Session::increment('i'));
        if ((Session::get('i') % 3) === 0) {
            Session::flash('_success', 'My flashed data');
            dump('Next refresh you will have flashed data too');
        } else {
            dump('-');
        }

        dump(Session::get('_success'));
        dump('When session.i is divisible with 2, data will be keeped on session permanent. You should see session.all filling with even numbers');

        if (Session::get('i') % 2 === 0) {
            Session::flash('k'.Session::get('i'), Session::get('i'));
            Session::keep('k'.Session::get('i'));
        }
        dump(Session::all());



    }
}