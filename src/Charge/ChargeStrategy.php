<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:51
 * @description: 支付的策略接口
 */

namespace Payment\Charge;


interface ChargeStrategy
{
    public function charge();
}