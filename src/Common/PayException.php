<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 21:47
 * @description:
 */

namespace Payment\Common;


class PayException extends \Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}