<?php
header('Content-Type: text/html; charset=utf-8');
//加载插件
require_once dirname(__FILE__) . '/../gemafu_config.php';
$result = @$GLOBALS['HTTP_RAW_POST_DATA'];
$result = json_decode($result,true);
$sign = $result['sign'];
$data = $result['data'];
if($sign != pay_sign($data)){
    exit("签名失效");
    //签名计算请查看怎么计算签名,或者下载我们的SDK查看
}
$orderid = (int)$data['orderid'];//订单号
$payid = (int)$data['payid'];//支付订单号
$pubtime = (int)$data['pubtime'];//支付时间
$m_oid = (string)$data['m_oid'];//商户订单号
$userid = (string)$data['userid'];//购买用户名
$appname = (string)$data['appname'];//购买系统名称
$money = (int)$data['money'];//支付金额 单位分
$f_url = (string)$data['f_url'];//跳转地址

//更新数据库
//这里放您的业务操作代码

echo "200";//全部完成以后，输出 200
