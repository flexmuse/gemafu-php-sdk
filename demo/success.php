<?php
/**
 * Created by PhpStorm.
 * User: hofeng
 * Date: 2019-04-11
 * Time: 17:49
 */
include_once './gemafu_config.php';
$res = gemafu_query($_GET["orderid"]);
if ($res != 1) {
    $msg = "支付成功，请在管理中心刷新查看。";
}else{
    $msg = $res;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>充值成功</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.3/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.1/css/jquery-weui.min.css">
</head>
<body ontouchstart>
<div class="weui-msg">
    <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">操作成功</h2>
        <p class="weui-msg__desc"><?php echo $msg; ?></p>
    </div>
    <div class="weui-msg__extra-area">
        <div class="weui-footer">
            <p class="weui-footer__links">
                <a href="javascript:void(0);" class="weui-footer__link">个码付 - 优秀的个人二维码收款服务商</a>
            </p>
            <p class="weui-footer__text">Copyright © 2019</p>
        </div>
    </div>
</div>
</body>
</html>
