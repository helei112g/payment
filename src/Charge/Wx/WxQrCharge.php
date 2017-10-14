<?php
namespace Payment\Charge\Wx;

use Payment\Common\Weixin\Data\Charge\QrChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

/**
 * @author: helei
 * @createTime: 2016-07-14 18:29
 * @description: 微信 扫码支付  主要用于网站上
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class WxQrCharge extends WxBaseStrategy
{
    public function getBuildDataClass()
    {
        $this->config->tradeType = 'NATIVE';// 微信文档这里写错了
        return QrChargeData::class;
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        if ($this->config->returnRaw) {
            return $ret;
        }

        // 扫码支付，返回链接
        return $ret['code_url'];
    }
}
