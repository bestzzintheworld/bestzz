<?php

namespace App\Http\Controllers;

use App\Hi;
use App\Swoole\Server;
use App\Swoole\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class HiController extends UserController
{

    public function index()
    {
        echo 'i am hi index!';
        //$this->Test_q();
        $this->Text_w();
        phpinfo();
    }

    public function hello()
    {
        return view('Hi.hello');
    }

    public function upload(Request $request)
    {
        if ($request->isMethod('POST')) { //判断是否是POST上传，应该不会有人用get吧，恩，不会的
            //在源生的php代码中是使用$_FILE来查看上传文件的属性
            //但是在laravel里面有更好的封装好的方法，就是下面这个
            //显示的属性更多
            $fileCharater = $request->file('source');

            if ($fileCharater->isValid()) { //括号里面的是必须加的哦
                //如果括号里面的不加上的话，下面的方法也无法调用的

                //获取文件的扩展名
                $ext = $fileCharater->getClientOriginalExtension();

                //获取文件的绝对路径
                $path = $fileCharater->getRealPath();
                var_dump($path);

                //定义文件名
                $filename = date('Y-m-d-h-i-s') . '.' . $ext;

                //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
                Storage::disk('public')->put($filename, file_get_contents($path));
            }
        }
    }

    public function upload1()
    {
        var_dump($_POST);
        var_dump($_FILES);
        if (file_exists("/storage/app/public/images/" . $_FILES['source']['name'])) {
            echo $_FILES["source"]["name"] . " already exists. ";

        } else {
            echo 'oh yeah!';
            echo $_FILES["source"]["tmp_name"];
            var_dump(storage_path('app/public/images/') . $_FILES["source"]["name"]);
            move_uploaded_file($_FILES["source"]["tmp_name"], storage_path('app/public/images/') . $_FILES["source"]["name"]);
        }
    }
}


