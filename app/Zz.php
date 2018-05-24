<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zz extends Model
{
    //
    protected $table = 'users';
//指定主键
    protected $primaryKey= 'id';
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    protected  function getDateFormat()
    {
        return time();
    }

}
