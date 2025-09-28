<?php
    include_once('config.php');

    function get_weixin_data($url) {
        global $ticket_filename;
        global $APPID;

        $ticket_json = file_get_contents($ticket_filename);
        $ticket_array = json_decode($ticket_json, true);
        $jsapi_ticket = $ticket_array["ticket"];

        $timestamp = time();
        $nonceStr = sha1("1qaz2wsx3edc4rfv".$timestamp);
        // print_r("jsapi_ticket=$jsapi_ticket");
        // echo "\n";
        // print_r("noncestr=$nonceStr");
        // echo "\n";
        // print_r("timestamp=$timestamp");
        // echo "\n";
        // print_r("url=$url");
        // echo "\n";
        $signature = sha1("jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url");
        
        $data = array(
            "timestamp" => $timestamp,
            "nonceStr" => $nonceStr,
            "signature" => $signature,

            // "jsapi_ticket" => $jsapi_ticket,
            // "url" => $url,
            // "appId" => $APPID,
        );
        return $data;
    }
?>
