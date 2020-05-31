<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>
    Pop Run！ 跑鸭！创建课程活动
  </title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="../../layui/css/layui.css" media="all">
  <style>
    .layui-input input{
      width: 100%;
    }
    .layui-upload-img {
      width: 320px;
    }
    .layui-upload-img1 {
      width: 100px;
    }
  </style>
  <script>
    var password = localStorage.getItem('admin_password') || prompt('请输入管理密码：');
    if(password != '123123'){
      history.back();
    }else{
      localStorage.setItem('admin_password', '123123');
    }
  </script>
</head>

<body>
  <div class="x-body" style="padding:20px">
    <form class="layui-form">
      <div class="layui-form-item">
        <label for="title" class="layui-form-label">
          <span style="color:red">*</span>活动标题
        </label>
        <div class="layui-input-block">
          <input type="text" id="title" name="title" required="" lay-verify="required" autocomplete="off" placeholder="请输入活动标题" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="desc" class="layui-form-label">
          <span style="color:red">*</span>简要描述
        </label>
        <div class="layui-input-block">
          <input type="text" id="desc" name="desc" required="" lay-verify="required" autocomplete="off" placeholder="请输入活动描述" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="distance" class="layui-form-label">
          <span style="color:red">*</span>完成条件
        </label>
        <div class="layui-input-block">
          <input type="number" id="distance" name="distance" required="" value="1" min="1" max="100" lay-verify="required" autocomplete="off" placeholder="完成条件:1-100（km）" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="period" class="layui-form-label">
          结束时间
        </label>
        <div class="layui-input-block">
          <input type="text" id="period" name="period" autocomplete="off" placeholder="结束时间（默认当前+30天）" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="content" class="layui-form-label">
          活动内容
        </label>
        <div class="layui-input-block">
          <textarea id="content" name="content" placeholder="请输入活动内容，使用 回车或<br> 换行" class="layui-textarea"></textarea>
        </div>
      </div>
      
      <div class="layui-form-item">
        <label for="mkey" class="layui-form-label">
          <span style="color:red">*</span>检索串
        </label>
        <div class="layui-input-block">
          <input type="text" id="mkey" name="mkey" required="" lay-verify="required" autocomplete="off" placeholder="检索串不能重复" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="type" class="layui-form-label">
          <span style="color:red">*</span>类型
        </label>
        <div class="layui-input-block">
          <input type="text" style="cursor:not-allowed" disabled id="type" name="type" value="0" required="" lay-verify="required" autocomplete="off" placeholder="0不可重复获得，1可以重复获得" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="name" class="layui-form-label">
          <span style="color:red">*</span>勋章名称
        </label>
        <div class="layui-input-block">
          <input type="text" id="name" name="name" required="" lay-verify="required" autocomplete="off" placeholder="勋章名称不能重复" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="honor_desc" class="layui-form-label">
          <span style="color:red">*</span>勋章描述
        </label>
        <div class="layui-input-block">
          <input type="text" id="honor_desc" name="honor_desc"  required="" lay-verify="required" autocomplete="off" placeholder="如：校区前100名，授予您一枚青铜勋章" class="layui-input">
        </div>
      </div>
      <div class="layui-upload">
        <button type="button" class="layui-btn" id="meid">上传勋章图片（必需）</button>
        <div class="layui-upload-list">
          <img class="layui-upload-img1" id="demo">
          <p id="demoText"></p>
        </div>
      </div>

      <div class="layui-upload">
        <button type="button" class="layui-btn" id="cover">上传封面图片（必需）</button>
        <div class="layui-upload-list">
          <img class="layui-upload-img" id="demo1">
          <p id="demoText"></p>
        </div>
      </div>

      <div class="layui-upload">
        <button type="button" class="layui-btn" id="imgs">上传活动介绍图组（可多张，非必需）</button>
        <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
          预览图：
          <div class="layui-upload-list" id="demo2"></div>
        </blockquote>
      </div>
      <div class="layui-form-item">
        <label for="L_repass" class="layui-form-label">
        </label>
        <button class="layui-btn" lay-filter="save" lay-submit="">
          创建
        </button>
      </div>
    </form>
  </div>
  <script src="../../layui/layui.js" charset="utf-8"></script>
  <script>
    layui.use(['form', 'layer', 'upload', 'laydate'], function() {
      $ = layui.jquery;
      var form = layui.form,
          layer = layui.layer,
          upload = layui.upload,
          laydate = layui.laydate;
      laydate.render({ 
        elem: '#period',
        type: 'datetime'
      });
      var cover = {};
      var imgs = [];
      //勋章数据
      var meid = 0;
      //勋章图片上传
      var uploadInst1 = upload.render({
          elem: '#meid',
          url: window.location.protocol+"//" + window.location.host + '/api/admin/uploadMedal', //改成您自己的上传接口
          field: 'img',
          method: 'post',
          headers: 'multipart/form-data',
          before: function(obj) {
            this.data = {
              mkey: $('#mkey').val(),
              type: $('#type').val(),
              name: $('#name').val(),
              desc: $('#honor_desc').val(),
            },
            //预读本地文件示例，不支持ie8
            obj.preview(function(index, file, result) {
              $('#demo').attr('src', result); //图片链接（base64）
            });
          },
          done: function(res) {
            //如果上传失败
            if (res.isSuccess) {
              meid = res.data.meid;
              return layer.msg('上传成功');
            }else{
              return layer.msg(res.msg);
            }
            // return layer.msg(res.msg);

            //上传成功
          },
          error: function() {
            //演示失败状态，并实现重传
            var demoText = $('#demoText');
            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
            demoText.find('.demo-reload').on('click', function() {
            uploadInst1.upload();
          });
        }
      });
      // 上传封面图
      var uploadInst = upload.render({
        elem: '#cover',
        url: window.location.protocol+"//" + window.location.host + '/api/main/uploadImg',
        field: 'img',
        method: 'post',
        before: function(obj) {
          //预读本地文件示例，不支持ie8
          obj.preview(function(index, file, result) {
            $('#demo1').attr('src', result); //图片链接（base64）
          });
        },
        done: function(res) {
          console.log(res)
          //如果上传失败
          if (res.code > 0) {
            return layer.msg('上传失败');
          }
          //上传成功
          cover = res.data;
          return layer.msg('上传成功');
        },
        error: function() {
          //演示失败状态，并实现重传
          var demoText = $('#demoText');
          demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
          demoText.find('.demo-reload').on('click', function() {
            uploadInst.upload();
          });
        }
      });

      //多图片上传
      upload.render({
        elem: '#imgs',
        url: window.location.protocol+"//" + window.location.host + '/api/main/uploadImg',
        field: 'img',
        multiple: true,
        before: function(obj) {
          //预读本地文件示例，不支持ie8
          obj.preview(function(index, file, result) {
            $('#demo2').append('<img src="' + result + '" alt="' + file.name + '" class="layui-upload-img">')
          });
        },
        done: function(res) {
          //上传完毕
          // console.log(res)
          imgs.push(res.data)
        }
      });
      //监听提交
      form.on('submit(save)', function(data) {
        data.field.cover = cover;
        data.field.imgs = imgs;
        data.field.meid = meid;
        $.ajax({
          url: window.location.protocol+"//" + window.location.host + '/api/pub/doActivity',
          method: 'post',
          data: data.field,
          dataType: 'JSON',
          success: function(res) {
            alert(res.msg);
            window.location.reload();
          },
          error: function(data) {

          }
        });
      });
    });
  </script>
</body>

</html>