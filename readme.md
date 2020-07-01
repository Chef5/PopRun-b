# 跑鸭

这是我的毕业设计：“跑鸭”微信小程序-一款基于校园跑步的社交小程序
技术栈：Laravel+MySQL

- 前端项目：https://github.com/Patrick-Jun/PopRun

## 一、功能设计

“跑鸭”微信小程序的核心功能就是：跑步+社交+活动，详细划分如下：

（1）跑步（首屏）：当前位置地图、排行榜（周榜、月榜）、运动路径、实时数据（里程、配速）、随机一言。

（2）动态圈子：打卡分享、发布分享、热门推荐、点赞评论、消息通知。

（3）活动广场：线上活动（报名、完赛条件、奖励）、跑步教程。

（4）个人中心：运动管理、动态管理、设置（通用设置、隐私设置）、勋章墙、等级称号、个人主页、资料编辑。

- 接口管理：http://api.dlpu.net

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

### 2.1 克隆代码到本地

``` shell
git clone https://github.com/Patrick-Jun/PopRun-b.git
```

### 2.2 配置.env

- 将.env.example更名改为.env
- 设置必要的参数（以下中文部分）

``` shell
WX_APPID=微信小程序id
WX_SECRET=微信小程序密钥

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=数据库名称
DB_USERNAME=数据库用户
DB_PASSWORD=数据库密码
```

### 2.3 安装依赖、生成key和数据库

在根目录执行：

``` shell
composer install
php artisan key:generate
php artisan migrate
```

### 2.4 启动运行

``` shell
php artisan serve
```

### 2.5 上线部署

完成以上步骤就可以在本地运行了，下面这是上线使用的

[config&deploy.md](./config&deploy.md)

## LICENSE

[MIT](LICENSE)