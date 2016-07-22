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

//  生成订单号 便于测试
function createPayid()
{
    return date('Ymdhis', time()).substr(floor(microtime()*1000),0,1).rand(0,9);
}

// 订单信息
$payData = [
    "order_no"	=> createPayid(),
    "amount"	=> '0.01',// 单位为元 ,最小为0.01
    "client_ip"	=> '127.0.0.1',
    "subject"	=> '      测试支付',
    "body"	=> '支付接口测试',
    "show_url"  => 'http://mall.tiyushe.com/goods/23.html',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
    "extra_param"	=> '',
];

/**
 * 包含客户的配置文件
 * 本次 2.0 版本，主要的改变是将配置文件独立出来，便于客户多个账号的情况
 * 已经使用不同方式读取配置文件，如：db  file   cache等
 */
$aliconfig = require_once __DIR__ . '/aliconfig.php';

/**
 * 实例化支付环境类，进行支付创建
 */
$charge = new ChargeContext();

try {
    // 支付宝即时到帐接口
    $charge->initCharge(Config::ALI_CHANNEL_WEB, $aliconfig);

    // 支付宝 手机网站支接口
    //$charge->initCharge(Config::ALI_CHANNEL_WAP, $aliconfig);
    $url = $charge->charge($payData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

// 跳转支付宝
header("Location:{$url}");
