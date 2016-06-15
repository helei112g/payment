<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 19:51
 * @description: 下单的统一接口   生成下单接口
 */

namespace Payment\Contracts;


interface ChargeInterface
{
    /**
     * 支付操作对象
     * @param array $data 支付需要的参数
     * @return mixed
     * @author helei
     */
    public function charges(array $data);
}