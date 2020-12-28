<?php


namespace App\Http\Controllers\Weixin;
use App\Http\Controllers\Controller;
use http\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpClientKernel;


class ApiController extends Controller
{
    public function create(){
        $body = $this->geturl("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx39685120458e30af&secret=ecb19caa3d3d3da087970607e865dcd4");
        $access_token = $body['access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $data = array(
            "button"=>array(
                "name"=>"梦想小镇",
                "sub_button"=>array(
                    "type"=>"click",
                    "name"=>"博园",
                    "url"=>"http://www.xzdream.cn"
                )
            )
        );
        $res = $this->posturl($url,$data);
        print_r($res);
        die();
    }

    private function geturl($url){
//        $headerArray =array("Content-type:application/json;","Accept:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        return $output;
    }

    private function posturl($url,$data){
        $data  = json_encode($data);
        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        print_r($output);
        die();
//        curl_close($curl);
//        return json_decode($output，true);
    }
}
