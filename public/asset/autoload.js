
    $("<link>").attr({href: "asset/waifu.css?v=1.4.2", rel: "stylesheet", type: "text/css"}).appendTo('head');
    $('body').append('<div class="waifu"><div class="waifu-tips"></div><canvas id="live2d" class="live2d"></canvas><div class="waifu-tool"><span class="fui-home"></span> <span class="fui-chat"></span> <span class="fui-eye"></span> <span class="fui-user"></span> <span class="fui-photo"></span> <span class="fui-info-circle"></span> <span class="fui-cross"></span></div></div>');
    $.ajax({url: 'asset/waifu-tips.js?v=1.4.2',dataType:"script", cache: true, async: false});
    $.ajax({url: 'asset/live2d.min.js?v=1.0.5',dataType:"script", cache: true, async: false});
    /* 可直接修改部分参数 */
    if(window.isMobile==false){
     live2d_settings['modelId'] = 1;                  // 默认模型 ID
        live2d_settings['modelTexturesId'] = 87;         // 默认材质 ID
        live2d_settings['modelStorage'] = false;         // 不储存模型 ID
        live2d_settings['canTurnToHomePage'] = false;    // 隐藏 返回首页 按钮
        live2d_settings['waifuSize'] = '600x535';        // 看板娘大小
        live2d_settings['waifuTipsSize'] = '570x150';    // 提示框大小
        live2d_settings['waifuFontSize'] = '30px';       // 提示框字体
        live2d_settings['waifuToolFont'] = '36px';       // 工具栏字体
        live2d_settings['waifuToolLine'] = '50px';       // 工具栏行高
        live2d_settings['waifuToolTop'] = '-60px';       // 工具栏顶部边距
        live2d_settings['waifuDraggable'] = 'axis-x';
        initModel('asset/waifu-tips.json');
        }// 拖拽样式     // 不储存模型 ID


