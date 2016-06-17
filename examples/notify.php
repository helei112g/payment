<?php
/**
 * @author: helei
 * @createTime: 2016-06-17 10:14
 * @description: 支付成功后的回调通知
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Contracts\PayNotifyInterface;
use Payment\Common\ChargeChannel;
use Payment\Factory\TradeFactory;
use Payment\Common\PayException;

/**
 * 首先该lib已经处理了相关的验证，并且可以进行自动的回调。
 * 现在我假设你自己的业务处理类叫做：NotifyOrder
 */

class NotifyOrder implements PayNotifyInterface
{

    /**
     * 必须要实现的方法。我会对该商户实现的方法进行回调
     * @param array $data
     * @return bool
     * @author helei
     */
    public function notifyProcess($data)
    {
        // 1. 检查订单是否已经被更新过了。
        // 2. 检查金额是否正确
        // 3. 其他逻辑
        // 4. 进行更新订单状态
        return true;
    }
}

// 支付宝的回调
$payway = ChargeChannel::CHANNEL_IS_ALIPAY;

// 微信的回调
//$payway = ChargeChannel::CHANNEL_IS_WX;

$api = TradeFactory::getInstance($payway);

$notify = new NotifyOrder();

try {
    $ret = $api->notify($notify);

    echo $ret;exit;// 执行成功时，需要输出结果
} catch (PayException $e) {
    echo $e->errorMessage();
}