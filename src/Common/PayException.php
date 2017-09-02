<?php
namespace Payment\Common;

/**
 * @author: helei
 * @createTime: 2016-07-14 18:02
 * @description: 统一的异常处理类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
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
