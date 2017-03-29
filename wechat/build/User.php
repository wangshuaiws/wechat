<?php
/**
 * Created by PhpStorm.
 * User: hs
 * Date: 2017/1/17
 * Time: 20:43
 */
namespace wechat\build;
use wechat\wx;

class user extends wx{
    public function user($openid,$lang='zh_CN'){
        $url = $this->url.'/cgi-bin/user/info?access_token='.$this->getAccessToken().'&openid='.$openid.'&lang=zh_CN ';
        $result = $this->curl($url);
        return $this->get(json_decode($result,true));

    }
}