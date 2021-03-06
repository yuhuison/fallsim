<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes">
    <meta charset="utf-8">
    <title>im-chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .layui-atCode{
           color:#000;
            padding:2px;
            background-color:#F08080;
            font-family:"微软雅黑";
        }
        .layui-bottom-status-quote{
           color:#F08080;
           width:200px;
           height:25px;
           margin:12px auto;
            border-radius: 25px;
            border: 2px solid #F08080;
            display:none;
            font-size:13px;
        }
         .layui-bottom-status-at{
           color:#008080;
            width:200px;
            height:25px;
            margin:12px auto;
            border-radius: 25px;
            border: 2px solid #8AC007;
            display:none;
            font-size:13px;
        }
        #quote_content{
           position:relative;
           left:5px;
           top:2px;
        }
        .layui-code {
 position:relative;
 margin:10px 0;
 padding:15px;
 line-height:20px;
 border:1px solid #ddd;
 border-left-width:6px;
 background-color:#F2F2F2;
 color:#333;
 font-family:Courier New;
 font-size:12px
}
        .layui-edge{
            display: block;
        }
        .waifu-tool{
        color:#FFFFFF;
        }
    </style>
    <script>
             if(window.screen.height>window.screen.width){
         window.isMobile=true;
         }else{
         window.isMobile=false;
         }
       function LoadCss(path){
  if(!path || path.length === 0){
  throw new Error('argument "path" is required !');
  }
  var head = document.getElementsByTagName('head')[0];
  var link = document.createElement('link');
  link.href = path;
  link.rel = 'stylesheet';
  link.type = 'text/css';
  head.appendChild(link);
  }
       function setBG(){
         if(window.isMobile){
         document.body.style.background = "url('https://yuhuison-1259460701.cos.ap-chengdu.myqcloud.com/06x.jpg')";
         }else{
         document.body.style.background = "url('https://yuhuison-1259460701.cos.ap-chengdu.myqcloud.com/06.jpg')";
         }
       }
       if(window.isMobile==false){
       LoadCss('{{asset('/asset/layui/css/layuiv2.css')}}');
       }else{
       LoadCss('{{asset('/asset/layui/css/layui.mobile.css')}}');
       }
    </script>
    
    <script type="text/javascript" src="{{asset('/asset/layui/jquery.js')}}"></script>

</head>
<body onload="setBG()">
<ul class="layui-nav" id="nav" >
    <li class="layui-nav-item" style="float: right;">
        <a href="javascript:;"><img src="{{ session('user')->avatar }}" class="layui-nav-img">{{ session('user')->username }}</a>
        <dl class="layui-nav-child">
            <dd><a href="/loginout">退出登录</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-this"><a href="/">首页</a></li>
     <li class="layui-nav-item"><a href="https://github.com/cuigeg/workman">原作者GitHub</a></li>
      <li class="layui-nav-item"><a href="https://github.com/yuhuison/fallsim">秋小十魔改版GitHub</a></li>
</ul>

