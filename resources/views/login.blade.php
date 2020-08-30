<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
    <link rel="stylesheet" type="text/css" href="{{asset('/asset/login/css/normalize.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('/asset/login/css/demo.css')}}" />
    <!--必要样式-->
    <link rel="stylesheet" type="text/css" href="{{asset('/asset/login/css/component.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('/asset/layui/css/layui.css')}}" />
    <script type="text/javascript" src="{{asset('/asset/layui/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('/asset/layui/layui.js')}}"></script>
</head>
<body>
<div class="container demo-1">
    <div class="content">
        <div id="large-header" class="large-header">
            <div class="logo_box">
                <form action="#" name="f" method="post">
                    <input type="password" style="position:absolute;top:-999px"/>
                    <div class="input_outer">
                        <span class="u_user"></span>
                        <input id="username" name="username" class="text" style="color: #FFFFFF !important" type="text" placeholder="请输入账户" value="">
                    </div>
                    <div class="input_outer">
                        <span class="us_uer"></span>
                        <input id="password" name="password" class="text" style="color: #FFFFFF !important; position:absolute; z-index:100;"value="" type="password" placeholder="请输入密码">
                    </div>
                    <div class="mb2"><a id = "sub" lay-filter="sub" class="act-but submit" href="javascript:;" style="color: #FFFFFF">登录</a></div>
                </form>
                <p style="text-align: center;">还没有账号？立即<a style="color: #cccccc;" href="javascript:;" onclick="register()"> 注册 </a></p>
            </div>
        </div>
    </div>
</div><!-- /container -->
<script src="{{asset('/asset/login/js/TweenLite.min.js')}}"></script>
<script src="{{asset('/asset/login/js/EasePack.min.js')}}"></script>
<script src="{{asset('/asset/login/js/rAF.js')}}"></script>
<script src="{{asset('/asset/login/js/demo-1.js')}}"></script>
</body>
<script>
  //设置cookie
  function setCookie(name,value,day){
    var date = new Date();
    date.setDate(date.getDate() + day);
    document.cookie = name + '=' + value + ';expires='+ date;
  };
  //获取cookie
  function getCookie(name){
    var reg = RegExp(name+'=([^;]+)');
    var arr = document.cookie.match(reg);
    if(arr){
      return arr[1];
    }else{
      return '';
    }
  };
    var oUser = document.getElementById('username');
    var oPswd = document.getElementById('password');
    if(getCookie('user') && getCookie('pswd')){
      oUser.value = getCookie('user');
      oPswd.value = getCookie('pswd');
    }
</script>
<script>
    function register() {
    if(window.screen.width>=window.screen.height){
            layer.open({
            type: 2,
            title: '注册',
            shadeClose: true,
            shade: 0.8,
            area: ['40%', '70%'],
            content: '/register' //iframe的url
        });
    }else{
            layer.open({
            type: 2,
            title: '注册',
            shadeClose: true,
            shade: 0.8,
            area: ['100%', '90%'],
            content: '/register' //iframe的url
        });
    }

    }
    //加载弹出层组件
    layui.use('layer',function(){

        var layer = layui.layer;

        //登录的点击事件
        $("#sub").on("click",function(){
            login();
        })

        $("body").keydown(function(){
            if(event.keyCode == "13"){
                login();
            }
        })

        //登录函数
        function login(){
            var username = $(" input[ name='username' ] ").val();
            var password = $(" input[ name='password' ] ").val();
            setCookie('user',username,7); //保存帐号到cookie，有效期7天
            setCookie('pswd',password,7); //保存密码到cookie，有效期7天
            $.ajax({
                url:"login",
                data:{"username":username,"password":password},
                type:"post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType:"json",
                success:function(res){
                    if(res.code === 200){
                        layer.msg(res.msg);
                        setTimeout(function(){
                            window.location = "/";
                        },1500);
                    }else{
                        layer.msg(res.msg,function(){});
                    }
                }
            })
        }
    })
</script>
</html>