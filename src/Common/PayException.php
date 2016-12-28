<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 18:02
 * @description: 统一的异常处理类
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common;


class PayException extends \Exception
{
    /**
     * 获取异常错误信息
     * @return string
     * @author helei
     */
    public function errorMessage()
    {
        return $this->getMessage();
    }
}