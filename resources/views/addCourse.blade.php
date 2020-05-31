<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>
    Pop Run！ 跑鸭！创建课程
  </title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="../../layui/css/layui.css" media="all">
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
          <span style="color:red">*</span>标题
        </label>
        <div class="layui-input-inline">
          <input type="text" id="title" name="title" required="" lay-verify="required" autocomplete="off" placeholder="请输入课程标题" class="layui-input">
        </div>
      </div>
      <div class="layui-form-item">
        <label for="text" class="layui-form-label">
          <span style="color:red">*</span>内容
        </label>
        <div class="layui-input-block">
          <textarea placeholder="请输入内容" id="text" required="" name="text" lay-verify="required" placeholder="请输入课程内容" class="layui-textarea"></textarea>
        </div>
      </div>

      <div class="layui-upload">
        <button type="button" class="layui-btn" id="img">上传封面图片</button>
        <div class="layui-upload-list">
          <img class="layui-upload-img" id="demo1">
          <p id="demoText"></p>
        </div>
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
    layui.use(['form', 'layer', 'upload'], function() {
      $ = layui.jquery;
      var form = layui.form,
        layer = layui.layer,
        upload = layui.upload;
      var img = {};

      var uploadInst = upload.render({
        elem: '#img',
        url: window.location.protocol+"//" + window.location.host + '/api/main/uploadImg',
        method: 'post',
        field: 'img',
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
          img = res.data;
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

      //监听提交
      form.on('submit(save)', function(data) {
        data.field.img = img;
        console.log(data);
        $.ajax({
          url: window.location.protocol+"//" + window.location.host + /api/pub/doCourse',
          method: 'post',
          data: data.field,
          dataType: 'JSON',
          success: function(res) {
            alert(res.msg)
          },
          error: function(data) {
            return layer.msg(data);
          }
        });
      });
    });
  </script>
</body>

</html>