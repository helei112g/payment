<?php
/**
 * @author: helei
 * @createTime: 2016-08-02 10:27
 * @description:
 */

namespace Payment\Common\Weixin\Data;
use Payment\Common\PayException;


/**
 * Class BackPubChargeData
 *
 * @property string $device_info   设备号
 * @property string $trade_type  交易类型
 * @property string $prepay_id   预支付交易会话标识
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class BackPubChargeData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appId' => $this->appId,
            'package'   => 'prepay_id=' . $this->prepay_id,
            'nonceStr'  => $this->nonceStr,
            'timeStamp' => time() . '',
            'signType'  => 'MD5',
        ];
    }

    protected function checkDataParam()
    {
        // 不进行检查
    }
}