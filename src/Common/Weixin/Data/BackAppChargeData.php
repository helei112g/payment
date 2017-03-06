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
