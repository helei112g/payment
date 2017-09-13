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
    protected $reqUrl = 'https://api.mch.weixin.qq.com/{debug}/pay/micropay';

    public function getBuildDataClass()
    {
        return BarChargeData::class;
    }

    /**
     * 返回的数据
     * @param array $ret
     * @return array
     */
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
