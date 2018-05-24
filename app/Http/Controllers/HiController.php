<?php

namespace App\Http\Controllers;

use App\Hi;
use App\Swoole\Server;
use App\Swoole\Client;
use Illuminate\Http\Request;


class HiController extends UserController
{

    public function index()
    {
        echo 'i am hi index!';
        //$this->Test_q();
        $this->Text_w();
        phpinfo();
    }

    public function hello(){

    }

    public function upload(Request $request){
        $path = $request->file('avatar')->store('avatars');

        return $path;
    }
}


