<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Swoole\swoole_simple_client;
use Illuminate\Swoole\swoole_simple_server;
use App\User;
use App\Http\Controllers\Auth;
use App\Ar;

class UserController extends Controller
{

    /**
     * 展示给定用户的信息。
     *
     * @param  int $id
     * @return Response
     */

    private function Test_q()
    {
        echo 'i am Test_q!';
    }

    protected function Text_w()
    {
        echo 'i am Text_w!';
    }

    public function tests()
    {
        echo md5(md5(123456));
//        $users = new Users();
//        $re = $users->get();
//        var_dump($re);
//        $users->name = 'aha1';
//        $users->age = 16;
//        $users->save();

    }

    //随机IP
    function Rand_IP()
    {

        $ip2id = round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
        $ip3id = round(rand(600000, 2550000) / 10000);
        $ip4id = round(rand(600000, 2550000) / 10000);
        //下面是第二种方法，在以下数据中随机抽取
        $arr_1 = array("218", "218", "66", "66", "218", "218", "60", "60", "202", "204", "66", "66", "66", "59", "61", "60", "222", "221", "66", "59", "60", "60", "66", "218", "218", "62", "63", "64", "66", "66", "122", "211");
        $randarr = mt_rand(0, count($arr_1) - 1);
        $ip1id = $arr_1[$randarr];
        return $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
    }

    public function test3()
    {
        $url = 'http://weixin.sogou.com/weixin?query=%E6%90%9E%E7%AC%91&_sug_type_=&sut=87565&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=1&sst0=1521099951295&page=8&ie=utf8&w=01019900&dr=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $cookie_file = 'cookie.txt';//cookie地址
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        //print_r($output);die();
        /***
         * 先按li分割 匹配公众号详情url、文章最新时间、头像、介绍、二维码、主体、最新文章标题、昵称 存入数组
         **/
        $regex = "/<li id=\"sogou.*?>.*?<\/li>/ism";
        preg_match_all($regex, $output, $li);
        //  var_dump($li);
        for ($i = 0; $i < count($li[0]); $i++) {
            $regex1 = "/<label name=\"em_weixinhao\".*?>.*?<\/label>/ism";
            $regex2 = "/<img height=\"32\" width=\"32\" class=\"shot-img\" src=\".*?\"/ism";
            $regex3 = "/<img height=\"104\" width=\"104\" src=\".*?\"/ism";
            $regex4 = "/<dt>功能介绍：<\/dt.*?>.*?<\/dd>/ism";
            $regex5 = '/认证：<\/dt.*?>.*?<\/dd>/ism';
            $regex6 = "/\p{Han}{0,10}\w{0,10}<em><!--red_beg-->搞笑<!--red_end--><\/em>\p{Han}{0,10}\w{0,10}<\/a>/uism";
            $regex7 = "/<dt>最近文章：<\/dt>.*?>.*?<\/a>/ism";
            $regex8 = "/<script.*?>.*?<\/script>/ism";
            preg_match_all($regex8, $li[0][$i], $latesttime);
            $regex8 = "/\d{10}/";
            $regex9 = "/<a target=\"_blank\" .*?\">.*?<\/a>/ism";
            preg_match_all($regex9, $li[0][$i], $infourl);//公众号详情url
            $regex9 = "/href=\".*?\"/ism";
            preg_match_all($regex9, $li[0][$i], $infourl);//公众号详情url
            preg_match_all($regex8, implode("", $latesttime[0]), $latesttime);//最新时间
            preg_match_all($regex1, $li[0][$i], $acount);//gh_name
            preg_match_all($regex2, $li[0][$i], $headerimg);//头像
            preg_match_all($regex3, $li[0][$i], $qrcode);//二维码
            preg_match_all($regex4, $li[0][$i], $introduce);//介绍
            preg_match_all($regex5, $li[0][$i], $subject);//主体
            preg_match_all($regex6, $li[0][$i], $name);//昵称
            preg_match_all($regex7, $li[0][$i], $latestarticle);//最新文章
            if (isset($infourl[0][0])) {
                $infourl[0][0] = str_replace("href=", "", $infourl[0][0]);
                $infourl[0][0] = str_replace("\"", "", $infourl[0][0]);
                $infourl[0][0] = str_replace("amp;", "", $infourl[0][0]);
                $wechat[$i]['infourl'] = $infourl[0][0];
            }

            if (isset($latesttime[0][0])) {
                $wechat[$i]['latesttime'] = $latesttime[0][0];
            } else {
                $wechat[$i]['latesttime'] = '';
            }
            if (isset($latestarticle[0][0])) {
                $latestarticle[0][0] = strip_tags($latestarticle[0][0]);
                $latestarticle[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $latestarticle[0][0]);
                $latestarticle[0][0] = str_replace("最近文章：", "", $latestarticle[0][0]);
                $wechat[$i]['latestarticle'] = $latestarticle[0][0];
            } else {
                $wechat[$i]['latestarticle'] = '';
            }
            if (isset($acount[0][0])) {
                $acount[0][0] = strip_tags($acount[0][0]);
                $wechat[$i]['gh_name'] = $acount[0][0];
            } else {
                $wechat[$i]['gh_name'] = '';
            }
            if (isset($headerimg[0][0])) {
                $headerimg[0][0] = substr($headerimg[0][0], strrpos($headerimg[0][0], 'src="'));
                $headerimg[0][0] = str_replace("\"", "", $headerimg[0][0]);
                $headerimg[0][0] = str_replace("src=", "", $headerimg[0][0]);
                $wechat[$i]['gh_head_img'] = $headerimg[0][0];
            } else {
                $wechat[$i]['gh_head_img'] = '';
            }
            if (isset($qrcode[0][0])) {
                $qrcode[0][0] = substr($qrcode[0][0], strrpos($qrcode[0][0], 'src="'));
                $qrcode[0][0] = str_replace("\"", "", $qrcode[0][0]);
                $qrcode[0][0] = str_replace("amp;", "", $qrcode[0][0]);
                $qrcode[0][0] = substr($qrcode[0][0], 4);
                $wechat[$i]['gh_qrcode_url'] = $qrcode[0][0];
            } else {
                $wechat[$i]['gh_qrcode_url'] = '';
            }
            if (isset($introduce[0][0])) {
                $introduce[0][0] = strip_tags($introduce[0][0]);
                $introduce[0][0] = str_replace(array("\r\n", "\r", "\n", "功能介绍："), "", $introduce[0][0]);
                $wechat[$i]['gh_signature'] = $introduce[0][0];
            } else {
                $wechat[$i]['gh_signature'] = '';
            }
            if (isset($subject[0][0])) {
                $subject[0][0] = strip_tags($subject[0][0]);
                $subject[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $subject[0][0]);
                $subject[0][0] = str_replace("认证：", "", $subject[0][0]);
                $wechat[$i]['gh_principal_name'] = $subject[0][0];
            } else {
                $wechat[$i]['gh_principal_name'] = '';
            }
            if (isset($name[0][0])) {
                $name[0][0] = strip_tags($name[0][0]);
                $wechat[$i]['gh_nick_name'] = $name[0][0];
            } else {
                $wechat[$i]['gh_nick_name'] = '';
            }
        }
        var_dump($wechat);
    }

