<?php
    include_once('config.php');

    // 定时运行两小时一次
    $access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";
    $ACCESS_TOKEN_json = file_get_contents($access_token_url);
    file_put_contents($access_token_filename, $ACCESS_TOKEN_json);

    $ACCESS_TOKEN_array = json_decode($ACCESS_TOKEN_json, true);
    $ACCESS_TOKEN = $ACCESS_TOKEN_array["access_token"];
    // $ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$ACCESS_TOKEN&type=wx_card";
    $ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$ACCESS_TOKEN&type=jsapi";
    $ticket_content = file_get_contents($ticket_url);
    file_put_contents($ticket_filename, $ticket_content);
?>