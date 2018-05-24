<?php

namespace App\Http\Controllers;

use App\Zz;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class ZzController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        phpinfo();
//        echo 'index';
        define('ZZ',
            [
                'zz',
                'zz',
                '1314',
                '520'
            ]);
        echo ZZ[1];
//        echo "\u{aa}";
//        echo "\u{0000aa}";
//        echo "\u{9999}";

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        echo 'create';

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo 'show: '.$id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function orm1(){
//        $users  = Zz::all();
//        $users = Zz::find(5);
//        $users = Zz::findorfail(6);
        $users = Zz::get();


        dd($users);
    }
    public function orm2(){
        $zz = new Zz();
//        $zz->name = '真滴帅';
//        $zz->age = 18;
//        $bool = $zz->save();
//        var_dump($bool);
         $re = Zz::find(6);
        echo $re->created_at;
    }
}