    public function test4()
    {
        $ch = curl_init();
        //设置选项，包括URL
        $url = "https://mp.weixin.qq.com/s?src=11&timestamp=1526972401&ver=891&signature=MfOc-XzY3R0JJiZBWLL4a0LQciZRTANhxv*MuRAnElowoJIITd9mRd4vJJWPgFF6KV3aYMUkbmkqM--VNoQda9VCofV98uQJniOxjafGvM4DkjoNjrP3FUmRcVaguhQ5&new=1";
        //  $url = "http://weixin.sogou.com/weixin?query=" . $obj . "&_sug_type_=&sut=4974927&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=" . $type . "&sst0=1520587565001&page=".$x."&ie=utf8&w=01019900&dr=1";
//        $rand_ip=$this->Rand_IP();
//
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$rand_ip, 'CLIENT-IP:'.$rand_ip));//伪造IP
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $cookie_file = 'cookie.txt';//cookie地址
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        // print_r($output);die();

        preg_match_all("/id=\"js_content\">(.*)<script/iUs", $output, $content, PREG_PATTERN_ORDER);
        $content = "<div id='js_content'>" . $content[1][0];
        $content = str_replace("data-src", "src", $content);
        $article_body = str_replace("preview.html", "player.html", $content);//文章主体
        // print_r($article_body);
        $regex0 = "/<img .*?>/";
        preg_match_all($regex0, $article_body, $body);
        var_dump($article_body);
        /*****
         * 替换微信图片地址
         */
        for ($k = 0; $k < count($body[0]); $k++) {
            $body[0][$k] = substr($body[0][$k], strrpos($body[0][$k], 'src="'));
            $body[0][$k] = str_replace("src=", "", $body[0][$k]);
            $tmp[$k]['image'] = $body[0][$k];
            $image = explode("\"", $tmp[$k]['image']);
            //  var_dump("<pre>", $image);
            $imgurl = 'http://img01.store.sogou.com/net/a/04/link?appid=100520029&url=' . $image[1] . "&tp=webp&wxfrom=5&wx_lazy=1";
            $article_body = str_replace($image[1], $imgurl, $article_body);

        }
        print_r($article_body);
        die();
        // $key = '&tp=webp&wxfrom=5&wx_lazy=1';
        /*****
         * 结束
         */
    }

    public function testurl()
    {
        $url = '<li id="sogou_vr_11002301_box_0" d="oIWsFtxJ5JJVvXbGqHjrXUc4BvP4">
<div class="gzh-box2">
<div class="img-box">
<a target="_blank" uigs="account_image_0" href="http://mp.weixin.qq.com/profile?src=3&amp;timestamp=1527039234&amp;ver=1&amp;signature=ZPgHg2JkfrewyO6-jShg7z9q2mWKJwl7XhwnlUmIcPGUR1zMrUmjq4JnEgJ5hpuk*kS3AKWea7-APNYnjdgjNg=="><span></span><img src="http://img01.sogoucdn.com/app/a/100520090/oIWsFtxJ5JJVvXbGqHjrXUc4BvP4" onload="resizeImage(this,58,58)" onerror="errorHeadImage(this)" style="width: 58px; height: auto; margin-top: 0px;"></a>
</div>
<div class="txt-box">
<p class="tit">
<a target="_blank" uigs="account_name_0" href="http://mp.weixin.qq.com/profile?src=3&amp;timestamp=1527039234&amp;ver=1&amp;signature=ZPgHg2JkfrewyO6-jShg7z9q2mWKJwl7XhwnlUmIcPGUR1zMrUmjq4JnEgJ5hpuk*kS3AKWea7-APNYnjdgjNg=="><em><!--red_beg-->星座<!--red_end--></em></a><i></i>
</p>
<p class="info">微信号：<label name="em_weixinhao">ljxygnjc</label>
<span class="line-s"></span>月发文&nbsp;168&nbsp;篇</p>
</div>
<div class="ew-pop">
<a class="code" href="javascript:void(0)"><img height="24" width="24" src="/new/pc/images/ico_ewm.png"></a><span style="display:none;" class="pop"><i></i>微信扫一扫关注<br>
<img height="104" width="104" src="http://mp.weixin.qq.com/rr?src=3&amp;timestamp=1527039234&amp;ver=1&amp;signature=EsKKhUD2I7agFqLustu9helYCVLxC-bcIVHsEQ7YNJ8HhCD0vsimA0*0UQG7YfhP566zQLwwbpU35L5K*3wFSoFUyFyijcFLYlqV8Q1cdOU=" data-id="oIWsFtxJ5JJVvXbGqHjrXUc4BvP4" onerror="qrcodeShowError(\'http://mp.weixin.qq.com/rr?src=3&amp;timestamp=1527039234&amp;ver=1&amp;signature=EsKKhUD2I7agFqLustu9helYCVLxC-bcIVHsEQ7YNJ8HhCD0vsimA0*0UQG7YfhP566zQLwwbpU35L5K*3wFSoFUyFyijcFLYlqV8Q1cdOU=\',4,\'oIWsFtxJ5JJVvXbGqHjrXUc4BvP4\')"><img height="32" width="32" class="shot-img" src="http://img01.sogoucdn.com/app/a/100520090/oIWsFtxJ5JJVvXbGqHjrXUc4BvP4" onerror="errorHeadImage(this)"></span>
</div>
</div>
<dl>
<dt>功能介绍：</dt>
<dd>搜罗[<em><!--red_beg-->星座<!--red_end--></em>]资讯,分析运势,解析爱情,揭秘个性<em><!--red_beg-->星座<!--red_end--></em>控必备的迷你宝典</dd>
</dl>
<dl>
<dt>
<script>document.write(authname(\'2\'))</script>微信认证：</dt>
<dd>哈尔滨网云科技有限公司</dd>
</dl>
<dl>
<dt>最近文章：</dt>
<dd>
<a target="_blank" uigs="account_article_0" href="http://mp.weixin.qq.com/s?src=11&amp;timestamp=1527039234&amp;ver=893&amp;signature=hZPSz8EJ8jCz5ZaxrrPElR780BQszUcxX7uDjRI9JPd-0sCMMGO9RKt9*Q35RTQ8I3N4o1E7*373XDi-CDurJ5dbZVKwWicisvtKYCuCJk1l0R7S3ruCFDCsm1*IPxI8&amp;new=1"><em><!--red_beg-->星座<!--red_end--></em>|射手的情商有多高?</a><span><script>document.write(timeConvert(\'1527004839\'))</script>9小时前</span>
</dd>
</dl>
</li>';
        $regex9 = "/href=\".*?\"/ism";
        preg_match_all($regex9, $url, $infourl);//公众号详情url
        if (isset($infourl[0][0])) {
            $infourl[0][0] = str_replace("href=", "", $infourl[0][0]);
            $infourl[0][0] = str_replace("\"", "", $infourl[0][0]);
            $infourl[0][0] = str_replace("amp;", "", $infourl[0][0]);
            $wechat['infourl'] = $infourl[0][0];
        }
        var_dump($wechat);
        exit();
    }

    public function searchex($obj, $type, $page)
    {
//        $ch = curl_init();
//        //设置选项，包括URL
//        $url = "https://mp.weixin.qq.com/profile?src=3&timestamp=1520925458&ver=1&signature=xJPZRztNSwiABDdcE*MZvFOsND7L-gzJ1*tWz5XItIDTDmjTe*05MddHWXpGNbvma1f4aBlbdJsC-IIll9girA==";
//        //  $url = "http://weixin.sogou.com/weixin?query=" . $obj . "&_sug_type_=&sut=4974927&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=" . $type . "&sst0=1520587565001&page=".$x."&ie=utf8&w=01019900&dr=1";
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        $cookie_file = 'cookie.txt';//cookie地址
//        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
//        //执行并获取HTML文档内容
//        $output = curl_exec($ch);
//
//        //释放curl句柄
//        curl_close($ch);
//        //打印获得的数据
//       // print_r($output);
//        $regex = "/\"content_url\":\"(.*?)\",/ism";
//        //preg_match_all('/var user_name = \"(.*?)\";/si',$output,$m);
//        preg_match_all($regex, $output, $article);
//
//        for($i=0;$i<count($article[0]);$i++){
//            $article[0][$i] = str_replace(array("\"content_url\":\"",'",'), "", $article[0][$i]);
//            if($article[0][$i] != false){
//                if(strpos($article[0][$i],'http') === false){
//                    $article[0][$i] = "https://mp.weixin.qq.com".$article[0][$i];
//                }
//            }
//
//        }
//       // var_dump($article[0]);
//        $articleurl = array_values(array_filter($article[0]));
//        // var_dump($articleurl);
//       // var_dump(strrpos($articleurl[0],'我'));die();
//        for($i=0;$i<count($articleurl);$i++){
//            if(strrpos($articleurl[$i],'amp;')) {
//                $articleurl[$i] = str_replace("amp;", "", $articleurl[$i]);
//            }
//        }
//        // var_dump("<pre>",$articleurl);die();
//       $this->test($articleurl);
//        die();
        $re = DB::connection('mysqlfl')->select('select * from cl WHERE class_name = "' . $obj . '"');
        if ($re == false) {
            $class_id = time() . rand(100000, 999999);
            DB::connection('mysqlfl')->insert('insert into cl (class_id,class_name,class_createtime) VALUES (?,?,?)', [$class_id, $obj, time()]);
        } else {
            $class_id = $re[0]->class_id;
        }
        $ch = curl_init();
        //设置选项，包括URL
        $url = "http://weixin.sogou.com/weixin?type=" . $type . "&s_from=input&query=" . $obj . "&page=" . $page . "&ie=utf8&_sug_=n&_sug_type_=";
        //  $url = "http://weixin.sogou.com/weixin?query=" . $obj . "&_sug_type_=&sut=4974927&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=" . $type . "&sst0=1520587565001&page=".$x."&ie=utf8&w=01019900&dr=1";
//        $rand_ip=$this->Rand_IP();
//
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$rand_ip, 'CLIENT-IP:'.$rand_ip));//伪造IP
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $cookie_file = 'cookie.txt';//cookie地址
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        //print_r($output);die();
        /***
         * 先按li分割 匹配公众号详情url、文章最新时间、头像、介绍、二维码、主体、最新文章标题、昵称 存入数组
         **/
        $regex = "/<li id=\"sogou.*?>.*?<\/li>/ism";
        preg_match_all($regex, $output, $li);
        //  var_dump($li);
        for ($i = 0; $i < count($li[0]); $i++) {
            $regex1 = "/<label name=\"em_weixinhao\".*?>.*?<\/label>/ism";
            $regex2 = "/<img height=\"32\" width=\"32\" class=\"shot-img\" src=\".*?\"/ism";
            $regex3 = "/<img height=\"104\" width=\"104\" src=\".*?\"/ism";
            $regex4 = "/<dt>功能介绍：<\/dt.*?>.*?<\/dd>/ism";
            $regex5 = '/认证：<\/dt.*?>.*?<\/dd>/ism';
            $regex6 = "/\p{Han}{0,10}\w{0,10}<em><!--red_beg-->" . $obj . "<!--red_end--><\/em>\p{Han}{0,10}\w{0,10}<\/a>/uism";
            $regex7 = "/<dt>最近文章：<\/dt>.*?>.*?<\/a>/ism";
            $regex8 = "/<script.*?>.*?<\/script>/ism";
            preg_match_all($regex8, $li[0][$i], $latesttime);
            $regex8 = "/\d{10}/";
//            $regex9 = "/<a target=\"_blank\" uigs=.*?\">.*?<\/a>/ism";
//            preg_match_all($regex9, $li[0][$i], $infourl);//公众号详情url
            $regex9 = "/href=\".*?\"/ism";
            preg_match_all($regex9, $li[0][$i], $infourl);//公众号详情url
            preg_match_all($regex8, implode("", $latesttime[0]), $latesttime);//最新时间
            preg_match_all($regex1, $li[0][$i], $acount);//gh_name
            preg_match_all($regex2, $li[0][$i], $headerimg);//头像
            preg_match_all($regex3, $li[0][$i], $qrcode);//二维码
            preg_match_all($regex4, $li[0][$i], $introduce);//介绍
            preg_match_all($regex5, $li[0][$i], $subject);//主体
            preg_match_all($regex6, $li[0][$i], $name);//昵称
            preg_match_all($regex7, $li[0][$i], $latestarticle);//最新文章
            if (isset($infourl[0][0])) {
                $infourl[0][0] = str_replace("href=", "", $infourl[0][0]);
                $infourl[0][0] = str_replace("\"", "", $infourl[0][0]);
                $infourl[0][0] = str_replace("amp;", "", $infourl[0][0]);
                $wechat[$i]['infourl'] = $infourl[0][0];
            }

            if (isset($latesttime[0][0])) {
                $wechat[$i]['latesttime'] = $latesttime[0][0];
            } else {
                $wechat[$i]['latesttime'] = '';
            }
            if (isset($latestarticle[0][0])) {
                $latestarticle[0][0] = strip_tags($latestarticle[0][0]);
                $latestarticle[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $latestarticle[0][0]);
                $latestarticle[0][0] = str_replace("最近文章：", "", $latestarticle[0][0]);
                $wechat[$i]['latestarticle'] = $latestarticle[0][0];
            } else {
                $wechat[$i]['latestarticle'] = '';
            }
            if (isset($acount[0][0])) {
                $acount[0][0] = strip_tags($acount[0][0]);
                $wechat[$i]['gh_name'] = $acount[0][0];
            } else {
                $wechat[$i]['gh_name'] = '';
            }
            if (isset($headerimg[0][0])) {
                $headerimg[0][0] = substr($headerimg[0][0], strrpos($headerimg[0][0], 'src="'));
                $headerimg[0][0] = str_replace("\"", "", $headerimg[0][0]);
                $headerimg[0][0] = str_replace("src=", "", $headerimg[0][0]);
                $wechat[$i]['gh_head_img'] = $headerimg[0][0];
            } else {
                $wechat[$i]['gh_head_img'] = '';
            }
            if (isset($qrcode[0][0])) {
//                $qrcode[0][0] = substr($qrcode[0][0], strrpos($qrcode[0][0], 'src="'));
//                $qrcode[0][0] = str_replace("\"", "", $qrcode[0][0]);
//                $qrcode[0][0] = str_replace("amp;", "", $qrcode[0][0]);
//                $qrcode[0][0] = substr($qrcode[0][0], 4);
//                $wechat[$i]['gh_qrcode_url'] = $qrcode[0][0];
//                $wechat[$i]['gh_qrcode_url'] = $this->base64EncodeImage($wechat[$i]['gh_qrcode_url']);
                $wechat[$i]['gh_qrcode_url'] = '';
            } else {
                $wechat[$i]['gh_qrcode_url'] = '';
            }
            if (isset($introduce[0][0])) {
                $introduce[0][0] = strip_tags($introduce[0][0]);
                $introduce[0][0] = str_replace(array("\r\n", "\r", "\n", "功能介绍："), "", $introduce[0][0]);
                $wechat[$i]['gh_signature'] = $introduce[0][0];
            } else {
                $wechat[$i]['gh_signature'] = '';
            }
            if (isset($subject[0][0])) {
                $subject[0][0] = strip_tags($subject[0][0]);
                $subject[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $subject[0][0]);
                $subject[0][0] = str_replace("认证：", "", $subject[0][0]);
                $wechat[$i]['gh_principal_name'] = $subject[0][0];
            } else {
                $wechat[$i]['gh_principal_name'] = '';
            }
            if (isset($name[0][0])) {
                $name[0][0] = strip_tags($name[0][0]);
                $wechat[$i]['gh_nick_name'] = $name[0][0];
            } else {
                $wechat[$i]['gh_nick_name'] = '';
            }
        }
        //var_dump($wechat);
        /***
         * $wechat 微信公众号数据
         * end
         ***/
        for ($i = 0; $i < count($wechat); $i++) {
            $ch = curl_init();
            //设置选项，包括URL
            $url = $wechat[$i]['infourl'];
            //  $url = "http://weixin.sogou.com/weixin?query=" . $obj . "&_sug_type_=&sut=4974927&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=" . $type . "&sst0=1520587565001&page=".$x."&ie=utf8&w=01019900&dr=1";
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $cookie_file = 'cookie.txt';//cookie地址
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
            //执行并获取HTML文档内容
            $output = curl_exec($ch);

            //释放curl句柄
            curl_close($ch);
            //打印获得的数据
            //  print_r($output);
            $regex = "/\"content_url\":\"(.*?)\",/ism";
            //preg_match_all('/var user_name = \"(.*?)\";/si',$output,$m);
            preg_match_all($regex, $output, $article);
            /***
             * 将匹配的文章url进行进一步匹配
             ***/
            for ($j = 0; $j < count($article[0]); $j++) {
                $article[0][$j] = str_replace(array("\"content_url\":\"", '",'), "", $article[0][$j]);
                if ($article[0][$j] != false) {
                    if (strpos($article[0][$j], 'http') === false) {
                        $article[0][$j] = "https://mp.weixin.qq.com" . $article[0][$j];
                        $article[0][$j] = str_replace("amp;", "", $article[0][$j]);
                    }
                }
            }
            /***
             * 匹配结束
             ***/
            $articleurl = array_values(array_filter($article[0]));//剔除数组中的空值
            /***
             * 将文章url中存在的amp;替换为空
             ***/
//            for ($j = 0; $j < count($articleurl); $j++) {
//                if (strpos($articleurl[$j], 'amp;')) {
//                    $articleurl[$j] = str_replace("amp;", "", $articleurl[$j]);
//                }
//            }
            // var_dump("<pre>",$articleurl);die();
            /***
             *结束
             ***/

            $regex = "/\"cover\":\"(.*?)\",/ism";
            //preg_match_all('/var user_name = \"(.*?)\";/si',$output,$m);
            preg_match_all($regex, $output, $cover);
            $cover = array_values(array_filter($cover[1]));//剔除数组中的空值
            /***
             * 将文章url中存在的amp;替换为空
             ***/
            for ($j = 0; $j < count($cover); $j++) {
                if (strpos($cover[$j], 'amp;')) {
                    $cover[$j] = str_replace("amp;", "", $cover[$j]);
                }
            }
            //  var_dump("<pre>",$cover);die();
            /***
             *结束
             ***/

            for ($j = 0; $j < count($articleurl); $j++) {
                // $url = "https://mp.weixin.qq.com/s?timestamp=1520904009&src=3&ver=1&signature=zm03rXOkyxnrgN-nZEfmW-3Qh6yuyaUMVJsyaxr74jQhztbn6K3Cj4uuUV3ByazGsogGkcoeMZ7oEUVeeAoIcJL4PJ25fDP181VaiRIZccFsMD1x3aqOTBu4xPeh6Ixr*t4aXYL*4cQDJy4tApa-ZEsUMxq1VxW7TuMz4hf*vj8=";
                $url = $articleurl[$j];
                $ch = curl_init();
                //设置选项，包括URL
                curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
                // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                //执行并获取HTML文档内容
                $output = curl_exec($ch);
                //释放curl句柄
                curl_close($ch);
                //打印获得的数据
                //  print_r($output);die();
                preg_match_all("/id=\"js_content\">(.*)<script/iUs", $output, $content, PREG_PATTERN_ORDER);
                if(!isset($content[1][0])){
                    continue;
                }
                $content = "<div id='js_content'>" . $content[1][0];
                $content = str_replace("data-src", "src", $content);
                $article_body = str_replace("preview.html", "player.html", $content);//文章主体
                $regex0 = "/<img .*?>/";
                preg_match_all($regex0, $article_body, $body);
//                /*****
//                 * 替换微信图片地址
//                 */
//                for ($k = 0; $k < count($body[0]); $k++) {
//                    $body[0][$k] = substr($body[0][$k], strrpos($body[0][$k], 'src="'));
//                    $body[0][$k] = str_replace("src=", "", $body[0][$k]);
//                    $tmp[$k]['image'] = $body[0][$k];
//                    $image = explode("\"", $tmp[$k]['image']);
//                    $tmp[$k]['agoimage'] = $image[1];
//                    $tmp[$k]['image'] = $image[1] . "&tp=webp&wxfrom=5&wx_lazy=1";
//                    $article_body = str_replace($tmp[$k]['agoimage'], $tmp[$k]['image'], $article_body);
//                }
//                // print_r($content);
//                // $key = '&tp=webp&wxfrom=5&wx_lazy=1';
//                /*****
//                 * 结束
//                 */
                preg_match_all('/var nickname = \"(.*?)\";/si', $output, $m);
                $gh_nick_name = $m[1][0];//公众号昵称
                //var_dump($gh_nick_name);
                preg_match_all('/var ct = \"(.*?)\";/si', $output, $m);
                $article_createtime = $m[1][0];//文章创建时间
                // var_dump($article_createtime);
                preg_match_all('/var user_name = \"(.*?)\";/si', $output, $m);
                $gh_name = $m[1][0];
                //var_dump($gh_name);//公众号ID
                //die();
                preg_match_all('/var msg_title = \"(.*?)\";/si', $output, $m);
                $article_title = $m[1][0];//文章标题
                // var_dump($article_title);
//                $regex = "/<span class=\"profile_meta_value\">(.*?)<\/span>/ism";
//                preg_match_all($regex, $output, $m);
//                $gh_account = $m[0][0];
                //var_dump($gh_account);
                $regex = "/var mid = \".*?\";/ism";
                preg_match_all($regex, $output, $m);
                $regex = "/\d{1,}/";
                preg_match_all($regex, $m[0][0], $m);
                $log = json_encode($m);
                //log日志文件
                $txt = './log.txt';
                //要写入的内容
                $addLogStr = date('Y-m-d H:i:s') . $log . "---" . $url . "---" . $article_title . "---" . $gh_nick_name . "\r\n";
                //打开资源并将光标设置为末尾
                $fp = fopen($txt, "a+");
                //写入内容
                fwrite($fp, $addLogStr);
                //关闭资源
                fclose($fp);
                if (isset($m[0][0])) {
                    $mid = $m[0][0];
                } else {
                    continue;
                }

                $regex = "/var idx = \".*?\";/ism";
                preg_match_all($regex, $output, $m);
                $regex = "/\d{1,}/";
                if ($m[0][0]) {
                    preg_match_all($regex, $m[0][0], $m);
                    $idx = $m[0][0];
                } else {
                    $idx = 1;
                }
                $article_id = $mid . $idx;
                $re = DB::connection('mysqlfl')->select('select * from arcl WHERE article_id ="' . $article_id . '"');
                if ($re == false) {
                    //第一次循环将username存入对应的公众号表
                    if ($j == 0) {
                        /***
                         * 存储数据gzh
                         **/

                        $re = DB::connection('mysqlgzh')->select('select gh_username from gh WHERE gh_username ="' . $gh_name . '"');
                        if ($re == false) {
                            $re = DB::connection('mysqlgzh')->table('gh')->insert([
                                [
                                    'gh_username' => $gh_name,
                                    'gh_account' => $wechat[$i]['gh_name'],
                                    'gh_nickname' => $wechat[$i]['gh_nick_name'],
                                    'gh_principal' => $wechat[$i]['gh_principal_name'],
                                    'gh_signature' => $wechat[$i]['gh_signature'],
                                    'gh_head_img' => $wechat[$i]['gh_head_img'],
                                    'gh_qrcode_url' => $wechat[$i]['gh_qrcode_url'],
                                    'gh_latesttime' => $wechat[$i]['latesttime'],
                                    'gh_latestarticle' => $wechat[$i]['latestarticle'],
                                ]
                            ]);
                            //储存公众号分类
                            DB::connection('mysqlfl')->insert('insert into ghcl (class_id,gh_username,sct) VALUES (?,?,?)', [$class_id, $gh_name, time()]);
                        }
                        //  var_dump($re);die();
                        /***
                         * 存储数据gzh结束
                         **/

                    }

//            //设定数据
//            $ar = new Ar();
//            $ar->article_title = $article_title;
//            $ar->article_createtime = $article_createtime;
//            $ar->article_body = $article_body;
//            $ar->article_id = $j+1;
//            // $ar->gh_name=$gh_name;
//            //$ar->gh_nick_name=$gh_nick_name;
//             $ar->save();  //保存
                    /****
                     *储存文章
                     ****/
                    $article_describe = strip_tags($article_body);
                    $article_describe = mb_substr(trim($article_describe), 0, 100, 'utf-8') . '......';
                    $article_id = $mid . $idx;
                    $re = DB::select('select article_id from ar WHERE article_id = "' . $article_id . '"');
                    if ($re == false) {
                        DB::table('ar')->insert([
                            'article_id' => $article_id,
                            'article_title' => trim($article_title),
                            'article_createtime' => $article_createtime,
                            'article_body' => json_encode($article_body),
                            'article_idx' => $idx,
                            'article_cover' => $cover[$j],
                            'article_describe' => $article_describe,
                        ]);
                        //var_dump($ar->article_id);
                        //储存文章分类关系
                        DB::connection('mysqlfl')->insert('insert into arcl (class_id,article_id,article_createtime,sct) VALUES (?,?,?,?)', [$class_id, $article_id, $article_createtime, time()]);
                        //储存文章公众号关系
                        DB::connection('mysqlgzh')->insert('insert into ghar (article_id,gh_username,article_createtime,sct) VALUES (?,?,?,?)', [$article_id, $gh_name, $article_createtime, time()]);
                        //  var_dump($re);
                    }
                    /****
                     *储存文章结束
                     ****/
                } else {
                    continue;
                }

            }
        }
    }

    public function base64EncodeImage($url)
    {
        //$url = 'http://mp.weixin.qq.com/rr?src=3&timestamp=1521425281&ver=1&signature=8WtWMVPpMMInDOkisJaN7I7ctIKxdjuBjW1pNzr9oRoQtXvfkotQG7x0MrRjDTcyLMKuZ*5hG-juKf83wZf4xYf5ZMmG4xy7bTLIWZfMg64=';
        $file_name = time() . '.jpg';//文件名
        $hander = curl_init();
        $fp = fopen($file_name, 'wb');
        curl_setopt($hander, CURLOPT_URL, $url);
        curl_setopt($hander, CURLOPT_FILE, $fp);
        curl_setopt($hander, CURLOPT_HEADER, 0);
        curl_setopt($hander, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
        curl_setopt($hander, CURLOPT_TIMEOUT, 60);
        curl_exec($hander);
        curl_close($hander);
        fclose($fp);

        $image_file = $file_name;
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        unlink($file_name);
        return $base64_image;
        //echo "<img src='".$base64_image."'>";
    }

    public function search($obj, $type)
    {
//        $x = 0;
//        while ($x<10) {
        $ch = curl_init();
        //设置选项，包括URL
        $url = "http://weixin.sogou.com/weixin?type=" . $type . "&s_from=input&query=" . $obj . "&ie=utf8&_sug_=n&_sug_type_=";
        //  $url = "http://weixin.sogou.com/weixin?query=" . $obj . "&_sug_type_=&sut=4974927&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=" . $type . "&sst0=1520587565001&page=".$x."&ie=utf8&w=01019900&dr=1";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $cookie_file = 'cookie.txt';//cookie地址
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        // print_r($output);
        $regex = "/<li id=\"sogou.*?>.*?<\/li>/ism";
        preg_match_all($regex, $output, $li);
        //  var_dump($li);
        for ($i = 0; $i < count($li[0]); $i++) {
            $regex1 = "/<label name=\"em_weixinhao\".*?>.*?<\/label>/ism";
            $regex2 = "/<img height=\"32\" width=\"32\" class=\"shot-img\" src=\".*?\"/ism";
            $regex3 = "/<img height=\"104\" width=\"104\" src=\".*?\"/ism";
            $regex4 = "/<dt>功能介绍：<\/dt.*?>.*?<\/dd>/ism";
            $regex5 = '/认证：<\/dt.*?>.*?<\/dd>/ism';
            $regex6 = "/\p{Han}{0,10}<em><!--red_beg-->" . $obj . "<!--red_end--><\/em>\p{Han}{0,10}<\/a>/uism";
            $regex7 = "/<dt>最近文章：<\/dt>.*?>.*?<\/a>/ism";
            $regex8 = "/<script.*?>.*?<\/script>/ism";
            preg_match_all($regex8, $li[0][$i], $newtime);
            $regex8 = "/\d{10}/";
            preg_match_all($regex8, implode("", $newtime[0]), $newtime);//最新时间
            preg_match_all($regex1, $li[0][$i], $acount);//gh_name
            preg_match_all($regex2, $li[0][$i], $headerimg);//头像
            preg_match_all($regex3, $li[0][$i], $qrcode);//二维码
            preg_match_all($regex4, $li[0][$i], $introduce);//介绍
            preg_match_all($regex5, $li[0][$i], $subject);//主体
            preg_match_all($regex6, $li[0][$i], $name);//昵称
            preg_match_all($regex7, $li[0][$i], $newarticle);//最新文章

            if (isset($newtime[0][0])) {
                $wechat[$i]['newtime'] = $newtime[0][0];
            } else {
                $wechat[$i]['newtime'] = '';
            }
            if (isset($newarticle[0][0])) {
                $newarticle[0][0] = strip_tags($newarticle[0][0]);
                $newarticle[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $newarticle[0][0]);
                $newarticle[0][0] = str_replace("最近文章：", "", $newarticle[0][0]);
                $wechat[$i]['newarticle'] = $newarticle[0][0];
            } else {
                $wechat[$i]['newarticle'] = '';
            }
            if (isset($acount[0][0])) {
                $acount[0][0] = strip_tags($acount[0][0]);
                $wechat[$i]['gh_name'] = $acount[0][0];
            } else {
                $wechat[$i]['gh_name'] = '';
            }
            if (isset($headerimg[0][0])) {
                $headerimg[0][0] = substr($headerimg[0][0], strrpos($headerimg[0][0], 'src="'));
                $headerimg[0][0] = str_replace("\"", "", $headerimg[0][0]);
                $headerimg[0][0] = str_replace("src=", "", $headerimg[0][0]);
                $wechat[$i]['gh_head_img'] = $headerimg[0][0];
            } else {
                $wechat[$i]['gh_head_img'] = '';
            }
            if (isset($qrcode[0][0])) {
                $qrcode[0][0] = substr($qrcode[0][0], strrpos($qrcode[0][0], 'src="'));
                $qrcode[0][0] = str_replace("\"", "", $qrcode[0][0]);
                $qrcode[0][0] = str_replace("amp;", "", $qrcode[0][0]);
                $qrcode[0][0] = substr($qrcode[0][0], 4);
                $wechat[$i]['gh_qrcode_url'] = $qrcode[0][0];
            } else {
                $wechat[$i]['gh_qrcode_url'] = '';
            }
            if (isset($introduce[0][0])) {
                $introduce[0][0] = strip_tags($introduce[0][0]);
                $introduce[0][0] = str_replace(array("\r\n", "\r", "\n", "功能介绍："), "", $introduce[0][0]);
                $wechat[$i]['gh_signature'] = $introduce[0][0];
            } else {
                $wechat[$i]['gh_signature'] = '';
            }
            if (isset($subject[0][0])) {
                $subject[0][0] = strip_tags($subject[0][0]);
                $subject[0][0] = str_replace(array("\r\n", "\r", "\n"), "", $subject[0][0]);
                $subject[0][0] = str_replace("认证：", "", $subject[0][0]);
                $wechat[$i]['gh_principal_name'] = $subject[0][0];
            } else {
                $wechat[$i]['gh_principal_name'] = '';
            }
            if (isset($name[0][0])) {
                $name[0][0] = strip_tags($name[0][0]);
                $wechat[$i]['gh_nick_name'] = $name[0][0];
            } else {
                $wechat[$i]['gh_nick_name'] = '';
            }
        }
        var_dump($wechat);

        die();

        /***
         * 存储数据gzh
         **/
        for ($i = 0; $i < count($wechat); $i++) {
            $re = DB::table('gh')->insert([
                ['gh_name' => $wechat[$i]['gh_name'], 'gh_head_img' => $wechat[$i]['gh_head_img'], 'gh_qrcode_url' => $wechat[$i]['gh_qrcode_url'], 'gh_signature' => $wechat[$i]['gh_signature'], 'gh_principal_name' => $wechat[$i]['gh_principal_name'], 'gh_nick_name' => $wechat[$i]['gh_nick_name']]
            ]);
        }
        //  var_dump($users1);die();
        /***
         * 存储数据gzh结束
         **/
//            $x++;
//        }
//        $gh_qrcode_url =  $wechat[0]['gh_qrcode_url'];
//        //var_dump($gh_qrcode_url);
//
//        header("Location:".$gh_qrcode_url);
////        $ch1 = curl_init();
//        //设置选项，包括URL
//        curl_setopt($ch1, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
//        curl_setopt($ch1, CURLOPT_URL, $gh_qrcode_url);
//        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch1, CURLOPT_HEADER, 0);
//        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
        // $cookie_file = 'cookie.txt';//cookie地址
        //curl_setopt($ch1, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
        //执行并获取HTML文档内容
        // $output1 = curl_exec($ch1);
        // var_dump($output1);
        //释放curl句柄
        //  curl_close($ch1);


        /*        $regex1 = "/<label name=\"em_weixinhao\".*?>.*?<\/label>/ism";*/
//        $regex2 = "/<img height=\"32\" width=\"32\" class=\"shot-img\" src=\".*?\"/ism";
//        $regex3 = "/<img height=\"104\" width=\"104\" src=\".*?\"/ism";
        /*        $regex4 = "/<dt>功能介绍：<\/dt.*?>.*?<\/dd>/ism";*/
        /*        $regex5 = '/认证：<\/dt.*?>.*?<\/dd>/ism';*/
//        $regex6 = "/\p{Han}{0,10}<em><!--red_beg-->" . $obj . "<!--red_end--><\/em>\p{Han}{0,10}<\/a>/uism";
//        preg_match_all($regex1, $output, $acount);
//        preg_match_all($regex2, $output, $headerimg);
//        preg_match_all($regex3, $output, $qrcode);
//        preg_match_all($regex4, $output, $introduce);
//        preg_match_all($regex5, $output, $subject);
//        preg_match_all($regex6, $output, $name);
//        for ($i = 0; $i < count($acount[0]); $i++) {
//            $acount[0][$i] = strip_tags($acount[0][$i]);
//        }
//        for ($i = 0; $i < count($headerimg[0]); $i++) {
//            $headerimg[0][$i] = substr($headerimg[0][$i], strrpos($headerimg[0][$i], 'src="'));
//            $headerimg[0][$i] = str_replace("\"", "", $headerimg[0][$i]);
//            $headerimg[0][$i] = str_replace("src=", "", $headerimg[0][$i]);
//        }
//        for ($i = 0; $i < count($qrcode[0]); $i++) {
//            $qrcode[0][$i] = substr($qrcode[0][$i], strrpos($qrcode[0][$i], 'src="'));
//            $qrcode[0][$i] = str_replace("\"", "", $qrcode[0][$i]);
//            $qrcode[0][$i] = substr($qrcode[0][$i], 4);
//        }
//        for ($i = 0; $i < count($introduce[0]); $i++) {
//            $introduce[0][$i] = strip_tags($introduce[0][$i]);
//            $introduce[0][$i] = str_replace(array("\r\n", "\r", "\n"), "", $introduce[0][$i]);
//        }
//        for ($i = 0; $i < count($subject[0]); $i++) {
//            $subject[0][$i] = strip_tags($subject[0][$i]);
//            $subject[0][$i] = str_replace(array("\r\n", "\r", "\n"), "", $subject[0][$i]);
//            $subject[0][$i] = str_replace("认证：", "", $subject[0][$i]);
//        }
//        for ($i = 0; $i < count($name[0]); $i++) {
//            $name[0][$i] = strip_tags($name[0][$i]);
//        }
////        $wechat = array();
////        for($i=0;$i<10;$i++){
////            $wechat[$i]['gh_ name'] = $acount[0][$i];
////            $wechat[$i]['gh_ head_img'] = $headerimg[0][$i];
////            $wechat[$i]['gh_ qrcode_url'] = $qrcode[0][$i];
////            $wechat[$i]['gh_ signature'] = $introduce[0][$i];
////            $wechat[$i]['gh_principal_name'] = $subject[0][$i];
////            $wechat[$i]['gh_ nick_name'] = $name[0][$i];
////        }
//
//        var_dump($acount);
//        var_dump($headerimg);
//        var_dump($qrcode);
//        var_dump($introduce);
//        var_dump($subject);
//        var_dump($name);
//        //  var_dump($wechat);

    }

    public function test($url = 'https://mp.weixin.qq.com/s?src=11&timestamp=1526972401&ver=891&signature=MfOc-XzY3R0JJiZBWLL4a0LQciZRTANhxv*MuRAnElowoJIITd9mRd4vJJWPgFF6KV3aYMUkbmkqM--VNoQda9VCofV98uQJniOxjafGvM4DkjoNjrP3FUmRcVaguhQ5&new=1')
    {

//        $replace = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','_');
//        $articles = DB::connection('mysqlgzh')->table('ghar')->where('article_id',1)->get();
//        var_dump($articles[0]->gh_name);
//        $ghname = str_split($articles[0]->gh_name);
//        for($i=0;$i<count($ghname);$i++){
//            if(in_array($ghname[$i],$replace)){
//               for($j=0;$j<count($replace);$j++){
//                   if($ghname[$i] == $replace[$j]){
//                       $ghname[$i] = $j+1;
//                       $j = count($replace);
//                   }
//               }
//            }
//        }
//        $articles[0]->gh_name = (int)((implode('',$ghname)));
//        var_dump($articles[0]->gh_name);
//        die();
//        $ar = Db::table('ar')->where('article_id',4)->get();
//        var_dump($ar[0]->article_body);
//        var_dump($ar[0]->article_title);
//        DIE();
        //$url = DB::table('tmp_articleurl')->select('url')->get();
        //  $url = json_decode($url);
        // var_dump($url);die();
        // echo count($url);
//        for ($j = 0; $j < count($arr); $j++) {
        //$url = $arr[$j];
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄share_notice
        curl_close($ch);
        //打印获得的数据
        //     print_r($output);die();
        //          if(preg_match("/share_media/", $output) == 1 && preg_match("/share_notice/", $output) == 1){
//               preg_match_all("/<a id=\"js_share_source\" href=\"(.*?)\"/ism",$output,$newlink);
//               var_dump($newlink[1][0]);
//               $this->test($newlink[1][0]);
        //  }else{
        preg_match_all("/id=\"js_content\">(.*)<script/iUs", $output, $content, PREG_PATTERN_ORDER);
        $content = "<div id='js_content'>" . $content[1][0];
        $content = str_replace("data-src", "src", $content);
        $article_body = str_replace("preview.html", "player.html", $content);//文章主体
        $regex0 = "/<img .*?>/";
        preg_match_all($regex0, $article_body, $body);
        for ($i = 0; $i < count($body[0]); $i++) {
            $body[0][$i] = substr($body[0][$i], strrpos($body[0][$i], 'src="'));
            $body[0][$i] = str_replace("src=", "", $body[0][$i]);
            $wechat[$i]['image'] = $body[0][$i];
            $image = explode("\"", $wechat[$i]['image']);
            $wechat[$i]['agoimage'] = $image[1];
            $wechat[$i]['image'] = $image[1] . "&tp=webp&wxfrom=5&wx_lazy=1";
            $article_body = str_replace($wechat[$i]['agoimage'], $wechat[$i]['image'], $article_body);
        }
        var_dump($article_body);
        // print_r($content);
        // $key = '&tp=webp&wxfrom=5&wx_lazy=1';
        //$html变量的值是前面获取到的文章全部html
        $regex = "/<span class=\"profile_meta_value\">(.*?)<\/span>/ism";
        preg_match_all($regex, $output, $m);
        $gh_account = $m[0][0];
        var_dump($gh_account);
        $regex = "/var mid = \"\" \|\| \"\"\|\| \"(.*?)\";/ism";
        preg_match_all($regex, $output, $m);
        $mid = $m[1][0];
        var_dump($mid);
        $regex = "/var idx = \"\" \|\| \"\" \|\| \"(.*?)\";/ism";
        preg_match_all($regex, $output, $m);
        $idx = $m[1][0] ? $m[1][0] : 1;
        var_dump($idx);
        preg_match_all('/var nickname = \"(.*?)\";/si', $output, $m);
        $gh_nick_name = $m[1][0];//公众号昵称
        var_dump($gh_nick_name);
        preg_match_all('/var ct = \"(.*?)\";/si', $output, $m);
        $article_createtime = $m[1][0];//文章创建时间
        var_dump($article_createtime);
        preg_match_all('/var user_name = \"(.*?)\";/si', $output, $m);
        $gh_name = $m[1][0];
        var_dump($gh_name);//公众号ID
        //die();
        preg_match_all('/var msg_title = \"(.*?)\";/si', $output, $m);
        $article_title = $m[1][0];//文章标题
        var_dump($article_title);
        //第一次循环将username存入对应的公众号表
//            if ($j == 0) {
//
//            }
//            //设定数据
//            $ar = new Ar();
//            $ar->article_title = $article_title;
//            $ar->article_createtime = $article_createtime;
//            $ar->article_body = $article_body;
//            $ar->article_id = $j+1;
//            // $ar->gh_name=$gh_name;
//            //$ar->gh_nick_name=$gh_nick_name;
//             $ar->save();  //保存
        /******
         *
         *
         * DB::table('ar')->insert([
         * 'article_id' => $mid.$idx,
         * 'article_title' => trim($article_title),
         * 'article_createtime' => $article_createtime,
         * 'article_body' => json_encode($article_body),
         * 'article_idx' => $idx,
         * ]);
         *****
         */
        //var_dump($ar->article_id);
        //$articles = DB::connection('mysqlgzh')->insert('insert into ghar (article_id,gh_username) VALUES (?,?)',[$mid.$idx,$gh_name]);
        //  var_dump($re);
        //     }


//        }
    }

    public function test1()
    {
        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/profile?src=3&timestamp=1524638636&ver=1&signature=tY3j8nhabvF7DgAwWXWlblGAnYSzBCJIsD-0CYhXKgLXsqjZxXbmCM5*-s5dUHOmEBa0E3zcIi2*QYOZnf9gYA==");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        print_r($output);
        $regex = "/\"cover\":\"(.*?)\",/ism";
        //preg_match_all('/var user_name = \"(.*?)\";/si',$output,$m);
        preg_match_all($regex, $output, $cover);
        var_dump("<pre>", $cover);


        if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u ', '测试')) {
            echo 'ok';
        } else {
            echo 'no';
        }
        die();
        //$regex4="@立\p{Han}.*着@u";//匹配汉字
        //$regex4="/碘化\p{Han}{1,10}/u";//匹配汉字
        //  $regex4="/([\x{4e00}-\x{9fa5}]+)/u";
        $regex4 = "/<dt class=\"reference-title\".*?>.*?<\/dt>/ism";
        preg_match_all($regex4, $output, $matches4);
        var_dump($matches4);
//        $html = file_get_contents("http://weixin.sogou.com/weixin?type=".$type."&s_from=input&query=".$obj."&ie=utf8&_sug_=n&_sug_type_=");
//        preg_match_all("/id=\"js_content\">(.*)<script/iUs",$html,$content,PREG_PATTERN_ORDER);
//        $content = "<div id='js_content'>".$content[1][0];
//        $content = str_replace("data-src","src",$content);
//        $content = str_replace("preview.html","player.html",$content);
//        // var_dump($content);
//        $key = '&tp=webp&wxfrom=5&wx_lazy=1';
//        //$html变量的值是前面获取到的文章全部html
//        preg_match_all('/var nickname = \"(.*?)\";/si',$html,$m);
//        $nickname = $m[1][0];//公众号昵称
//        preg_match_all('/var round_head_img = \"(.*?)\";/si',$html,$m);
//        $head_img = $m[1][0];//公众号头像
//        var_dump($nickname);
//        var_dump($head_img);

    }

    public function test2()
    {
        $ch = curl_init();
        $url = "https://mp.weixin.qq.com/s?__biz=MzAxNTc2NzI1Ng==&mid=2650630048&idx=2&sn=2d93054feebb457b1a66a5d03991e758&chksm=83f6c417b4814d01af4b28cf6183824396cb04ddd6a8f732c1470f01cc637121d50fdea423a4&scene=27#wechat_redirect";
        //设置选项，包括URL
        //$url="https://mp.weixin.qq.com/s?timestamp=1520928827&src=3&ver=1&signature=zzTnkz94dM00MZOvv9wFRFOjB9Ve7Wn2x8ojSHd8bUzckwRVcEVN8A-GNBA*airJ8YH4tnh29nqW22-cFgHHAh6qmbcdo9gVxp599zLlfUkSlrYpgZThmVzEXsakQdFU3PzbJ2QEaTLxRqpyca6LOeNMO4WP94jWU2EijJzjFA0=";
        $url = "http://mp.weixin.qq.com/s?__biz=MzAxNTc2NzI1Ng==&amp;mid=2650630048&amp;idx=2&amp;sn=2d93054feebb457b1a66a5d03991e758&amp;chksm=83f6c417b4814d01af4b28cf6183824396cb04ddd6a8f732c1470f01cc637121d50fdea423a4&amp;scene=27#wechat_redirect";
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        // print_r($output);
        $regex = "/var mid = \".*?\";/ism";
        preg_match_all($regex, $output, $m);
        $regex = "/\d{1,}/";
        if ($m) {
            preg_match_all($regex, $m[0][0], $m);
            $mid = $m[0][0];
        } else {
            // $idx = 1;
        }
        var_dump($mid);

//
//        $a = D('gh');
//        for($i=0;$i<100;$i++){
//            $re[$i]  =$a->where(array('text'=>'123'))->select();
//        }
//        for($i=0;$i<100;$i++){
//            $re[$i]  =D('gh')->where(array('text'=>'123'))->select();
//        }
    }

    public function show($obj)
    {

        // $users = DB::select('select * from users');
        // dd($users);
        //$users = DB::select('select  NAME,age from users where id = ?', [$id]);
        //DB::insert('insert into users (id,name,age) VALUES (?,?,?)',[5,'睡意',17]);
        // DB::update('update users set name = \'睡意1\' where id = ?', [5]);
        //DB::delete('delete from users  where id = ?', [5]);
//        foreach ($users as $user) {
//            echo $user->name;
//        }
//        $users1 = DB::table('users')->get();
//        $users1 = DB::table('users')->first();
//        $users1 = DB::table('users')->orderBy('id','desc')->first();
//        $users1 = DB::table('users')->where('id','<',4)->get();
//        $users1 = DB::table('users')->whereRaw('id<? and age>=?',[4,15])->get();
//        $users1 = DB::table('users')->pluck('name');
//        $users1 = DB::table('users')->pluck('name','age');
//        $users1 = DB::table('users')->select('name','age')->get();
        //  dd($users);
//        foreach ($users1 as $user) {
//            echo $user->name;
//        }
//        return view('user.profile',['user1'=>$users1]);
//        return view('user.profile', ['user' => User::findOrFail($id),'name'=>'bestzz'])->with('level', '最高级');


        //$users = DB::table('users')->paginate(2);
        // return view('user.profile', ['users' => $users]);
    }

    public function query()
    {
//        $re = DB::table('users')->insert(
//            ['id' => 5, 'name' => '吊炸天']
//        );
//        $re = DB::table('users')->delete(
//            ['id' => 5]
//        );
//        $re = DB::table('users')
//            ->where('id', 4)
//            ->update(
//                ['name' => '帅气~']
//            );
//        $re = DB::table('users')->increment('age', 14);//该字段加14  1不用写
//        $re = DB::table('users')->increment('age', 14);//该字段减14  1不用写
//        $re = DB::table('users')
//            ->where('id', 1)->decrement('age', 1, ['name' => '小仙女']);//该字段减1
//        $re = DB::table('users')->
//        where('id', '>=', 6)
//            ->delete();
//        var_dump($re);
    }

    public function query1()
    {

        $count = DB::table('users')->count();
        $max = DB::table('users')->max('id');
        $avg = DB::table('users')->avg('age');
        $sum = DB::table('users')->sum('age');
        echo $sum;
    }

    public function tswoole()
    {
    }
}