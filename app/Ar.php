<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ar extends Model
{
    //
    protected $table = 'ar';
//指定主键
    protected $primaryKey= 'article_id';
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
