<?php
header('Content-Type: text/html; charset=utf-8');
//加载config文件
require_once dirname(__FILE__) . '/../gemafu_config.php';
$b_url = urldecode($_GET['b_url']);//解码支付成功后异步页面
$f_url = urldecode($_GET['f_url']);//解码支付成功后同步页面
//下单
$tmp_data = array(
    "method" => $_GET['method'],
    "m_id" => (int)$_GET['m_id'],
    "m_oid" => $_GET['m_oid'],
    "m_money" => (int)$_GET['m_money'],
    "timestamp" => (int)$_GET['timestamp'],
    "f_url" => $f_url,
    "b_url" => $b_url,
    "userid" => $_GET['userid'],
    "appname" => $_GET['appname']
);

$post_data = array(
    "data" => $tmp_data,
    "sign" => $_GET['sign']
);
$result = HttpsRequest($post_data);
if ($result['code'] != "200") {
    exit($result['msg']);
}
$alert = "";
$money = $result["money"] / 100;
if ($result['freecode']) {
    $alert = "$.alert(\"支付时请输入<b><font color='red'>" . $money . "</font></b>元，一分不多一分不少，否则订单无法生效，切记！\", \"重要提醒\");";
}
//过期时间
$expiration = $result['expiration'] - time();
?>
<!DOCTYPE html>
<html>
<head>
    <title>个码付 微信个人码收款工具</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.3/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.1/css/jquery-weui.min.css">
    <style>
        body, html {
            height: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        .demos-title {
            text-align: center;
            font-size: 34px;
            color: #3cc51f;
            font-weight: 400;
            margin: 0 15%;
        }

        .demos-sub-title {
            text-align: center;
            color: #888;
            font-size: 14px;
        }

        .demos-header {
            padding: 35px 0;
        }

        .demos-content-padded {
            padding: 15px;
        }

        .demos-second-title {
            text-align: center;
            font-size: 24px;
            color: #3cc51f;
            font-weight: 400;
            margin: 0 15%;
        }

        footer {
            text-align: center;
            font-size: 14px;
            padding: 20px;
        }

        footer a {
            color: #999;
            text-decoration: none;
        }

        .scroll {
            position: absolute;
            overflow: scroll;
            -webkit-overflow-scrolling: touch;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;

    </style>
</head>
<body>
<div class="scroll">
    <header class="demos-header">
        <h1 class="demos-title">微信支付</h1>
    </header>

    <div class="weui-form-preview">
        <div class="weui-form-preview__hd">
            <label class="weui-form-preview__label">付款金额</label>
            <em class="weui-form-preview__value">¥<?php echo $money; ?></em>
        </div>
        <div class="weui-form-preview__bd">
            <div class="weui-panel__bd">
                <div class="weui-media-box__hd">
                    <center><img width="60%" class="weui-media-box__thumb"
                                 src="qrcode.php?text=<?php echo $result['qr_content']; ?>"></center>
                </div>
            </div>
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">订单号</label>
                <span class="weui-form-preview__value"><?php echo $result['orderid']; ?></span>
            </div>
            <!-- 这里可以根据自己业务情况增加显示字段 -->
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">倒计时</label>
                <span class="weui-form-preview__value" id="time"><?php echo $result['orderid']; ?></span>
            </div>
            <div class="weui-form-preview__ft">
                <button class="weui-form-preview__btn weui-form-preview__btn_primary">长按识别二维码支付</button>
            </div>
        </div>
    </div>

    <div class="weui-footer weui-footer_fixed-bottom">
        <p class="weui-footer__links">
            <a href="javascript:void(0);" class="weui-footer__link">个码付 - 优秀的个人二维码收款服务商</a>
        </p>
        <p class="weui-footer__text">Copyright © 2019</p>
    </div>
</div>
</body>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.1/js/jquery-weui.min.js"></script>
<script src='js/time.js'></script>
<script>
    $(function () {
        stopDrop();
        ajaxDataPostRequest();
        resetTime();
        <?php echo $alert;?>
    });

    function ajaxDataPostRequest() {
        $.ajax({
            url: "check.php",
            data: {
                orderid: '<?php echo $result['orderid']; ?>'
            },
            type: 'POST',
            dataType: 'html',
            async: false,
            error: function (result) {
                console.log(result);
            },
            success: function (result) {
                console.log(result);
                if (result >= 2) {
                    window.location.href = "<?php echo $f_url; ?>";
                } else {
                    setTimeout("ajaxDataPostRequest()", 5000);
                }

            }
        });
    }

    function resetTime() {
        var timer = null;
        var t =<?php echo $expiration; ?>;
        var m = 0;
        var s = 0;
        m = Math.floor(t / 60 % 60);
        m < 10 && (m = '0' + m);
        s = Math.floor(t % 60);

        function countDown() {
            s--;
            s < 10 && (s = '0' + s);
            if (s.length >= 3) {
                s = 59;
                m = "0" + (Number(m) - 1);
            }
            if (m.length >= 3) {
                m = '00';
                s = '00';
                clearInterval(timer);
            }
            console.log(m + "分钟" + s + "秒");
            $("#time").html(m + "分钟" + s + "秒");
            if (m == "00" && s == "00") {
                $.alert({
                    title: '订单过期',
                    text: '很遗憾，您的订单已经过期，请重新下单。',
                    onOK: function () {
                        history.go(-1)
                    }
                });
            }
        }

        timer = setInterval(countDown, 1000);
    }

    function stopDrop() {
        var lastY;//最后一次y坐标点
        $(document.body).on('touchstart', function(event) {
            lastY = event.originalEvent.changedTouches[0].clientY;//点击屏幕时记录最后一次Y度坐标。
        });
        $(document.body).on('touchmove', function(event) {
            var y = event.originalEvent.changedTouches[0].clientY;
            var st = $(this).scrollTop(); //滚动条高度  
            if (y >= lastY && st <= 10) {//如果滚动条高度小于0，可以理解为到顶了，且是下拉情况下，阻止touchmove事件。
                lastY = y;
                event.preventDefault();
            }
            lastY = y;

        });
    }
</script>
</html>