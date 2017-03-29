<?php
namespace wechat\build;
use wechat\wx;

/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2017/1/6
 * Time: 15:37
 */
class Message extends wx
{
    public function text($content)
    {
        $xml = '
           <xml>
 <ToUserName><![CDATA[%s]]></ToUserName>
 <FromUserName><![CDATA[%s]]></FromUserName>
 <CreateTime>%s</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[%s]]></Content>
 </xml>';
        $text = sprintf($xml, $this->message->FromUserName, $this->message->ToUserName, time(), $content);
        header('Content-type:application/xml');
        echo $text;
    }
    public function tuwen(){
        $text = '
        <xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>3</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
</Articles>
</xml>
        ';
        $title1 = "宝宝";
        $title2 = "喜欢你";
        $title3 = "爱情";
        $ds1 = "美丽的姑娘哟";
        $ds2 = "我好想你，真的好想你";
        $ds3 = "爱情可以使你骄傲如烈日，也能让我卑微似尘土";
        $picurl1 = "http://www.ws970.cn/images/1.jpg";
        $picurl2 = "http://www.ws970.cn/images/2.jpg";
        $picurl3 = "http://www.ws970.cn/images/3.jpg";
        $url1 = "www.news.qq.com";
        $url2 = "news.163.com";
        $url3 = "news.sina.com.cn";
        $tuwen = sprintf($text, $this->message->FromUserName, $this->message->ToUserName,
            time(),$title1,$ds1,$picurl1,$url1,$title2,$ds2,$picurl2,$url2,$title3,$ds3,$picurl3,$url3);
        header('Content-type:application/xml');
        echo $tuwen;

    }

    public function weather($address){
        $text = '
        <xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>4</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title>
</item>
<item>
<Title><![CDATA[%s]]></Title>
</item>
<item>
<Title><![CDATA[%s]]></Title>
</item>
<item>
<Title><![CDATA[%s]]></Title>
</item>
</Articles>
</xml>
        ';
        /*$text = '
           <xml>
 <ToUserName><![CDATA[%s]]></ToUserName>
 <FromUserName><![CDATA[%s]]></FromUserName>
 <CreateTime>%s</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[%s]]></Content>
 </xml>';*/
        $add = 'theCityName='.$address;
        $data = $this->curl('http://www.webxml.com.cn/WebServices/WeatherWebService.asmx/getWeatherbyCityName',$add);
        $a = simplexml_load_string($data);
        //$weather =sprintf($text, $this->message->FromUserName, $this->message->ToUserName, time(),$a->string[0]);

        $title1 = $a->string[0].$a->string[1].'    '.'天气预报';
        $title2 = $a->string[6].$a->string[5].'  '.$a->string[7];
        $title3 = $a->string[13].$a->string[12].'  '.$a->string[14];
        $title4 = $a->string[18].$a->string[17].'  '.$a->string[19];
        //$title5 = $a->string['1'].'简介:'.$a->string[22];
        $weather =sprintf($text, $this->message->FromUserName, $this->message->ToUserName,
            time(),$title1,$title2,$title3,$title4);
        header('Content-type:application/xml');
        echo $weather;
    }

    public function music(){
        $musics = <<<XML
        <xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
</Music>
</xml>
XML;
        $title = "逆流成河";
        $url = "http://www.ws970.cn/music/逆流成河.mp3";
        $music = sprintf($musics, $this->message->FromUserName, $this->message->ToUserName, time(),$title,$url,$url);
       // header('Content-type:application/xml');
        echo $music;
    }
//<ThumbMediaId><![CDATA[R6u9rlI2yUCYy8tha9m7rH9i0LAYEriy2K13KjJW6q-JPZkl6UdPh8lKhajXL-P9]]></ThumbMediaId>
//<ThumbMediaId><![XvQIno0Kb_-IEjpHrRwCog0EOv5iM9nNuZZABQrWgZ9_hkpdanvLPL_-bFdtXHw7]]></ThumbMediaId>
}