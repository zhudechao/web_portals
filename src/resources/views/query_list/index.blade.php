<html>
<head>
    <link rel="stylesheet" href="/../../../layui-v2.5.6/layui/css/layui.css">
</head>
<body>
<div class="layui-container">
    <div class="layui-row">
        <form class="layui-form" action="">
            <div class="layui-form-item"></div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品名:</label>
                <div class="layui-input-block">
                    <input type="text" name="product_name" placeholder="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">爬取地址:</label>
                <div class="layui-input-block">
                    <input type="text" name="product_url" placeholder="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="button" id="save_caiji">保存</button>
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
                <th>商品名</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="list">
            </tbody>
        </table>
    </div>

</div>
</body>
<script src="/../../../layui-v2.5.6/layui/layui.all.js"></script>
<script>
    layui.use(['jquery'],function () {
        var obj = {};
        obj['caiji'] = function(){
            var url = $("[name=url]").val();
            if(url == ""){
                alert("采集地址不能为空")
                return;
            }
            $.ajax({
                url:"/querylist/caiji",
                type:"POST",
                data:{
                    url:url
                },
                dataType:"json",
                success:function (res) {
                    console.log(res)
                    if(res.code == 0){
                        $("#order_num").html(res.data.order_num);
                    }else{
                        alert("数据异常重新抓取");
                    }
                }
            });
        };
        obj['save_caiji'] = function () {
            var name = $("[name=product_name]").val();
            var url = $("[name=product_url]").val();
            if(name == ""){
                alert("采集商品名不能为空")
                return;
            }
            if(url == ""){
                alert("采集商品地址不能为空")
                return;
            }
            $.ajax({
                url:"/querylist/saveCaiji",
                type:"POST",
                data:{
                    url:url,
                    name:name
                },
                dataType:"json",
                success:function (res) {
                    console.log(res)
                    if(res.code == 0){
                        window.location.reload();
                    }else{
                        alert("数据异常重新抓取");
                    }
                }
            });
        };
        obj['getCaijiList'] = function () {
            $.ajax({
                url:"/querylist/getCaijiUrlList",
                type:"GET",
                data:{
                },
                dataType:"json",
                success:function (res) {
                    if(res.code == 0){
                        var html = '';
                        for (var i in res.data){
                            html += '<tr>';
                            html += '<td><a href="/querylist/caijiView?id='+res.data[i]['aliexpress_id']+'">'+res.data[i]['aliexpress_name']+'</a></td>';
                            html += '<td>'+res.data[i]['aliexpress_create_time']+'</td>';
                            html += '<td></td>';
                            html += '</tr>';
                        }
                        $("#list").append(html);
                    }
                }
            });
        };
        $ = layui.jquery;
        $(document).ready(function () {
            obj.getCaijiList();
            $("#zhuaqu").bind("click",function () {
                obj.caiji()
            });
            $("#save_caiji").bind('click',function () {
                obj.save_caiji()
            });
        });
    });
</script>
</html>
