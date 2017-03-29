<?php
namespace wechat;
/**
 * 微信操作的基础类
 *class wx;
 * @package wechat
*/
class wx extends error
{
    //微信的配置项
    static $config = [];
    //粉丝发来的消息
    protected $message;
    protected $accessToken;
    protected $url;

    public function __construct(array $config = [])
    {
        if(!empty($config)){
        self::$config = $config;
    }
    $this->url = 'https://api.weixin.qq.com';
    $this->message = $this->parsePostRequestData();
}
    //与微信服务器进行绑定
    public function valid()
    {
        if (isset($_GET["signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"]) && isset($_GET["echostr"])) {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            $token = self::$config['token'];
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                echo $_GET["echostr"];
                exit;
            }
        }
    }

    //使用curl发送get请求,先初始化
/*    public function curlGet( $url ){
        $ch = curl_init();
        //设置我们请求的地址
        curl_setopt($ch,CURLOPT_URL,$url);
        //数据返回后不直接显示
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //禁止证书校验
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        $data = '';
        if(curl_exec($ch)){
            //发送成功，获取数据
            $data = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }*/

     //使用curl发送post请求,先初始化，如果是GET请求不加下面几项，如果是POST
    //加上下面的几项
    public function curl( $url,$fields ){
        $ch = curl_init();
        //设置我们请求的地址
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //数据返回后不直接显示
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
        //禁止证书校验
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        if($fields) {
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded; charset=utf-8",
                "Content-length: ".strlen($fields)
            ));
        }
        $data = '';
        if(curl_exec($ch)){
            //发送成功，获取数据
            $data = curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }

    //利用id与secret生成access_token
    public function getAccessToken(){
        //缓存名
        $cacheName = md5(self::$config['appID'].self::$config['appsecret']);
        //缓存文件
        $file = __DIR__.'/cache/'.$cacheName.'.php';
        if(is_file($file) && filemtime($file)+7000 > time()){
            //缓存有效
            $data = include $file;
        }else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$config['appID'] . '&secret=' . self::$config['appsecret'];
            $accessToken = $this->curl($url);
            $data = json_decode($accessToken, true);
            //获取令牌失败
            if (isset($data['errcode'])) {
                return false;
            }

            file_put_contents($file, '<?php return '. var_export($data,true).';?>');
        }
        //成功获取令牌
        return $this->accessToken = $data['access_token'];
    }

    //返回粉丝发来的消息
    public function getMessage(){
        return $this->message;
    }
    //获取并解析粉丝发过来的消息内容
    private function parsePostRequestData(){
        $postStr = file_get_contents("php://input");
        if( isset( $postStr )){
            return simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        }
    }
    //获取功能实例如消息管理实例
    public function instance( $name ){
        $class ='\wechat\build\\' . ucfirst($name);
        return new $class;
    }
}