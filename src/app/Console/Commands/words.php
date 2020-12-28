<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class words extends Command
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
        $url = "http://collocationdictionary.freedicts.com/words";
        $html = \phpspider\core\requests::get($url);
        // 选择器规则
        $selector = "//body//li//a";
        // 提取结果
        $word_arr = \phpspider\core\selector::select($html, $selector);
        $count = 1;

        foreach ($word_arr as $key=>$value){
            $db_res = DB::table("word")->where("word",$value)->first();
            if(!empty($db_res)){
                echo $count++." {$value}:单词已存在\n";
                continue;
            }
            $url = "https://www.youdao.com/w/{$value}/#keyfrom=dict2.top";
            $html = \phpspider\core\requests::get($url);
            // 选择器规则
            $selector = "//div[contains(@id,'phrsListTab')]//div[contains(@class,'trans-container')]//ul//li";
            // 提取结果
            $result = \phpspider\core\selector::select($html, $selector);
            if(empty($result)){
                echo "有道异常\n";
                continue;
            }
            $descpstr = "";
            if(is_array($result)){
                foreach ($result as $desc_value){
                    $descpstr .= $desc_value."|";
                }
            }else{
                $descpstr = $result;
            }

            $insert_res = DB::insert('insert into word (id, word,description) values (?, ?,?)', [null, $value,$descpstr]);
            if($insert_res){
                echo $count++." ".$value."：单词入库成功\n";
            }else{
                echo $count++." ".$value."：单词入库失败\n";
            }
        }
    }
}
