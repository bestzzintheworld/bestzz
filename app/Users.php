<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $connection = 'mysqlzz';
    protected $table = 'users';
//指定主键
    protected $primaryKey= 'id';
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

//    protected  function getDateFormat()
//    {
//        return time();
//    }

}
