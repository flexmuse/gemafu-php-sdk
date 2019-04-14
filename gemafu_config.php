<?php
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');
@error_reporting(E_ALL^E_NOTICE);

//客户支付配置，必填，重要！
define("GEMAFU_MID", 00000);//替换成你的商户号 m_id
define("SECRET_KEY", "xxxxxxxxxxxxxxxxxxxxxxxxxxx");//替换成你的秘钥
/*
以下参数请勿修改
*/

//SDK配置，
define("PAY_API", 'https://fcapi.flexmuse.com/pay');//请求的api
//获取下单地址
function gemafu_order($paydata){
    if(!is_array($paydata)){
        exit("data错误");
    }
    $data = array(
        "method" => "PlaceOrder",
        "m_id" => GEMAFU_MID,
        "m_oid" => "",//你的订单号
        "m_money" =>'',//你的金额 单位分
        "timestamp" => time(),
        "f_url" => PAY_RETURN_URL,//支付成功后返回地址
        "b_url" => PAY_NOTIFY_URL,//异步回调地址
        "userid" => '',//你的用户id
        "appname" => '',//你的项目名称
        );
    $data=array_merge($data,$paydata);

    extract($data);

    if(empty($m_id)){exit("m_id没有填写");}
    if(empty($m_money)){exit("金额不能为空");}
    if(empty($userid)){exit("付款用户id不能为空");}
    if(empty($m_oid)){$data['m_oid']=md5(time() . mt_rand(1,1000000));}
    if(empty($appname)){exit("appname没有填写");}
    if(!empty($f_url)){
        $data['f_url']=urldecode($f_url);//
    }
    if(!empty($b_url)){
        $data['b_url']=urldecode($b_url);//
    }else{
        exit("异步回调地址没有填写");
    }
    $data['sign'] = pay_sign($data);
    
    if(!empty($f_url)){
        $data['f_url']=urlencode($f_url);//
    }
    if(!empty($b_url)){
        $data['b_url']=urlencode($b_url);//
    }

    $url_quer=http_build_query($data);
    $url="/recharge/pay/pay.php?{$url_quer}";
    return $url;
}

//查询订单
function gemafu_query($orderid){
    if(!$orderid){
        exit("data错误");
    }
    $data = array(
        "method" => "QueryOrder",
        "m_id" => GEMAFU_MID,
        "orderid" => (int)$orderid,//订单号
        "timestamp" => (int)time()
    );
    $sign = pay_sign($data);
    $postdata = array(
        "data" => $data,
        "sign" => $sign
    );
    $res = HttpsRequest($postdata);
    if($res["code"] == "200"){
        return (int)$res["status"];
    }
    else{
        return $res["msg"];
    }
}
//支付下单计算签名
function pay_sign($paydata){
    if(!is_array($paydata)){
        exit("data错误");
    }
    $key = SECRET_KEY;
    ksort($paydata);
    $res = "";
    foreach ($paydata as $k => $v) {
        $res .= $k;
        $res .= $v;
    }
    $res .= $key;
    $sign = md5($res);
    return $sign;
}

function HttpsRequest($post_data){
    $data = json_encode($post_data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, PAY_API);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $getdata = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($getdata, true);
    return $result;
}