<?php


namespace App\Http\Controllers\Weixin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class IndexController extends Controller
{
    private function checkSignature(Request $request)
    {
        $signature = $request->get("signature");
        $timestamp = $request->get("timestamp");
        $nonce = $request->get("nonce");

        $token = "zhudechao";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $request->get("echostr");
            exit();
        }else{
            return false;
        }
    }

    public function index(Request $request)
    {
        if($request->has("echostr")){
            //第一次校验token
            $this->checkSignature($request);
        }

        $content = $request->getContent();
        //解析post来的XML为一个对象$postObj
        $postObj = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        $RX_TYPE = trim($postObj->MsgType);
        if($RX_TYPE == 'event'){
            $resultStr = $this->receiveEvent($postObj);
            echo $resultStr;
            exit();
        }else{
            $fromUsername = $postObj->FromUserName; //请求消息的用户
            $toUsername = $postObj->ToUserName; //"我"的公众号id
            $keyword = trim($postObj->Content); //消息内容
            $time = time(); //时间戳
            $msgtype = 'text'; //消息类型：文本
            $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";

            if ($keyword != '' && !in_array($keyword,array("梦想小镇"))) {
                $contentStr = "你好,非常高兴响应你内容，猿艺人生正在起航!\n";
                $contentStr .= "你可以输入关键字【梦想小镇】";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgtype, $contentStr);
                echo $resultStr;
                exit();
            }
            elseif($keyword == "梦想小镇"){
                $textTpl = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
    <item>
      <Title><![CDATA[%s]]></Title>
      <Description><![CDATA[%s]]></Description>
      <PicUrl><![CDATA[%s]]></PicUrl>
      <Url><![CDATA[%s]]></Url>
    </item>
  </Articles>
</xml>";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "news", "1","梦想小镇",'猿猿的博园','http://www.xzdream.cn/assets/avatar.jpg','http://www.xzdream.cn');
                echo $resultStr;
                exit();
            }
        }


    }

    private function receiveEvent($object){
        $content = "";
        switch ($object->Event){
            case "subscribe":
                $content = "欢迎关注猿艺人生，在这里你可以给我留言我们一起探讨。猿艺人生也会更新最新动态。发布技术，猿生点滴!";//这里是向关注者发送的提示信息
                $content .= "您可以尝试输入一下内容";
                break;
            case "unsubscribe":
                $content = "";
                break;
        }
        $result = $this->transmitText($object,$content);
        return $result;
    }

    private function transmitText($postObj,$contentStr){
        $fromUsername = $postObj->FromUserName; //请求消息的用户
        $toUsername = $postObj->ToUserName; //"我"的公众号id
        $keyword = trim($postObj->Content); //消息内容
        $time = time(); //时间戳
        $msgtype = 'text'; //消息类型：文本
        $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";

        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgtype, $contentStr);
        echo $resultStr;
        exit();
    }
}
