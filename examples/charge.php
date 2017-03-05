<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Config;
use Payment\Common\PayException;
use Payment\Client\Charge;

date_default_timezone_set('Asia/Shanghai');


// 订单信息
$payData = [
    'subject'    => 'test',
    'body'    => 'test',
    'order_no'    => time() . rand(1000, 9999),// 14887239163319   14887240631516
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'timeout_express' => time() + 600,// 表示必须 60s 内付款
    'scene' => 'bar_code',// 条码支付：bar_code 声波支付：wave_code
    'auth_code' => '281590693078650089',// 用户付款码
];

// 微信扫码支付，需要设置的参数
$payData['product_id']  = '123456';

// 微信公众号支付，需要的参数
$payData['openid'] = 'oinNst2_hWU_5oBigLd8n3-59PCc';// 需要通过微信提供的api获取该openid

/**
 * 包含客户的配置文件
 * 本次 2.0 版本，主要的改变是将配置文件独立出来，便于客户多个账号的情况
 * 已经使用不同方式读取配置文件，如：db  file   cache等
 */
$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';

$channel = 'ali_bar';
try {
    $ret = Charge::pay($channel, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

if ($channel === Config::ALI_CHANNEL_APP) {
    echo htmlspecialchars($ret);
    exit;
} elseif ($channel === Config::ALI_CHANNEL_QR) {
    $url = \Payment\Utils\DataParser::toQRimg($ret);// 内部会用到google 生成二维码的api  可能有些同学反应很慢
    echo "<img alt='支付宝扫码支付' src='{$url}' style='width:150px;height:150px;'/>";
    exit;
} elseif ($channel === Config::ALI_CHANNEL_BAR) {// 条码支付，直接返回支付结果
    var_dump($ret);
    exit;
} elseif ($channel === Config::WX_CHANNEL_QR) {
    $url = \Payment\Utils\DataParser::toQRimg($ret);
    echo "<img alt='微信扫码支付' src='{$url}' style='width:150px;height:150px;'/>";
    exit;
} elseif ($channel === Config::WX_CHANNEL_PUB) {
    $json = $ret;
    var_dump($json);
} elseif (stripos($channel, 'wx') !== false) {
    var_dump($ret);
    exit;
} elseif (stripos($channel, 'ali') !== false) {
    // 跳转支付宝
    header("Location:{$ret}");
}

?>

<!--微信公众号支付-->
<?php if ($channel === Config::WX_CHANNEL_PUB) : ?>

    <html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>微信支付样例-支付</title>
        <script type="text/javascript">
            //调用微信JS api 支付
            function jsApiCall()
            {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    <?php echo $json; ?>,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        alert(res.err_code+res.err_desc+res.err_msg);
                    }
                );
            }

            function callpay()
            {
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
            }
        </script>
    </head>
    <body>
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
    <div align="center">
        <button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
    </div>
    </body>
    </html>
<?php endif;?>
