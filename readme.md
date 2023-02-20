# 跑鸭

这是我的毕业设计：“跑鸭”微信小程序-一款基于校园跑步的社交小程序
技术栈：Laravel+MySQL

- 前端项目：https://github.com/Chef5/PopRun

## 一、功能设计

“跑鸭”微信小程序的核心功能就是：跑步+社交+活动，详细划分如下：

（1）跑步（首屏）：当前位置地图、排行榜（周榜、月榜）、运动路径、实时数据（里程、配速）、随机一言。

（2）动态圈子：打卡分享、发布分享、热门推荐、点赞评论、消息通知。

（3）活动广场：线上活动（报名、完赛条件、奖励）、跑步教程。

（4）个人中心：运动管理、动态管理、设置（通用设置、隐私设置）、勋章墙、等级称号、个人主页、资料编辑。

**E-R图：**

根据功能分析，一共规划出11个实体，形成E-R图：
![20200618185938.jpeg](http://img.cdn.1zdz.cn/github/readme/poprun/20200618185938.jpeg)

**数据模型图：**

由E-R图，共转换成16张表，数据模型图由Navicat导出
![20200618191037.jpeg](http://img.cdn.1zdz.cn/github/readme/poprun/20200618191037.jpeg)

- 接口文档：待整理。

**目录结构：**

``` shell
├─.vscode               #VS Code配置
├─app                   #app目录
│  ├─Console                #【核心】控制台：定时任务
│  │  └─Commands                #命令：定时任务要执行的操作
│  ├─Exceptions         #异常抛出类
│  ├─Http               #Http控制
│  │  ├─Controllers         #【核心】控制器
│  │  │  └─Auth                 #控制器里进行分类：认证
│  │  └─Middleware      #中间件：过滤请求和响应
│  ├─Lib                #公共方法：主要写了一个返回参数格式化
│  └─Providers			
├─config                #配置文件：主要改了数据库编码配置，支持emoji
├─database              #数据库
│  ├─factories
│  ├─migrations             #【核心】数据库迭代生成
│  └─seeds
├─public                #公共资源
│  ├─css
│  ├─js
│  ├─layui                  #layui引入
│  └─resources              #资源
│      ├─images                 #图片
│      │  ├─2020-01-31              #图片按上传日期分目录管理
│      ├─medals                 #勋章图片
│      └─userImgs               #用户头像
├─resources
├─routes                #【核心】路由
├─storage
├─tests
└─vendor                #Laravel依赖
```

## 二、如何使用

要求：
PHP: 7.2+

**以下步骤一步一步弄，顺序不能乱，通常是能一次性运行起来的！**

### 2.1 克隆代码到本地

``` shell
git clone https://github.com/Chef5/PopRun-b.git
```

### 2.2 创建数据库

在本地MySQL创建一个数据库，假定数据库名称设置为：`poprun`

字符集：utf8mb4
排序规则：utf8mb4_unicode_ci

### 2.3 配置.env

- 将.env.example更名改为.env
- 设置必要的参数（以下中文部分）

``` shell
WX_APPID=微信AppID(小程序ID)
WX_SECRET=微信小程序AppSecret(小程序密钥)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=数据库名称（前面步骤创建的）
DB_USERNAME=数据库用户
DB_PASSWORD=数据库密码
```

### 2.4 安装依赖、生成key和数据库

在根目录执行：

``` shell
composer install
php artisan key:generate
php artisan migrate
```

Q: composer install 如果遇到类似这样的报错：

``` sh
In PackageManifest.php line 131:
  Undefined index: name  
                         
Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
``` 

A: 可尝试以下命令：

``` sh
composer clear
composer update --ignore-platform-req=ext-sockets
```

### 2.5 启动运行

``` shell
php artisan serve
```

浏览器能正常打开：http://127.0.0.1:8000/ ，说明你已经成功运行起来了，接下来还要初始一点数据，继续！

### 2.6 初始化数据

完成以上步骤，在本地浏览器中打开以下链接进行数据初始化

初始化数据位置，可以自己修改：`app/Http/Controllers/AdminController.php`

- 初始化用户等级数据：http://127.0.0.1:8000/api/admin/initData?key=123123&data=honors
- 初始化勋章数据：http://127.0.0.1:8000/api/admin/initData?key=123123&data=medals


### 2.7 创建活动和教程

管理密码：123123

- 创建活动：http://127.0.0.1:8000/addActivity （勋章图片和检索串、勋章名称一一对应，且检索串、勋章名称不能和之前已有值的重复）

- 创建课程：http://127.0.0.1:8000/addCourse

Q: 上传出错
A: 请检查是否启动了本地项目 `php artisan serve`

### 2.8 其他注意事项

注意，本项目是20年我那会大四做的，当时技术有限，或多或少都留下了不少的坑，如果需要自用，请不要喷我哈。

本人于2023年2月，根据本文档重新跑了一遍，纠正了一些坑，项目能顺利跑起来，并备注了一些注意事项，可以全局搜索：`// TODO:` 查看。

## LICENSE

[MIT](LICENSE)