<script src="{{asset('/asset/layui/layui.js')}}"></script>
<script>
        if(window.isMobile){
          $('#nav').css("display","none");
        }
        window.jqs=$;
        window.qxs_msgs_mine= new Array();
        var socket;
        var ping;
        function sendMessage(socket, data){
            socket.send(data)
        }
        if(window.isMobile==false){
        $.ajax({url:"/userinfo",async:false,success:function(result){
                 window.userinfo=JSON.parse(result).data;
             }});
        layui.use(['layim','element','upload'], function(layim){
            var element = layui.element;
            //基础配置
            layim.config({
                init: {
                    url: '/userinfo' //接口地址（返回的数据格式见下文）
                    ,type: 'get' //默认get，一般可不填
                    ,data: {} //额外参数
                }
                //获取群员接口（返回的数据格式见下文）
                ,members: {
                    url: '/group_members' //接口地址（返回的数据格式见下文）
                    ,type: 'get' //默认get，一般可不填
                    ,data: {} //额外参数
                }
                //上传图片接口（返回的数据格式见下文），若不开启图片上传，剔除该项即可
                ,uploadImage: {
                    url: '/upload?type=im_image&path=im'//接口地址
                    ,token: "{{ csrf_token() }}"
                    ,type: 'post' //默认post
                }
                //上传文件接口（返回的数据格式见下文），若不开启文件上传，剔除该项即可
                ,uploadFile: {
                    url: '/upload?type=im_file&path=file'//接口地址
                    ,token: "{{ csrf_token() }}"
                    ,type: 'post' //默认post
                }
                //扩展工具栏，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                ,tool: [{
                    alias: 'code' //工具别名
                    ,title: '代码' //工具名称
                    ,icon: '&#xe64e;' //工具图标，参考图标文档
                }]

                , isAudio: true //开启聊天工具栏音频
                , isVideo: true //开启聊天工具栏视频
                , notice: true //是否开启桌面消息提醒，默认false
                ,msgbox: '/message_box' //消息盒子页面地址，若不开启，剔除该项即可
                ,find: '/find'//发现页面地址，若不开启，剔除该项即可
                ,chatLog: '/chat_log' //聊天记录页面地址，若不开启，剔除该项即可
            });
            setTimeout(function () {
                //监听自定义工具栏点击，以添加代码为例
                //建立websocket连接
                socket = new WebSocket('ws://47.94.8.40:8282');
                socket.onopen = function(){
                    console.log("websocket is connected");
                    ping = setInterval(function () {
                        sendMessage(socket,'{"type":"ping"}');
                    },1000 * 20);
                    sendMessage(socket,JSON.stringify({
                        type: 'login' //随便定义，用于在服务端区分消息类型
                        ,sessionid: "{{ $sessionid }}"
                        ,sessionname : "{{$sessionname}}"
                    }));
                };
                socket.onmessage = function(res){
                    data = JSON.parse(res.data);
                    
                    switch (data.type) {
                        case "friend":
                        case "group":
                            var msgid = data['cid'];
                            if(data.content=="%delMsg%"){
                               	var local = layui.data('layim')[window.userinfo.mine.id];
                               	if(local.chatlog!=undefined){
                               	   for(var key in local.chatlog){
                               	      if(key==(data['type']+data['id'])){
                               	          var smsg = local.chatlog[key];
                               	          for(i_msg=0;i_msg<smsg.length;i_msg++){
                               	             var msg_=smsg[smsg.length-i_msg-1];
                               	             if(msg_.cid==msgid){
                               	                 smsg.splice(smsg.length-i_msg-1,1);
                               	                 local.chatlog[key]=smsg;
                               	                 layui.data('layim', {
					                             key: window.userinfo.mine.id
					                              ,value: local
					                             });
					                             window.jqs("[data-cid='"+msgid+"'").hide();
					                             layim.getMessage({
                                                  system: true //系统消息
                                                  ,id: data['id'] //聊天窗口ID
                                                  ,type: data['type'] //聊天窗口类型
                                                  ,content: data['username']+'撤回了一条消息'
                                                 });
                               	                 break;
                               	             }
                               	          }
                               	      }
                               	   }
                               	}
                            }
                            if(data.content!="%delMsg%"){
                            layim.getMessage(data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                            var msge=$("[data-cid='"+msgid+"']");
                            msge.attr("id",msgid);
                            window.SetMsgTextMenu(msge);
                            break;
                            }
                        //单纯的弹出
                        case "layer":
                            if (data.code === 200) {
                                //layer.msg(data.msg)
                            } else if(data.code === 403){
                                layer.msg(data.msg,{time:2*1000},function() {
                                    window.location.href = '/loginout';
                                });
                            }else {
                                layer.msg(data.msg,function(){})
                            }
                            break;
                        //将新好友添加到列表
                        case "addList":
                            layim.setChatStatus('<span style="color:#FF5722;">在线</span>'); //模拟标注好友在线状态
                            layim.addList(data.data);
                            break;
                        //好友上下线变更
                        case "friendStatus" :
                            if(data.status === 'online'){
                                layim.setChatStatus('<span style="color:#FF5722;">在线</span>'); //模拟标注好友在线状态
                            }else{
                                layim.setChatStatus('<span style="color:#666;">离线</span>'); //模拟标注好友在线状态
                            }
                            layim.setFriendStatus(data.uid, data.status);
                            break;
                        //消息盒子
                        case "msgBox" :
                            //为了等待页面加载，不然找不到消息盒子图标节点
                            setTimeout(function(){
                                if(data.count > 0){
                                    layim.msgbox(data.count);
                                }
                            },1000);
                            break;
                        //token过期
                        case "token_expire":
                            window.location.reload();
                            break;
                        //加群提醒
                        case "joinNotify":
                            layim.getMessage(data.data);
                            break;

                    }
                };
                socket.onclose = function(){
                    console.log("websocket is closed");
                    clearInterval(ping);
                }
            },150);
            
            window.delMsgById=function(msgid,resdata){
              for(var msg_ of window.qxs_msgs_mine){
                  if(msg_.msgid == msgid){
                     var smsg=msg_;
                     smsg.mine.content="%delMsg%";
                     sendMessage(socket,JSON.stringify({
                          type: 'chatMessage' //随便定义，用于在服务端区分消息类型
                          ,data: smsg
                      }));
                      layim.getMessage({
                        system: true //系统消息
                        ,id: smsg.to.id //聊天窗口ID
                        ,type: smsg.to.type //聊天窗口类型
                        ,content: window.userinfo.mine.username+'撤回了一条消息'
                        });
                  }
              }
            }
            layim.on('sendMessage', function(res){
                console.log(res);
                window.qxs_msgs_mine.push(res);
                res['msgid']=res['cid'];
                window.jqs(window.qxs_chatObj['elem']).find(".layim-chat-mine:last").attr("data-cid",res['msgid']);
                window.SetMsgTextMenu(window.jqs(window.qxs_chatObj['elem']).find(".layim-chat-mine:last"));
                sendMessage(socket,JSON.stringify({
                    type: 'chatMessage' //随便定义，用于在服务端区分消息类型
                    ,data: res
                }));
            });
            layim.on('chatChange', function(obj){
               var msgs=window.jqs(".layim-chat-main").find('ul').find('li');
               for(var i = 0; i < msgs.length; i++) {
                  var msg=msgs.eq(i);
	              if(msg.attr("data-cid")!=undefined && msg.attr("data-bind")==undefined){
	                 msg.attr("data-bind","1");
	                 window.SetMsgTextMenu(msgs.eq(i));
	              }
                }
                window.qxs_chatObj=obj;
            });
            layim.on('sign', function(value){
                $.ajax({
                    url:"/update_sign"
                    ,type:"post"
                    ,data:{sign:value}
                    ,dataType:"json"
                    ,headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                    ,success:function (res) {
                        if(res.code === 200){
                            //layer.msg(res.msg)
                        }else{
                            //layer.msg(res.msg,function () {})
                        }
                    },
                    error:function () {
                        layer.msg("网络繁忙",function(){});
                    }
                })
            });
            layim.on('tool(code)', function(insert, send, obj){ //事件中的tool为固定字符，而code则为过滤器，对应的是工具别名（alias）
                layer.prompt({
                    title: '插入代码'
                    ,formType: 2
                    ,shade: 0
                }, function(text, index){
                    layer.close(index);
                    insert('[pre class=layui-code]' + text + '[/pre]'); //将内容插入到编辑器，主要由insert完成
                    //send(); //自动发送
                });
            });
            layim.on('chatChange', function(obj){
                var type = obj.data.type;
                if(type === 'friend'){
                    if(obj.data.status == 'online'){
                        layim.setChatStatus('<span style="color:#FF5722;">在线</span>'); //模拟标注好友在线状态
                    }else{
                        layim.setChatStatus('<span style="color:#666;">离线</span>'); //模拟标注好友在线状态
                    }
                }
            });
            
            
        });
     }else{
            window.delMsgByIdM=function(msgid,resdata){
              for(var msg_ of window.qxs_msgs_mine){
                  if(msg_.msgid == msgid){
                     var smsg=msg_;
                     smsg.mine.content="%delMsg%";
                     sendMessage(socket,JSON.stringify({
                          type: 'chatMessage' //随便定义，用于在服务端区分消息类型
                          ,data: smsg
                      }));
                      layui.layim.getMessage({
                        system: true //系统消息
                        ,id: smsg.to.id //聊天窗口ID
                        ,type: smsg.to.type //聊天窗口类型
                        ,content: window.userinfo.mine.username+'撤回了一条消息'
                        });
                  }
              }
            }     
     
                window.qxs_m_msg_click=function(msg){
                   var lasttime = window.jqs(msg).attr("clicktime");
                   var msgjq=window.jqs(msg);
                   var msgid=msgjq.attr("data-cid");
                   if(msgid!=undefined){
                      if(lasttime==undefined || lasttime==""){
                         msgjq.attr("clicktime",(new Date()).valueOf().toString());
                      }else{
                         var nowtime=(new Date()).valueOf();
                         if((nowtime-parseInt(lasttime))<1000){
                          msgjq.attr("clicktime","");
                          window.SetMsgTextMenuM(msgjq,msgid);
                         }else{
                         msgjq.attr("clicktime",nowtime.toString());
                         }
                         
                      }
                   }
                };
                layui.use('layer', function(){
                window.layer= layui.layer;
                });  
     
     
     
             $.ajax({url:"/userinfo",async:false,success:function(result){
                 window.userinfo=JSON.parse(result).data;
             }});
             layui.use('mobile', function(){
               var mobile = layui.mobile,layim = mobile.layim;
               layui.layim=layim;
                 layim.config({ 
                 
                  isgroup : true,
                  init: window.userinfo
                  ,uploadImage: {
                    url: '/upload?type=im_image&path=im'//接口地址
                    ,token: "{{ csrf_token() }}"
                    ,type: 'post' //默认post
                   }    
                  //扩展“更多”的自定义列表，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                   ,moreList: [{
                    alias: 'find'
                    ,title: '添加新的好友/群'
                    ,iconUnicode: '&#xe628;' //图标字体的unicode，可不填
                      ,iconClass: '' //图标字体的class类名
                    }]
                  });
                              setTimeout(function () {
                //监听自定义工具栏点击，以添加代码为例
                //建立websocket连接
                socket = new WebSocket('ws://47.94.8.40:8282');
                socket.onopen = function(){
                    console.log("websocket is connected");
                    ping = setInterval(function () {
                        sendMessage(socket,'{"type":"ping"}');
                    },1000 * 20);
                    sendMessage(socket,JSON.stringify({
                        type: 'login' //随便定义，用于在服务端区分消息类型
                        ,sessionid: "{{ $sessionid }}"
                        ,sessionname : "{{$sessionname}}"
                    }));
                };
                socket.onmessage = function(res){
                    data = JSON.parse(res.data);
                    switch (data.type) {
                        case "friend":
                        case "group":
                            if(data.content!="%delMsg%"){
                            console.log(data);
                            layim.getMessage(data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                            break;
                            }else{
                                var msgid=data.cid;
                               	var local = layui.data('layim-mobile')[window.userinfo.mine.id];
                               	if(local.chatlog!=undefined){
                               	   for(var key in local.chatlog){
                               	      if(key==(data['type']+data['id'])){
                               	          var smsg = local.chatlog[key];
                               	          for(i_msg=0;i_msg<smsg.length;i_msg++){
                               	             var msg_=smsg[smsg.length-i_msg-1];
                               	             if(msg_.cid==msgid){
                               	                 smsg.splice(smsg.length-i_msg-1,1);
                               	                 local.chatlog[key]=smsg;
                               	                 layui.data('layim-mobile', {
					                             key: window.userinfo.mine.id
					                              ,value: local
					                             });
					                             window.jqs("[data-cid='"+msgid+"'").hide();
					                             layim.getMessage({
                                                  system: true //系统消息
                                                  ,id: data['id'] //聊天窗口ID
                                                  ,type: data['type'] //聊天窗口类型
                                                  ,content: data['username']+'撤回了一条消息'
                                                 });
                               	                 break;
                               	             }
                               	          }
                               	      }
                               	   }
                               	}                            
                            
                            }
                        //单纯的弹出
                        case "layer":
                            if (data.code === 200) {
                               // layer.msg(data.msg)
                            } else if(data.code === 403){
                                layer.msg(data.msg,{time:2*1000},function() {
                                    window.location.href = '/loginout';
                                });
                            }else {
                                layer.msg(data.msg,function(){})
                            }
                            break;
                        //将新好友添加到列表
                        case "addList":
                            layim.setChatStatus('<span style="color:#FF5722;">在线</span>'); //模拟标注好友在线状态
                            layim.addList(data.data);
                            break;
                        //好友上下线变更
                        case "msgBox" :
                            //为了等待页面加载，不然找不到消息盒子图标节点
                            setTimeout(function(){
                                if(data.count > 0){
                                    layim.msgbox(data.count);
                                }
                            },1000);
                            break;
                        //token过期
                        case "token_expire":
                            window.location.reload();
                            break;
                        //加群提醒
                        case "joinNotify":
                            layim.getMessage(data.data);
                            break;

                    }
                };
                socket.onclose = function(){
                    console.log("websocket is closed");
                    clearInterval(ping);
                }
            },150);
            layim.on('chatlog',function(res){
                  console.log(res);
                  layer.open({
                  type: 2, 
                  content: '/chat_log?id='+res.id.toString()+"&type="+res.type //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
                  ,area: ['100%', '100%']
                }); 
            
            });
            layim.on('sendMessage', function(res){
                window.qxs_msgs_mine.push(res);
                res['msgid']=res['cid'];
                sendMessage(socket,JSON.stringify({
                    type: 'chatMessage' //随便定义，用于在服务端区分消息类型
                    ,data: res
                }));
            });
            layim.on('moreList', function(obj){
              switch(obj.alias){ //alias即为上述配置对应的alias
               case 'find': //发现
                  layer.open({
                  type: 2, 
                  content: '/find' //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
                  ,area: ['100%', '100%']
                }); 
               break;
                   }
              });        
               layim.on('newFriend', function(){
                  layer.open({
                  type: 2, 
                  content: '/message_box' //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
                  ,area: ['100%', '100%']
                }); 
  

               });   
                  
                  
                  
                  
                  
                  
                });
                
     }

</script>
</body>
        <!-- 实现拖动效果，需引入 JQuery UI -->
    <script src="{{asset('/asset/jquery-ui.min.js?v=1.12.1')}}"></script>
    
    <!-- 使用 aotuload.js 引入看板娘 -->
    <script src="{{asset('/asset/autoload.js?v=1.4.2')}}"></script>
</html>
