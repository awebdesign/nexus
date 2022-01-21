<?php

use Aweb\Nexus\Config;
use Aweb\Nexus\Request;
use Aweb\Nexus\Route;
use Aweb\Nexus\Session;
use Aweb\Nexus\Support\Arr;
use Aweb\Nexus\Support\Str;
use Aweb\Nexus\Validator;

class ControllerCommonNexus extends Controller {

    public function index()
    {


        // if (Request::get('flush')) {
        //     Session::flush();
        // }


        // $this->request->post['nested'] = [
        //     'nested1' => [
        //         'nested2' => [
        //             'nested3' => 123
        //         ]
        //     ]
        // ];

        // $arr = [
        //     'foo' => [
        //         'bar' => 1
        //     ]
        // ];

        // dump(Arr::get($arr, 'foo.bar'));

        // $arr = [
        //     ['foo' => 1],
        //     ['foo' => 2],
        // ];

        // dump(Arr::pluck($arr, [], 'foo'));

        // dump(Str::plural('car'));
        // dump(Str::snake('IAmASnake'));
        // dump(Str::camel('i-am-a-camel'));
        // dump(Str::slug('IAmASlug'));

        // dump(collect($arr)->sort(function($x, $y) {
        //     return $y['foo'] - $x['foo'];
        // }));

        // dump(Request::method());
        // Request::set('foo', 123);
        // dump(Request::get('foo', 'bar'));
        // dump(Request::get('nested.nested1.nested2.nested3', 'bar'));
        // dump(Request::post('nested.nested1.nested2.nested3', 'bar'));
        // dump(Request::post('x', 'bar'));
        // dump(Request::getScheme());
        // dump(Request::isSecure());
        // dump(Request::getPort());
        // dump(Request::getQuery());
        // dump(Request::getQueryString());
        // dump(Request::getHost());
        // Request::setMethod('post');
        // dump($this->request->server, Request::getMethod());
        // dump(Request::server('PWD'));
        // dump(Request::has('foo'));
        // dump(Request::has('baz'));
        // dump(Request::hasAny(['baz', 'foo']));
        // dump(Request::filled(['baz', 'foo']));
        // dump(Request::filled(['bar', 'foo']));
        // dump(Request::anyFilled(['bar', 'sasd']));
        // dump(Request::keys());
        // dump(Request::all());
        // dump(Request::input('nested'));
        // dump(Request::only(['nested', 'bar', 'sdasd']));
        // dump(Request::except(['nested', 'bar']));
        // dump(Request::query('bar'));

        // Config::set('foo.bar.bam', 123);
        // dump(Config::get('foo'));
        // dump(Config::get('foo.bar.bam'));
        // dump(Config::get('foo.bar.baz', 'default'));
        // dump(Config::set([
        //     'a' => 1,
        //     'b' => 2,
        // ]));
        // dump(Config::get('a'));
        // dump(Config::get('b'));

        // dump(Session::has('foo'));
        // Session::set('foo', 123);
        // dump(Session::has('foo'));
        // Session::put('foo.bar', 234);
        // dump(Session::has('foo.bar'));
        // dump(Session::get('foo.bar'));
        // dump(Session::pull('foo'));
        // dump(Session::get('foo'));
        // dump(Session::increment('foo'));
        // dump(Session::increment('foo'));
        // dump(Session::increment('foo'));
        // dump(Session::decrement('foo'));
        // dump(Session::all());
        // Session::forget('foo');
        // dump(Session::all());
        // dump(Session::all());
        // dump(Session::getId());
        // dump(Session::getName());
        // dump(Session::getFlashBag());
        // Session::flash('foo', 'bar');
        // dump(Session::getFlashBag());
        // dump(Session::all());
        // dump(Session::increment('i'));
        // if ((Session::get('i') % 3) === 0) {
        //     Session::flash('_success', 'My flashed data');
        //     dump('Next refresh you will have flashed data too');
        // } else {
        //     dump('-');
        // }

        // dump(Session::get('_success'));
        // dump('When session.i is divisible with 2, data will be keeped on session permanent. You should see session.all filling with even numbers');

        // if (Session::get('i') % 2 === 0) {
        //     Session::flash('k'.Session::get('i'), Session::get('i'));
        //     Session::keep('k'.Session::get('i'));
        // }
        // dump(Session::all());

        Config::set('config_admin_language', 'ro-ro');
        Session::set('language', 'ro-ro');

        $this->load->language('customer/customer');
        if (Request::method() === 'POST') {
            Request::validate([
                'firstname' => 'required|max:255',
                'lastname' => 'required',
            ]);
        }

        // dump('old firstname', Request::old('firstname'));
        // dump('old lastname', Request::old('lastname'));
        Session::get('_errors');
        echo '<form method="POST" action="">
            <div class="form-group">
                <label>
                    Firstname <input type="text" name="firstname" class="form-control" value="' . Request::old('firstname') . '">
                </label>
                <div class="text-danger">'. error('firstname') .'</div>
            </div>
            <div class="form-group">
                <label>
                    Lastname <input type="text" name="lastname" class="form-control" value="' . Request::old('lastname') . '">
                </label>
                <div class="text-danger">'. error('lastname') .'</div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>';

        dump(Route::link('foo/bar', ['foo' => 123]));
        dump(
            Route::link('foo/bar', ['foo' => 123], Route::CATALOG)
        );

        // Session::set('config_admin_language', 'ro-ro');
        // Session::set('language', 'ro-ro');

        // $validator = Validator::make(Request::post(), [
        //     'firstname' => 'required|max:255',
        //     'lastname' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     $errors = $validator->errors();
        //     print_r($errors->firstOfAll());
        // }

        //TODO: DD throws error 500
    }
}