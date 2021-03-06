# 微信个人码支付平台客服QQ：106755758
# 项目说明
项目地址：[http://gemafu.youzhuanshi.com/](http://gemafu.youzhuanshi.com/)

Git命令：`git clone https://github.com/flexmuse/gemafu-php-sdk.git`

SKD包下载：[https://github.com/flexmuse/gemafu-php-sdk/archive/master.zip](https://github.com/flexmuse/gemafu-php-sdk/archive/master.zip)

个码付，是一个自由，安全，便捷的微信个人二维码收款平台。能够帮助运营者通过个人微信收款码实现系统全自动收款、入单等工作。
### 自由
平台无需任何资质审查、进件，只要您拥有微信号即可收款，实现与企业收款一样的自动化功能。
### 安全
完全实时到账，0秒到达您的个人微信号，平台不做任何二清等资金留存，资金绝对安全。
### 便捷
大批的第三方服务商为您提供一对一的技术服务，让您的系统快速便捷接入到平台里来。
# 接入步骤
接入非常简单，全程均有服务商支持服务。

步骤：平台申请（联系客服，QQ：106755758） -> 根据本文档接入系统 -> 开始使用
# 基础说明
### SDK包文件结构
```
gemafu-php-sdk
│   gemafu_config.php   //主配置文件  
└───pay              //主程序包
│   │   check.php       //检测订单状态程序
│   │   pay.php         //下单程序
│   │   phpqrcode.php   //二维码生成类
│   │   qrcode.php      //显示二维码组件
│   └───js          //JS函数包
│       │   time.js     //计时函数
└───demo            //demo程序
    │   notify_url.php  //异步通知接收程序
    │   pay.php         //下单程序
    │   success.php     //支付后跳转界面
```
### 时序图
![时序图](https://cdn.flexmuse.com/gemafu/fodder/res/static/img/poe.jpg)
### 系统前置条件
1，首先需要获得平台账号，以获得商户号与密钥。

#### 在根目录中的主配置文件上面，替换这两个重要参数。
```php
<?php
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');
@error_reporting(E_ALL^E_NOTICE);
//客户支付配置，必填，重要！
define("GEMAFU_MID", 00000);//替换成你的商户号
define("SECRET_KEY", "xxxxxxxxxxxxxxxxxxxxxxxxxxx");//替换成你的秘钥
```
2，平台中配置好收款账号，请咨询客服，客服会帮助您检测是否完成。
### 接口调用
数据提交方式：POST

数据组织形式：JSON

请求数据结构：
```json
{
  "data": {
    "method": "PlaceOrder",
    "m_id": 9999,
    "....": "...."
  },
  "sign": "XXXXXXXXXXXXXXXXXX"
}
```
返回数据结构：
```json
{
"code": "200",
"orderid": 1234567890,
"...": "..."
}
```
2xx的均为正确返回，但是状态不同，唯有200状态下是带有数据返回，其他均会返回一个msg字段，信息就在msg。
### 签名方式
参与签名的数据：json里data字段下的所有内容

签名方式：data字段下所有的数据，key进行顺序排序，所有key和value均以字符串形式连接，最后再加上系统密钥，然后md5值为签名

特别注意：字符串连接中间没有任何分隔符什么的；签名就是md5小写，不用转换大写。

代码样例：
```php
function pay_sign($paydata){
    if(!is_array($paydata)){
        exit("data错误");
    }
    $key = SECRET_KEY;//配置文件里你的密钥
    ksort($paydata);//ksort方式排序即可
    $res = "";
    foreach ($paydata as $k => $v) {
        $res .= $k;
        $res .= $v;
    }
    $res .= $key;//连接时候中间不加任何东西
    $sign = md5($res);//直接md5即可，不用其他转换
    return $sign;
}
```
# 系统下单
### 调用方式
订单数据以数组形式在 `./demo/pay.php`里组织好后，程序会自动提交到`./pay/pay.php`，您也可以根据demo里的形式，直接提交到下单程序。

### 请求字段表
编号|字段名|类型|必要性|说明
:--:|--|:--:|:--:|--
1|method|字符串|必填|下单为常量：PlaceOrder
2|m_id|数值|必填|商户号
3|m_oid|字符串|必填|商户订单号
4|m_money|数值|必填|下单金额，单位为：分
5|timestamp|数值|必填|下单时间戳
6|b_url|字符串|必填|异步通知地址
7|userid|字符串|必填|客户唯一标识
8|appname|字符串|必填|商户系统应用名称
9|f_url|字符串|非必填|付款完成后跳转地址
10|body|字符串|非必填|商户根据自己需要带的数据

### 返回字段表
编号|字段名|类型|说明
:--:|--|:--:|--
1|code|字符串|正确返回为200
2|orderid|数值|平台订单号
3|m_oid|字符串|商户订单号
4|m_money|数值|下单金额，单位为：分
5|userid|字符串|客户唯一标识
6|appname|字符串|商户系统应用名称
7|f_url|字符串|付款完成后跳转地址
8|money|数值|客户需支付金额
9|qr_content|字符串|付款二维码内容
10|freecode|字符串|付款完成后跳转地址
11|expiration|数值|订单到期时间戳
# 订单查询
### 调用方式
订单查询在 `./pay/pay.php`里自动通过ajax形式调用同目录的check.php进行查询，有结果后，自动跳转程到下单时候定的f_user字段。

### 请求字段表
编号|字段名|类型|必要性|说明
:--:|--|:--:|:--:|--
1|method|字符串|必填|查询订单为常量：QueryOrder
2|orderid|数值|必填|平台订单号
3|timestamp|数值|必填|当前请求时间戳

### 返回字段表
编号|字段名|类型|说明
:--:|--|:--:|--
1|code|字符串|正确返回为200
2|status|数值|状态码，0或1为未完成，2为完成，3和4均为完成支付，但是异步通知失败，4处于自动重试中，3为重试后也失败。

## 重要：为了方便js查询，此处如果签名传送固定值："nosign"，系统则不验证签名即可查询。
# 异步通知
本demo的异步通知样例是放在`./demo/notify_url.php`里，通知校验完成后商户系统完成自己业务。异步通知如果失败，可以在平台看到商户系统返回的内容是什么。
## 重要：仅需要在异步通知成功后返回200字符串在body里，平台将认为异步通知成功
### 请求字段表（平台向商户发起）
编号|字段名|类型|说明
:--:|--|:--:|--
1|orderid|数值|平台订单号
2|payid|数值|平台支付号
3|pubtime|数值|微信支付时间戳
4|m_oid|字符串|商户订单号
5|userid|字符串|客户唯一标识
6|appname|字符串|商户系统应用名称
7|money|数值|客户实际支付金额
8|f_url|字符串|付款完成后跳转地址
9|body|字符串|商户下单时候带的数据
### 返回字段表
body里打印`200`即可，有其他错误正常打印，平台将记录非200信息，以供debug查询。
# 常见问题
### 请求失败
1，查看前置填写的的商户号密钥等是否正常。

2，平台内手续费如果不足，将不能下单。

3，是否缺少必填字段。

4，其他情况收集返回信息，提供给客服查询。
### 签名错误
1，查看商户号与密钥填写是否正确。

2，是否有字段类型发送错误。
### 收不到异步通知
1，先在平台内查看返回信息进行debug查询。

2，提交的异步通知地址是否正确？

3，接收到的POST为空，如果是PHP7，请使用`$data = file_get_contents('php://input');`，如果是5.x，请使用`$data = $GLOBALS['HTTP_RAW_POST_DATA'];`
### 掉单
1，强烈推荐上传多个收款人以及多个固定收款码（详细情况咨询客服），因为自由输入模式很容易客户输入错误造成不能及时对上单。

2，如果是技术性掉单，系统每分钟都有AI机器人自动进行补单处理，稍等即可。

3，的确无法自动对应，请在平台中进行手工补单，如果出现错误，把错误信息截屏给客服处理。
# 客服QQ：106755758