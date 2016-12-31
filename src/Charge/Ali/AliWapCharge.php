<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:19
 * @description: 支付宝 手机网站支付 接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Ali;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\WapChargeData;
use Payment\Common\AliConfig;

class AliWapCharge extends AliBaseStrategy
{

    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::ALI_TRADE_WAP;
        // 以下两种方式任选一种
        return WapChargeData::class;

        //return 'Payment\Common\Ali\Data\Charge\WapChargeData';
    }
}