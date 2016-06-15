<?php
/**
 * @author: helei
 * @createTime: 2016-06-14 16:41
 * @description:
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Factory\ChargeFactory;
use Payment\Common\ChargeChannel;

// 微信APP支付实例
$appCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_WX);
var_dump($appCharge);