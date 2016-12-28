<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:42
 * @description: 配置文件接口，主要提供返回属性数组的功能
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common;


abstract class ConfigInterface
{
    public function toArray()
    {
        return get_object_vars($this);
    }
}