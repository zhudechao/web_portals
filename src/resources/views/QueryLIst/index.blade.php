<html>
<head>
    <link rel="stylesheet" href="/../../../layui-v2.5.6/layui/css/layui.css">
</head>
<body>
<div class="layui-container">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">抓取地址</label>
                <div class="layui-input-block">
                    <input type="text" name="url" placeholder="地址" autocomplete="off" class="layui-input">
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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="order_num">0</td>
                </tr>
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
        }
        $ = layui.jquery;
        $(document).ready(function () {
            $("#zhuaqu").bind("click",function () {
                obj.caiji()
            })
        });
    });
</script>
</html>
