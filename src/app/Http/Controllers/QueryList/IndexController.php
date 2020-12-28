<?php


namespace App\Http\Controllers\QueryList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use QL\QueryList;
use QL\Ext\PhantomJs;

class IndexController extends Controller
{
    public function index(){
        return view("query_list.index");
    }

    public function caijiView(Request $request){
        $id = $request->get('id');
        $db_res = DB::table('aliexpress')->where(array(
            'aliexpress_id'=>$id
        ))->get();

        $list = DB::table("aliexpress_order")->where("aliexpress_id","=",$id)->get();
        return view("query_list.caiji",array('info'=>$db_res[0],'list'=>$list));
    }

    //采集
    public function caiji(Request $request){
        $url = $request->post('url');
        $id = $request->post('id');

        $base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        $base_path1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        require_once "{$base_path1}/vendor/autoload.php";
        require_once "{$base_path}/lib/QueryListPhantomJS/PhantomJs.php";
//        $url = "https://www.aliexpress.com/item/4000434911552.html?spm=a2g01.12616982.tplist001.3.fc10601cisOonE&gps-id=5950812&scm=1007.23961.125497.0&scm_id=1007.23961.125497.0&scm-url=1007.23961.125497.0&pvid=b7d7d77a-4756-4f22-9641-754a92786d81";
        //采集某页面所有的图片
        $ql = QueryList::getInstance();
        $ql->use(PhantomJs::class,"{$base_path}/lib/phantomjs","browser");
        $script_arr = $ql->browser($url)->find("script")->texts()->all();
        foreach ($script_arr as $key=>$value){
            if(strpos($value,"window.runParams") !== false){
                $one = strpos($value,'"tradeCount"')+13;
                $two = strpos($value,',"tradeCountUnit"');
                $order_num = substr($value,$one,$two-$one);
                //
                DB::table("aliexpress_order")->insert(array(
                    "aliexpress_id"=>$id,
                    "order_number"=>$order_num
                ));
                return response()->json(array(
                    'code'=>0,
                    'data'=>array(
                        'order_num'=>$order_num
                    )
                ));
            }
        }
        return response()->json(array(
            'code'=>1,
            'data'=>array()
        ));
    }

    //保存采集信息
    public function saveCaiji(Request $request){
        $url = $request->post('url');
        $name = $request->post('name');

        $db_ret = DB::table("aliexpress")->insert(array(
            'aliexpress_name'=>$name,
            'aliexpress_url'=>$url
        ));

        if($db_ret !== true){
            return response()->json(array(
                'code'=>1,
                'data'=>array(),
                'message'=>'系统繁忙，稍后重试'
            ));
        }

        return response()->json(array(
            'code'=>0,
            'data'=>array()
        ));
    }

    //获取采集资源列表
    public function getCaijiUrlList(Request $request){
        $list = DB::table('aliexpress')->get()->toArray();
        return response()->json(array(
            'code'=>0,
            'data'=>$list
        ));
    }

    //计划任务
    public function crontabStart(){
        $list = DB::table('aliexpress')->get()->toArray();
        foreach ($list as $value){
            $this->crontabCaiji($value->aliexpress_id,$value->aliexpress_url);
        }

        return response()->json(array(
            'code'=>0,
            'data'=>array()
        ));
    }

    private function crontabCaiji($id,$url){
        $url = $url;
        $id = $id;

        $base_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        $base_path1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        require_once "{$base_path1}/vendor/autoload.php";
        require_once "{$base_path}/lib/QueryListPhantomJS/PhantomJs.php";
//        $url = "https://www.aliexpress.com/item/4000434911552.html?spm=a2g01.12616982.tplist001.3.fc10601cisOonE&gps-id=5950812&scm=1007.23961.125497.0&scm_id=1007.23961.125497.0&scm-url=1007.23961.125497.0&pvid=b7d7d77a-4756-4f22-9641-754a92786d81";
        //采集某页面所有的图片
        $ql = QueryList::getInstance();
        $ql->use(PhantomJs::class,"{$base_path}/lib/phantomjs","browser");
        $script_arr = $ql->browser($url)->find("script")->texts()->all();
        foreach ($script_arr as $key=>$value){
            if(strpos($value,"window.runParams") !== false){
                $one = strpos($value,'"tradeCount"')+13;
                $two = strpos($value,',"tradeCountUnit"');
                $order_num = substr($value,$one,$two-$one);
                //
                DB::table("aliexpress_order")->insert(array(
                    "aliexpress_id"=>$id,
                    "order_number"=>$order_num
                ));
            }
        }
    }
}
