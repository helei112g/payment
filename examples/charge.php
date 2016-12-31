<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\ChargeContext;
use Payment\Config;
use Payment\Common\PayException;

date_default_timezone_set('Asia/Shanghai');


//  生成订单号 便于测试
function createPayid()
{
    return date('Ymdhis', time()).substr(floor(microtime()*1000),0,1).rand(0,9);
}

// 订单信息
$payData = [
    "order_no"	=> '201612311430',
    "amount"	=> '10.00',// 单位为元 ,最小为0.01
    "client_ip"	=> '127.0.0.1',
    "subject"	=> 'test',
    "body"	=> 'test wap pay',
    "show_url"  => 'https://helei112g.github.io/',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
    "extra_param"	=> '',
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
$aliconfig = require_once __DIR__ . '/aliconfig.php';
$wxconfig = require_once __DIR__ . '/wxconfig.php';

/**
 * 实例化支付环境类，进行支付创建
 */
$charge = new ChargeContext();

try {
    // 支付宝即时到帐接口  新版本，不再支持该方式
    //$type = Config::ALI_CHANNEL_WEB;

    // 支付宝 手机网站支接口
    $type = Config::ALI_CHANNEL_WAP;

    // 支付宝 移动支付接口
    //$type = Config::ALI_CHANNEL_APP;

    // 支付宝  扫码支付
    //$type = Config::ALI_CHANNEL_QR;

    $charge->initCharge($type, $aliconfig);

    // 微信 扫码支付
    //$type = Config::WX_CHANNEL_QR;

    // 微信 APP支付
    //$type = Config::WX_CHANNEL_APP;

    // 微信 公众号支付
    //$type = Config::WX_CHANNEL_PUB;

    //$charge->initCharge($type, $wxconfig);
    $ret = $charge->charge($payData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

if ($type === Config::ALI_CHANNEL_APP) {
    echo $ret;exit;
} elseif ($type === Config::ALI_CHANNEL_QR) {
    $url = \Payment\Utils\DataParser::toQRimg($ret);// 内部会用到google 生成二维码的api  可能有些同学反应很慢
    echo "<img alt='支付宝扫码支付' src='{$url}' style='width:150px;height:150px;'/>";exit;
} elseif ($type === Config::WX_CHANNEL_QR) {
    $url = \Payment\Utils\DataParser::toQRimg($ret);
    echo "<img alt='微信扫码支付' src='{$url}' style='width:150px;height:150px;'/>";exit;
} elseif ($type === Config::WX_CHANNEL_PUB) {
    $json = $ret;
    var_dump($json);
} elseif (stripos($type, 'wx') !== false) {
    var_dump($ret);exit;
} elseif (stripos($type, 'ali') !== false) {
    // 跳转支付宝
    header("Location:{$ret}");
}

?>

<!--微信公众号支付-->
<?php if ($type === Config::WX_CHANNEL_PUB) { ?>

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
<?php } ?>

