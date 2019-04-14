<?php
/**
 * Created by PhpStorm.
 * User: eyouke
 * Date: 2019/4/11
 * Time: 16:34
 */
header('Content-Type: text/html; charset=utf-8');
//加载config文件
require_once dirname(__FILE__) . '/../gemafu_config.php';

//查询
//echo $_POST['orderid'];
$tmp_data = array(
    "method" => "QueryOrder",
    "m_id" => (int)GEMAFU_MID,
    "timestamp" => time(),
    "orderid" => (int)$_POST['orderid']
);

$post_data = array(
    "data" => $tmp_data,
    "sign" => pay_sign($tmp_data)
);
$data = json_encode($post_data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, PAY_API);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$getdata = curl_exec($curl);
curl_close($curl);
//print_r($getdata);

$result = json_decode($getdata,true);
if($result['code'] != "200"){
    echo 0;
}else{
    echo $result['status'];
}
exit;