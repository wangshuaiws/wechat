<?php
namespace wechat\build;
use wechat\wx;
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2017/1/9
 * Time: 15:25
 * 创建按钮
 */
class Button extends wx
{
    public function create($data)
    {
        $url = $this->url."/cgi-bin/menu/create?access_token=".$this->getAccessToken();
        $result = $this->curl($url,$data);
        return $this->get(json_decode($result,true));
    }
    //删除当前使用的全部按钮
    public function remove()
    {
        $url = $this->url."/cgi-bin/menu/delete?access_token=".$this->getAccessToken();
        $result = $this->curl($url);
        return $this->get($result);

    }
    //上传临时素材
 /*   public function up(){
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->getAccessToken();
        $result = $this->curl($url);
        return $this->get($result);
    }*/
}