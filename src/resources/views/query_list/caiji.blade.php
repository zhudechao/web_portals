<head>
    <link rel="stylesheet" href="/../../../layui-v2.5.6/layui/css/layui.css">
</head>
<div class="layui-container">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">抓取地址</label>
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="{{$info->aliexpress_id}}"/>
                    <input type="text" name="url" disabled="disabled" placeholder="地址" autocomplete="off" class="layui-input" value="{{$info->aliexpress_url}}">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="button" class="layui-btn" id="zhuaqu">抓取</button>
                </div>
            </div>
        </form>
    </div>

    <div class="layui-row">
        <table class="layui-table">
            <colgroup>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>订单量</th>
                <th>抓取时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list as $value)
            <tr>
                <td>{{$value->order_number}}</td>
                <td>{{$value->create_time}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="/../../../layui-v2.5.6/layui/layui.all.js"></script>
<script>
    layui.use(['jquery'],function () {
        var obj = {};
        obj['caiji'] = function(){
            var url = $("[name=url]").val();
            var id = $("[name=id]").val();
            if(url == ""){
                alert("采集地址不能为空")
                return;
            }
            $.ajax({
                url:"/querylist/caiji",
                type:"POST",
                data:{
                    url:url,
                    id:id
                },
                dataType:"json",
                success:function (res) {
                    console.log(res)
                    if(res.code == 0){
                        alert("抓取成功");
                        window.location.reload();
                    }else{
                        alert("数据异常重新抓取");
                    }
                }
            });
        };
        $ = layui.jquery;
        $(document).ready(function () {
            $("#zhuaqu").bind("click",function () {
                obj.caiji()
            });
        });
    });
</script>
