<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:36
 * @description:
 */

namespace Payment\Refund;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\RefundData;

class AliRefund extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        return RefundData::class;
    }
}