<?php
/**
 * @author: helei
 * @createTime: 2016-08-02 09:41
 * @description:
 */

namespace Payment\Common\Weixin\Data;

use Payment\Common\PayException;

/**
 * Class BackAppChargeData
 *
 * @property string $device_info   设备号
 * @property string $trade_type  交易类型
 * @property string $prepay_id   预支付交易会话标识
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class BackAppChargeData extends WxBaseData
{
    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'partnerid' => $this->mchId,
            'prepayid'  => $this->prepay_id,
            'package'   => 'Sign=WXPay',
            'noncestr'  => $this->nonceStr,
            'timestamp' => time(),
        ];
    }

    protected function checkDataParam()
    {
        // 对于返回数据不做检查检查
    }
}
