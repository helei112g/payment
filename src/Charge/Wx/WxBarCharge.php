<?php
namespace Payment\Charge\Wx;

use Payment\Common\Weixin\Data\Charge\BarChargeData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;

/**
 * @author: helei
 * @createTime: 2017-03-06 18:29
 * @description: 微信 刷卡支付  对应支付宝的条码支付
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class WxBarCharge extends WxBaseStrategy
{
    public function getBuildDataClass()
    {
        return BarChargeData::class;
    }

    /**
     * 刷卡支付 的请求地址是另外一个
     * @return string
     */
    protected function getReqUrl()
    {
        return WxConfig::MICROPAY_URL;
    }

    protected function retData(array $ret)
    {
        $ret['total_fee'] = bcdiv($ret['total_fee'], 100, 2);
        $ret['cash_fee'] = bcdiv($ret['cash_fee'], 100, 2);

        if ($this->config->returnRaw) {
            return $ret;
        }

        return $ret;
    }
}
