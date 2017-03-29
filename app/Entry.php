<?php
namespace app;
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL^E_NOTICE);
//业务代码，为了测试微信sdk的功能
use wechat\wx;
class Entry{
    protected $wx;
    public function __construct()
    {
        $config = [
            'token' => 'site',
            'appID' => 'wx9b7b627dd3a6fe93',
            'appsecret' => '7f388292fda0a94e9ca77aeefa77da9c'
        ];
        $this->wx = new wx($config);
        $this->wx->valid();
    }

    public function handler(){
        //获取粉丝基本信息
 /*       $user = $this->wx->instance('user')->user('oj4p-v0H1QRJeTxk3SJu5dQzEPRI');
        print_r($user);*/
 /*       //调用button下面的remove方法，将按钮全部删除
        $ab = $this->wx->instance('button')->remove();
        var_dump($ab);*/
        //创建按钮，调用button的create方法，创建自定义的按钮，传入自定义参数
/*        $json =<<<php
         {
     "button":[
     {
          "type":"click",
          "name":"宝宝",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {
               "type":"view",
               "name":"我的项目",
               "url":"http://www.ws970.cn/"
            },
            {
               "type":"view",
               "name":"我的网页",
               "url":"http://www.ws970.cn/show/"
            },
            ]
       },
	   {
               "type":"click",
               "name":"970",
               "key":"V1001_GOOD"
	   }]
 }
php;
        $data = $this->wx->instance('button')->create($json);
        var_dump($data);*/
//        $d = $this->wx->getAccessToken();
//        echo $d;
//      $message = $this->wx->getMessage();
//      file_put_contents('a.php',var_export($content,true));
//        $this->wx->instance('message')->show();
          $message = $this->wx->getMessage();
//        file_put_contents('a.php',var_export($message,true));
        switch($message->MsgType){
            case "text":
                if($message->Content == '新闻'){
                    echo $this->wx->instance('message')->tuwen();
                }
                if($message->Content == '使用'){
                    echo $this->wx->instance('message')->text('1、回复关键字\'新闻\'即可收到图文信息'."\r\n".'2、回复关键字例如\'cxtq安阳\'即可查询安阳三天内的天气情况'."\r\n".'3、回复关键字\'cxwz黄焖鸡米饭\'即可查询周围店铺情况,不过在此之前要先上传您的地理位置。');
                }
                if($message->Content == '音乐'){
                    echo $this->wx->instance('message')->text("歌曲如下:\n 1,金南玲-逆流成河\n 2,梦然-没有你陪伴真的好孤单\n 3,左小祖咒&钟欣潼-把悲伤留给自己-(电影《罗曼蒂克消亡史》推广曲)");
                }
                if($message->Content == '1'){
                    echo $this->wx->instance('message')->text('111');
                }
                if($message->Content == '2'){
                    echo $this->wx->instance('message')->music();
                }
                if(preg_match('/^cxwz([\x{4e00}-\x{9fa5}]+)/ui',$message->Content,$res)) {
                    $address = $res['1'];
                    $conn = mysqli_connect('localhost','root','abc@?187499','weixin');
                    $sql = "SELECT lx,ly FROM members WHERE wxname = '{$message->FromUserName}'";
                    $result = mysqli_query($conn,$sql);
                    if($row = mysqli_fetch_assoc($result))
                    {
                        $content =
                            "http://api.map.baidu.com/place/search?query=".urlencode($address)."&location={$row['lx']},{$row['ly']}&radius=1000&output=html&coord_type=gcj02 ";
                        echo $this->wx->instance('message')->text($content);

                    }
                    else{
                        echo $this->wx->instance('message')->text('请您先上传地理位置');
                    }
                }
                if(preg_match('/^cxtq([\x{4e00}-\x{9fa5}]+)/ui',$message->Content,$res)) {
                    $address = $res['1'];
                    echo $this->wx->instance('message')->weather($address);
                }

                else{
                    echo $this->wx->instance('message')->text('今天也是充满希望的一天');
                }
            break;
            case "image":
                echo $this->wx->instance('message')->text('好看的图片呢');
                break;
            case "voice":
                echo $this->wx->instance('message')->text('声音美美哒');
                break;
            case "location":
                $Location_X = $message->Location_X;        //纬度
                $Location_Y = $message->Location_Y;        //经度
                $fromUsername = $message->FromUserName;
                $time = time();
                /*$db_conn = new PDO('mysql:host=127.0.0.1;dbname=weixin','root','abc@?187499');
                echo $this->wx->instance('message')->text("我们已经收到您当前所在的位置:\n经度:{$Location_Y}\n纬度:{$Location_X}");
                $sql = "SELECT wxname FROM members WHERE wxname = :users";
                $stmt = $db_conn->prepare($sql);
                $res = $stmt->execute(array(users => $fromUsername));
                $flag = $stmt->rowCount();
                $time = time();
                if($flag){
                    $sql = "UPDATE members SET lx = '{$Location_X}',ly = '{$Location_Y}' WHERE wxname = '{$fromUsername}'";
                    $db_conn->query($sql);
                }else {
                    $sql = "INSERT INTO members (wxname,lx,ly,join_time) VALUES ('{$fromUsername}','{$Location_X}','{$Location_Y}','{$time}')";
                    $db_conn->query($sql);
                }*/
                try{
                    $conn = mysqli_connect('localhost','root','abc@?187499','weixin');
                }catch (PDOException $e){
                    echo $this->wx->instance('message')->text('无法连接数据库');
                }
                //mysqli_query('SET NAMES UTF8');
                $sql = "SELECT wxname FROM members WHERE wxname = '{$fromUsername}'";
                $res = mysqli_query($conn,$sql);
                if(mysqli_fetch_assoc($res))
                {
                    $sql = "UPDATE members SET lx = '{$Location_X}',ly = '{$Location_Y}' WHERE wxname = '{$fromUsername}'";
                    mysqli_query($conn,$sql);
                    echo $this->wx->instance('message')->text('成功更新您的位置');
                }else{
                    $sql = "INSERT INTO members (wxname,lx,ly,join_time) VALUES ('{$fromUsername}','{$Location_X}','{$Location_Y}','$time')";
                    mysqli_query($conn,$sql);
                    echo $this->wx->instance('message')->text('成功插入您的位置');
                }
                break;
            case "event":
                if($message->Event == subscribe) {
                    echo $this->wx->instance('message')->text('感谢您关注了我们,回复关键字\'使用\'即可获得详细帮助');
                }
                break;
        }
//        $this->wx->instance('message')->text('我爱你'.$message->Content);
//        $this->wx->instance('message')->text('你好');
    }
}




?>

