# fallsIM

* 基于 https://github.com/cuigeg/workman 项目的再次创作
* 增加 移动端支持，修复加入群组的bug，PC端添加Live2D效果
* 修复原代码的部分bug
* 新增**引用消息，撤回消息，艾特某人**的实现
* **电脑端右键消息唤出面板**，**手机端双击消息唤出菜单**
* 基于layim实现前端数据渲染
* 基于laravel的TLS版本5.5实现http请求
* 基于workerman的GatewayWorker框架开发的一款高性能支持分布式部署的聊天接口。
* 支持 **PC网页端** 和 **手机网页端** 演示地址: http://47.94.8.40  （自动识别移动端/PC端）
* PC端示例图
* <img src="https://yuhuison-1259460701.cos.ap-chengdu.myqcloud.com/exp/t1.png" style="zoom: 50%;" />
* 手机端示例图
* 

<img src="https://yuhuison-1259460701.cos.ap-chengdu.myqcloud.com/exp/t2.png" style="zoom: 67%;" />





## 环境需求

PHP >=7.1
GatewayWorker 3.0.12
layui 2.4.5
layim v2
layim-mobile v2

## 支持功能

| 功能           | 描述                                                         |
| -------------- | ------------------------------------------------------------ |
| 登录           | 用户登陆                                                     |
| 注册           | 注册过程中为用户分配了一个默认分组，并将用户添加到所有人都在的一个群（用于日后推送公告信息） |
| 查找-好友      | 可以根据用户名、昵称、id来查找，不输入内容不允许查找数据，点击发起好友申请，每页6人 |
| 查找-群聊      | 可根据群昵称、群id查找群聊，点击加入，每页6人                |
| 创建群聊       | 创建一个新群聊                                               |
| 修改群聊名称   | 修改指定群聊名称                                             |
| 解散群聊       | 解散指定群聊分组                                             |
| 面板内快速查找 | 查找已加的好友（好友列）、群聊（群聊列）                     |
| 消息盒子       | 用来接受好友请求和同意或拒绝好友请求的系统消息               |
| 个性签名       | 如果客服系统可显示客户访问商品ID和商品，如果聊天可以显示发表心情 |
| 一对一聊天     | 可发送文字、表情、图片、文件、音乐链接、视频链接、代码等     |
| 群聊           | 新成员加入群聊时，如果此刻你正开启着该群对话框，将收到新人入群通知 |
| 查看群成员     | 查看群聊中所有成员                                           |
| 临时会话       | 在群成员中，点击群成员头像即可发起临时会话                   |
| 历史记录       | 聊天面板只显示20条记录，更多记录点击聊天记录查看             |
| 离线消息       | 对方不在线的时候，向对方发起好友请求或者消息，将在对方上线后第一时间推送 |
| 换肤           | 这个是layim自带的东西                                        |
| 删除好友       | 好友列表右击删除好友                                         |
| 查看聊天记录   | 好友列表右击查看聊天记录或者打开聊天窗口点击聊天记录         |
| 删除好友分组   | 右击好友分组可将好友分组删除                                 |
| 新增好友分组   | 右击好友分组可新增好友分组                                   |
| 重命名好友分组 | 右击好友分组可重命名改分组                                   |

## 部署 (以阿里云新机为例)

### 1.环境配置

在阿里云轻量级应用服务器中选择 应用镜像为 **宝塔面板**，然后进入宝塔面板，软件商店，安装 **PHP7.2** , **nginx** , **Mysql**。

进入宝塔面板PHP配置，配置**PHP**禁用函数列表，将禁用函数列表全部清除。

进入宝塔面板MYSQL配置，在配置项中加入跳过检测。

```
在[mysqld]下添加 skip-grant-tables
```

配置PHP依赖环境为阿里云镜像

```
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

### 2.下载项目

进入SSH终端，输入进行将项目下载到本地

```
git clone https://github.com/yuhuison/fallsim.git
```

将下载的文件夹复制到 /var/www/laravel(没有则先创建)

```
cp fallsim /var/www/laravel
```

### 3.部署数据库

终端输入 

```
mysql
```

之后创建名称为im的数据库

```
create database im;
quit;
```

到复制到的代码目录

```
cd /var/www/laravel
```

输入mysql

```
mysql
```

使用刚才创建的数据库

```
use im;
```

运行初始化数据库脚本

```
source im.sql;
quit;
```

之后重置数据库的密码(自行在网上搜索)

### **4.配置数据库密码**

复制项目.env文件

```
cp .env.example .env
```

更改.env文件中的值为你的数据库密码

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=im
DB_USERNAME=root
DB_PASSWORD=你的密码
DB_PREFIX=im_
```

配置 /Applications/YourApp/Config/Db.php

```
<?php
namespace Config;
/**
 * mysql配置
 * @author walkor
 */
class Db
{
    /**
     * 数据库配置
     */
    public static $homestead = array(
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'im',
        'user' => 'root',
        'password' => '你的密码',
        'charset'  => 'utf8',
    );
}
```

### 5.配置服务器

配置 （/resources/views/index.blade.php）78行

改为

```
  socket = new WebSocket('ws://你的服务器IP地址:8282');
```

nginx配置 新增一个server

```
server {
    listen 80 default_server;
    listen [::]:80 default_server ipv6only=on;

    root /var/www/laravel/public;
    index index.php index.html index.htm;

    server_name server_domain_or_IP;

    location / {
            try_files $uri $uri/ /index.php?$query_string;
    }
        location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

修改PHP  fastcgi_pass 监听配置为 127.0.0.1:9000

宝塔面板文件搜索：php-fpm.conf: 
大概率在/www/server/php/72/etc/php-fpm.conf

将listen改为

```
listen = 127.0.0.1:9000
```

安装依赖

```
composer insatll
```

生成独立的key

```
php artisan key:generate
```


### 6.测试项目运行

启动项目 开启进程守护模式

```
php start.php start -d
```

浏览器访问 http://你的服务器IP地址

## 声明

1. 前端部分是采用layui,在此郑重说明，layui中的im部分layim并不开源，仅供交流学习，请勿将此项目中的layim用作商业用途
2. 此项目持续开发中，欢迎有兴趣的朋友共同维护
3. 此项目属于个人项目，如果有兴趣的小伙伴可以一起贡献代码 感觉还不错的小伙伴，请留下您的star

## 进阶

- 为你的服务器绑定域名并配置SSL证书以加密聊天消息
- 尝试workman 分布式部署以提高性能
- (TO-DO ) 将移动端和PC端路由至不同的页面以降低代码耦合性

## To-Do

- 优化撤回消息的算法
- 增加修改个人资料界面
- 增加自定义背景图
- 增加移动端的群消息，好友处理
- 将移动端和PC端路由至不同的页面以降低代码耦合性
