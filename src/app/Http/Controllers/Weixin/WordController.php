<?php


namespace App\Http\Controllers\Weixin;


use App\Http\Controllers\Controller;
use http\Client;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use WXBizDataCrypt;
use function MongoDB\BSON\toJSON;

class WordController extends Controller
{
    private $result = array(
        "code"=>0,
        "message"=>"成功",
        "data"=>array()
    );

    private function getOpenId($js_code,$encryptedData,$iv){
        $base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        include_once $base_path."/lib/weixin/wxBizDataCrypt.php";

        $appid = 'wxd1a3a2fc85116b1c';
        $app_secret = "44b84933fcf0129819f24cb0c255530a";
        $login_code = $js_code;

        //通过js_code 获取 session_key
        $http_client = new \GuzzleHttp\Client();
        $res = $http_client->get("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$app_secret}&js_code={$login_code}&grant_type=authorization_code")->getBody()->getContents();
        $res_arr = json_decode($res,true);

        if(isset($res_arr['openid'])){
            return $res_arr['openid'];
        }

        $sessionKey = $res_arr['session_key'];
        $open_id = "openid";

        $encryptedData=$encryptedData;

        $data = null;
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $wechat_userinfo = json_decode($data,true);
            $open_id = $wechat_userinfo['openId'];
            return $open_id;
        } else {
            return "";
        }
    }

    public function wordList(Request $request){
//        $nextNum = $request->get("nextNum");
        $level = $request->get('level');
        $words = array(
            "count"=>0,
            "data"=>array()
        );
        if($level == 0){
            $tmp_ids = array();
            $ids = DB::table("word")->get(array('id'))->toArray();
            foreach ($ids as $key=>$value){
                array_push($tmp_ids,$value->id);
            }

            $rand_num = rand(0,count($tmp_ids)-1);
            $id = $tmp_ids[$rand_num];
            //记录随机单词
            DB::table("user_rand_word")->insert(array(
                "word_id"=>$id
            ));
            $word_db = DB::table("word")->find($id);
            $words["data"]["word"]=$word_db->word;
            $words['data']["description"] = $word_db->description;
            $words["count"]=count($tmp_ids);
            return response()->json($words);
        }else{
            $tmp_ids = array();
            $ids = DB::table("word")->where("level_".$level,"=",1)->get(array('id'))->toArray();
            foreach ($ids as $key=>$value){
                array_push($tmp_ids,$value->id);
            }
            $rand_num = rand(0,count($tmp_ids)-1);
            $id = $tmp_ids[$rand_num];
            //记录随机单词
            DB::table("user_rand_word")->insert(array(
                "word_id"=>$id
            ));
            $word_db = DB::table("word")->find($id);
            $words["data"]["word"]=$word_db->word;
            $words['data']["description"] = $word_db->description;
            $words["count"]=count($tmp_ids);
            return response()->json($words);
        }

    }

    /**
     * 随机获取默写单词
     */
    public function getMoxieWord(){
        $max_id = DB::table("word")->max("id");
        if(empty($max_id)){
            $this->result['code'] = 1;
            return response()->json($this->result);
        }
        $rand_num = rand(23,$max_id);
        $word_row = DB::table("word")->find($rand_num);
        if(empty($word_row)){
            $this->result['code'] = 1;
            return response()->json($this->result);
        }

        $this->result['data'] = array(
            "id"=>$word_row->id,
            "word"=>$word_row->word,
            "description"=>$word_row->description
        );
        return response()->json($this->result);
    }

    public function test(Request $request){
        //$list = DB::table("word")->where("word","lotxx")->first();
        //$db = DB::insert('insert into word (id, word,description) values (?, ?,?)', [null, 'Dayle',"n. 黛尔（女子名）"]);
        $url = "https://www.youdao.com/w/a/#keyfrom=dict2.top";
        $html = \phpspider\core\requests::get($url);
        // 选择器规则
        $selector = "//div[contains(@id,'phrsListTab')]//div[contains(@class,'trans-container')]//ul//li";
        // 提取结果
        $result = \phpspider\core\selector::select($html, $selector);
        var_dump($result);
        die();
        foreach ($result as $value){
            echo $value."<br/>";
        }
    }

    public function decodeWeChatUserInfo(Request $request){
        $base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

        include_once $base_path."/lib/weixin/wxBizDataCrypt.php";


        $appid = 'wxd1a3a2fc85116b1c';
        $app_secret = "44b84933fcf0129819f24cb0c255530a";
        $login_code = $request->get("js_code");

        //通过js_code 获取 session_key
        $http_client = new \GuzzleHttp\Client();
        $res = $http_client->get("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$app_secret}&js_code={$login_code}&grant_type=authorization_code")->getBody()->getContents();
        $res_arr = json_decode($res,true);

        $sessionKey = $res_arr['session_key'];
        $open_id = "openid";

        $encryptedData=$request->get("encryptedData");

        $iv = $request->get("iv");
        $data = null;
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $wechat_userinfo = json_decode($data,true);
            $open_id = $wechat_userinfo['openId'];
            //查找opendid是否存在
            $db_res = DB::table("user_bind")->where("user_bind_wechat_openid",$open_id)->first();
            if(empty($db_res)){
                $insert_res = DB::insert('insert into user_bind (user_bind_id, user_bind_user_id,user_bind_wechat_openid) values (?, ?,?)', [null,0,$open_id]);
                return response()->json(array(
                    'code'=>0
                ));
            }
            return response()->json(array(
                'code'=>0
            ));
        } else {
            print($errCode . "\n");
        }
    }

    public function meGetWordList(Request $request){
        $next = $request->get('next',1);
        $page_size  = 20;
        $offset = ($next - 1) * $page_size;
        $list = DB::table('word')->offset($offset)->limit($page_size)->orderBy('word')->get()->toArray();
//        $list = iterator_to_array($list);
        $this->result['data'] = $list;
        return response()->json($this->result);
    }

    //用户功能定制提交
    public function submitFunDiy(Request $request){
        $encryptedData = $request->post('encryptedData');
        $iv = $request->post('iv');
        $js_code = $request->post('js_code');
        $openid = $this->getOpenId($js_code,$encryptedData,$iv);

        $content = $request->post("content");
        if(empty($content)){
            $this->result['code'] = 1;
            $this->result['message'] = "提交内容不能为空";
            return response()->json($this->result);
        }

        $db_res = DB::table("user_bind")->where("user_bind_wechat_openid",$openid)->first();
        if(empty($db_res)){
            $this->result['code'] = 1;
            $this->result['message'] = "请先登录";
            return response()->json($this->result);
        }
        $insert_res = DB::insert('insert into diy_fun (diy_fun_id, user_bind_id,diy_fun_content) values (?, ?,?)', [null,$db_res->user_bind_id,$content]);
        if($insert_res !== true){
            $this->result['code'] = 1;
            $this->result['message'] = "系统繁忙";
            return response()->json($this->result);
        }

        return response()->json($this->result);
    }

    public function submitMeMoxieGroup(Request $request){
        $encryptedData = $request->post('encryptedData');
        $iv = $request->post('iv');
        $js_code = $request->post('js_code');
        $openid = $this->getOpenId($js_code,$encryptedData,$iv);
        $word_id = $request->post("word_id");
        $db_res = DB::table("user_bind")->where("user_bind_wechat_openid",$openid)->first();
        if(empty($db_res)){
            $this->result['code'] = 1;
            $this->result['message'] = "请先登录";
            return response()->json($this->result);
        }

        $me_moxie_res = DB::table("me_moxie")->where(array("user_bind_id"=>$db_res->user_bind_id,"word_id"=>$word_id))->first();
        if(!empty($me_moxie_res)){
            $this->result['code'] = 1;
            $this->result['message'] = "已加入";
            return response()->json($this->result);
        }

        $insert_res = DB::insert('insert into me_moxie (me_moxie_id, user_bind_id,word_id) values (?, ?,?)', [null,$db_res->user_bind_id,$word_id]);
        if($insert_res !== true){
            $this->result['code'] = 1;
            $this->result['message'] = "系统繁忙";
            return response()->json($this->result);
        }

        return response()->json($this->result);
    }

    public function getMeMoxieList(Request $request){
        /**
        SELECT ub.user_bind_id,mm.word_id,w.word FROM user_bind  ub
        LEFT JOIN me_moxie  mm ON
        ub.user_bind_id = mm.user_bind_id
        LEFT JOIN word w
        ON mm.word_id = w.id
        WHERE ub.user_bind_wechat_openid = '';
         */
        $encryptedData = $request->post('encryptedData');
        $iv = $request->post('iv');
        $js_code = $request->post('js_code');
        $openid = $this->getOpenId($js_code,$encryptedData,$iv);

        //获取所有单词id集合
        $wordlist = DB::table("user_bind")
            ->leftJoin("me_moxie",'user_bind.user_bind_id','=','me_moxie.user_bind_id')
            ->leftJoin("word",'me_moxie.word_id','=','word.id')
            ->where('user_bind.user_bind_wechat_openid',$openid)->get()->toArray();

        $wordid_arr = array();
        foreach ($wordlist as $key=>$value){
            $wordid_arr[] = $value->word_id;
        }

        $word_count = count($wordid_arr);
        $rand_num = rand(0,$word_count-1);

        $word_id = $wordid_arr[$rand_num];

        $word_row = DB::table('word')->find($word_id);
        $this->result['data'] = array(
            "id"=>$word_row->id,
            "word"=>$word_row->word,
            "description"=>$word_row->description
        );
        return response()->json($this->result);
    }

    //微信小程序埋点
    public function getClientInfo(Request $request){
        $ip = $request->getClientIp();
        $userAgent = $request->userAgent();
        $uri = $request->get("uri");
        DB::table("site_visitor_record")->insert(array(
            'ip'=>$ip,
            'client_type'=>$userAgent,
            'uri'=>$uri
        ));
        return response()->json($this->result);
    }

    private function checkSignature(Request $request)
    {
        $signature = $request->get("signature");
        $timestamp = $request->get("timestamp");
        $nonce = $request->get("nonce");

        $token = "zhudechao18779175574";
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

    public function checkToken(Request $request){
        if($request->has("echostr")){
            //第一次校验token
            $this->checkSignature($request);
        }
    }

    //统一信息下发
    public function uniformSend(){
        $appid = 'wxd1a3a2fc85116b1c';
        $app_secret = "44b84933fcf0129819f24cb0c255530a";
        $http_client = new \GuzzleHttp\Client();
        $res = $http_client->get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$app_secret);
        $token_arr = json_decode($res->getBody()->getContents(),true);

        $access_token = $token_arr['access_token'];

        $http_client->post("https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=".$access_token,array(

        ));
    }
}
