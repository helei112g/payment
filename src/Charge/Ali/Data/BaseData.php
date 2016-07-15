<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description:
 */

namespace Payment\Charge\Ali\Data;

use Payment\Common\AliConfig;

abstract class BaseData
{
    /**
     * 支付的请求数据
     * @var string $reqData
     */
    protected $data;

    public function __construct(AliConfig $config, array $payData)
    {
        $this->data = array_merge($payData, $config->toArray());
    }

    /**
     * 获取变量，通过魔术方法
     * @param string $name
     * @return null|string
     * @author helei
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }
}