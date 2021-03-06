<?php

use Aweb\Nexus\Config;
use Aweb\Nexus\Request;
use Aweb\Nexus\Url;
use Aweb\Nexus\Session;
use Aweb\Nexus\Support\Arr;
use Aweb\Nexus\Support\Str;
use Aweb\Nexus\Validator;
use Aweb\Nexus\Db;
use Aweb\Nexus\Schema;
use Aweb\Nexus\Database\Schema\Blueprint;
use Aweb\Nexus\Support\Updater;

class ControllerCommonNexus extends Controller {

    public function index()
    {
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        /**
         * Oc3 .tpl hack
         */
        $defaultTemplateEngine = $this->config->get('template_engine');
        if($defaultTemplateEngine !== 'Template') {
            $this->config->set('template_engine', 'Template');
        }

        $updater = new Updater();
        $data['current_version'] = $updater->getCurrentVersion();
        $data['succces'] = $data['warning'] = null;
        if($update_available = $updater->isNewVersionAvailable())
        {
            $link = Url::route('common/nexus/update');
            $data['warning'] = "Version " . $update_available['version'] . " is available! <a href='{$link}'>Click here to install</a>";
        } else {
            $data['succces'] = 'Nexus is up to date!';
        }

        $tpl_ext = '';
        if(version_compare(VERSION, '2.3.0.2', '<')) {
            $tpl_ext = '.tpl';
        }

        $this->response->setOutput($this->load->view('common/nexus' . $tpl_ext, $data));
    }

    public function update()
    {
        try {
            $updater = new Updater();
            $updater->doUpdate();
        } catch(Exception $e) {
            Session::errors([$e->getMessage()]);

            return Url::redirectTo('common/nexus');
        }

        Session::success('Nexus has been updated successfully!');
        return Url::redirectTo('common/nexus');
    }

    public function tests()
    {



        // transactions https://laravel.com/docs/5.8/database
        // pre( DB::table('setting')->count());
        // DB::transaction(function () {
        //     DB::table('setting')->insert(['code' => 'test_db']);

        //     DB::table('setting')->insert(['codes' => 'test_db2']);
        // });
        // pre( DB::table('setting')->count(),1);

        // pre( DB::table('setting')->count());
        // DB::beginTransaction();
        // DB::table('setting')->insert(['code' => 'test_db2']);
        // DB::commit();
        // DB::rollBack();
        // pre( DB::table('setting')->count());


        // Schema::create('flights', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->string('airline');
        //     $table->timestamps();
        // });
        // Schema::drop('flights');

        // Db::table('setting')->insert([
        //     'code' => 'test_db',
        //     'serialized' => 0
        // ]);

        // Db::table('setting')->increment('code');

        // $results = Db::from('setting')->where('setting_id', 1)->first();
        // pre($results->code);

        // $results = Db::select('select setting_id, code from ' . DB_PREFIX . 'setting where setting_id = ?', [1]);
        // pre($results);

        // $results = DB::table('setting')->count();
        // pre($results);

        // $cursor = DB::table('setting')
        // ->select('code', DB::raw('COUNT(setting_id) as total_ids'))
        // ->groupBy('code')
        // ->havingRaw('count(total_ids) > ?', [1])
        // ->cursor();
        // foreach ($cursor as $e) {
        //     pre($e);
        // }

        // $test = DB::table('zone')->where('zone_id', '>', '50')->limit(5)->pluck('name', 'code')->toJson();
        // pre($test);

        // DB::table('setting')->select('code')->orderBy('setting_id')->groupBy('code')->chunk(5, function($settings)
        // {
        //     foreach ($settings as $setting)
        //     {
        //         pre($setting);
        //     }
        // });

        // $orders = DB::table('setting')
        //         ->select('code', DB::raw('COUNT(setting_id) as total_ids'))
        //         ->groupBy('code')
        //         ->havingRaw('count(total_ids) > ?', [5])
        //         ->get();
        // dd($orders);

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

        // Config::set('config_admin_language', 'ro-ro');
        // Session::set('language', 'ro-ro');

        // $this->load->language('customer/customer');
        // if (Request::method() === 'POST') {
        //     Request::validate([
        //         'firstname' => 'required|max:255',
        //         'lastname' => 'required',
        //     ]);
        // }

        // // dump('old firstname', Request::old('firstname'));
        // // dump('old lastname', Request::old('lastname'));
        // Session::get('_errors');
        // echo '<form method="POST" action="">
        //     <div class="form-group">
        //         <label>
        //             Firstname <input type="text" name="firstname" class="form-control" value="' . Request::old('firstname') . '">
        //         </label>
        //         <div class="text-danger">'. error('firstname') .'</div>
        //     </div>
        //     <div class="form-group">
        //         <label>
        //             Lastname <input type="text" name="lastname" class="form-control" value="' . Request::old('lastname') . '">
        //         </label>
        //         <div class="text-danger">'. error('lastname') .'</div>
        //     </div>
        //     <button type="submit" class="btn btn-primary">Submit</button>
        // </form>';

        // dump(Url::route('foo/bar', ['foo' => 123]));
        // dump(
        //     Url::route('foo/bar', ['foo' => 123], 1)
        // );

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