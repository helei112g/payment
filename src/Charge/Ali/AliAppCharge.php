<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:20
 * @description: 支付宝移动支付接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Ali;


use Payment\Common\Ali\Data\Charge\AppChargeData;

class AliAppCharge extends AliCharge
{
    /**
     * 获取支付对应的数据完成类
     * @return string
     * @author helei
     */
    protected function getChargeDataClass()
    {
        // 以下两种方式任选一种
        return AppChargeData::class;

        //return 'Payment\Common\Ali\Data\Charge\AppChargeData';
    }
}