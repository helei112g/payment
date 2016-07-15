<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:42
 * @description:
 */

namespace Payment\Common;


abstract class ConfigInterface
{
    public function toArray()
    {
        return get_object_vars($this);
    }
}