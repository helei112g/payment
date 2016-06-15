<?php
/**
 * @author: helei
 * @createTime: 2016-06-14 15:33
 * @description:
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Factory\ChargeFactory;
use Payment\Common\ChargeChannel;

// 获取支付宝手机支付实例
$mobileCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY);
var_dump($mobileCharge);

// 获取支付宝网站支付实例
$pcCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY_DIRECT);
var_dump($pcCharge);
