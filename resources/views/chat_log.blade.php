<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>聊天记录</title>
    <link rel="stylesheet" href="{{asset('/asset/layui/css/layui.css')}}" media="all">
    <style>
        body .layim-chat-main{height: auto;}
    </style>
</head>
<body>
<div class="layim-chat-main">
    <ul id="LAY_view">
    </ul>
</div>
<div id="LAY_page" style="margin: 0 10px;"></div>
<textarea title="消息模版" id="LAY_tpl" style="display:none;"><%# layui.each(d.data, function(index, item){
  if(item.id == parent.layui.layim.cache().mine.id){ %>
    &lt;li class="layim-chat-mine"&gt;&lt;div class="layim-chat-user"&gt;&lt;img src="<% item.avatar %>"&gt;&lt;cite&gt;&lt;i&gt;<% layui.data.date(item.timestamp) %>&lt;/i&gt;<% item.username %>&lt;/cite&gt;&lt;/div&gt;&lt;div class="layim-chat-text"&gt;<% layui.layim.content(item.content) %>&lt;/div&gt;&lt;/li&gt;
    <%# } else { %>
    &lt;li&gt;&lt;div class="layim-chat-user"&gt;&lt;img src="<% item.avatar %>"&gt;&lt;cite&gt;<% item.username %>&lt;i&gt;<% layui.data.date(item.timestamp) %>&lt;/i&gt;&lt;/cite&gt;&lt;/div&gt;&lt;div class="layim-chat-text"&gt;<% layui.layim.content(item.content) %>&lt;/div&gt;&lt;/li&gt;
    <%# }
  }); %>
</textarea>
<script type="text/javascript" src="{{asset('/asset/layui/jquery.js')}}"></script>
<script src="{{asset('/asset/layui/layui.js')}}"></script>
<script>
    var msgs_has_del = new Array();
    layui.use(['layim', 'laytpl','laypage'], function(){
        var layim = layui.layim
            ,layer = layui.layer
            ,laytpl = layui.laytpl
            ,$ = layui.jquery
            ,laypage = layui.laypage
            ,mark = 0;

        function getData(page = 1){
            //实际使用时，下述的res一般是通过Ajax获得，而此处仅仅只是演示数据格式
            $.ajax({
                url:"/chat_record_data",
                type:"get",
                data:{id:"{{ $id }}",type:"{{ $type }}",page:page},
                dataType:"json",
                success:function(res){
                   
                    var msgsdata=res.data.data;
                    console.log(msgsdata);
                    var index = 0;
                    while(index!=-1){
                        index=-1;
                    	for (var i = 0; i < msgsdata.length; i++) {
		                  	if(msgsdata[i].content=="%delMsg%"){
		                  	   index=i;
		                  	   msgs_has_del.push(msgsdata[i]);
		                  	   msgsdata.splice(index,1);
		                  	   break;
		                  	}
	                  	}
                      
                    }
                    for (var i = 0; i < msgsdata.length; i++) {
                        for(var delmsg of msgs_has_del){
                            
                        }
	                 }
                    
                    res.data.data=msgsdata;
                    if (mark === 0){
                        //执行一个laypage实例
                        laypage.render({
                            elem: 'LAY_page'
                            ,count: res.data.total //数据总数，从服务端得到
                            ,curr:1//当前页
                            ,groups:5//连续分页数
                            ,jump: function(obj, first){
                                //跳转到当前页
                                if(!first){
                                    getData(obj.curr);
                                }
                            }
                        });
                    }
                    mark = 1;
                    laytpl.config({
                        open: '<%',
                        close: '%>'
                    });
                    var html = laytpl(LAY_tpl.value).render({
                        data: res.data.data
                    });
                    $('#LAY_view').html(html);

                },
                error:function(){

                }
            })
        }
        getData(1);


        //开始请求聊天记录
        var param =  location.search; //获得URL参数。该窗口url会携带会话id和type，他们是你请求聊天记录的重要凭据



    });
</script>
</body>
</html>