<?php

namespace App\Http\Controllers;

use App\Hi;
use App\Swoole\Server;
use App\Swoole\Client;

class HiController extends UserController
{

    public function index()
    {
        echo 'i am hi index!';
        //$this->Test_q();
        $this->Text_w();
        phpinfo();
    }

    public function testServer(){
        //创建Server对象，监听 127.0.0.1:9501端口
        $serv = new swoole_server("127.0.0.1", 9501);

//监听连接进入事件
        $serv->on('connect', function ($serv, $fd) {
            echo "Client: Connect.\n";
        });

//监听数据接收事件
        $serv->on('receive', function ($serv, $fd, $from_id, $data) {
            $serv->send($fd, "Server: ".$data);
        });

//监听连接关闭事件
        $serv->on('close', function ($serv, $fd) {
            echo "Client: Close.\n";
        });

//启动服务器
        $serv->start();

    }

    public function testClient(){
        $client = new Client();
        $client->connect();

    }
}


