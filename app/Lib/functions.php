<?php

function returnData($isSuccess, $msg, $data){
    return [
        'isSuccess' => $isSuccess,
        'msg' => $msg,
        'data' => $data
    ];
}