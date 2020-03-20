<?php

function returnData(){
    $args = func_get_args();
    $isSuccess = $args[0]; //必需：成功否 boolean
    $msg = $args[1]; //必需：提示信息  anytype
    $data = count($args)>2 ? $args[2] : $args[1]; //可选：数据，失败时可不传（使用提示信息）

    $msg = is_string($msg) ? $msg : json_encode($msg);
    return [
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data' => $data
    ];
}