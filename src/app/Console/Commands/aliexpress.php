<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use QL\QueryList;
use QL\Ext\PhantomJs;

class aliexpress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        require_once "/Users/zhudechao/githup/web_portals/vendor/autoload.php";
        require_once "/Users/zhudechao/githup/web_portals/src/lib/QueryListPhantomJS/PhantomJs.php";
        $url = "https://www.aliexpress.com/item/4000434911552.html?spm=a2g01.12616982.tplist001.3.fc10601cisOonE&gps-id=5950812&scm=1007.23961.125497.0&scm_id=1007.23961.125497.0&scm-url=1007.23961.125497.0&pvid=b7d7d77a-4756-4f22-9641-754a92786d81";
        //采集某页面所有的图片
        $ql = QueryList::getInstance();
        $ql->use(PhantomJs::class,"/Users/zhudechao/githup/web_portals/src/lib/phantomjs","browser");
        $script_arr = $ql->browser($url)->find("script")->texts()->all();
        foreach ($script_arr as $key=>$value){
            if(strpos($value,"window.runParams") !== false){
                print_r($value);
                die();
            }
        }
        die();
        // 选择器规则
//        $selector = "//div[contains(@id,'root')]";
        $selector = "//body";
        // 提取结果
        $word_arr = \phpspider\core\selector::select($html, $selector);
        print_r($word_arr);
        die();
    }
}
