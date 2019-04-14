<?php
header('Content-Type: text/html; charset=utf-8');
require_once dirname(__FILE__) . '/../gemafu_config.php';

$paydata=array();
$paydata['userid'] = (string)$m_id;//支付用户id，自己系统下单人的ID
$paydata['m_oid'] = (int)$orderid;//订单号
$paydata['m_money'] = (int)$money;//金额，单位为分
$paydata['method'] = "PlaceOrder";//下单指令，必填，不要修改
$paydata['timestamp'] = time();//时间戳，必填
$paydata['appname'] = "DEMO";//系统名称，根据自己情况修改
$paydata['f_url'] = "https://你自己的网址域名及个码付的运行目录/demo/success.php?orderid=".$orderid;//支付成功后跳转，修改自己的链接
$paydata['b_url'] = "https://你自己的网址域名及个码付的运行目录/demo/notify_url.php";//支付成功后异步回调，修改自己的链接
$geturl = gemafu_order($paydata);//向个码付下单，获取支付链接
header("location:".$geturl);

