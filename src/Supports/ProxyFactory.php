<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Payment\Supports;

use InvalidArgumentException;
use Payment\Payment;

/**
 * @package Payment\Supports
 * @author  : Leo
 * @email   : dayugog@gmail.com
 * @date    : 2019/3/30 2:20 PM
 * @version : 1.0.0
 * @desc    : 创建代理的工厂类
 **/
class ProxyFactory
{
    /**
     * @param string $proxyName
     * @return BaseObject
     * @throws InvalidArgumentException
     */
    public static function createProxy(string $proxyName)
    {
        $className = self::formatProxyClassName($proxyName);

        try {
            $proxy = self::makeProxy($className);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
        return $proxy;
    }

    /**
     * 格式化支付代理的名称
     * @param string $proxyName
     * @return string
     */
    protected static function formatProxyClassName(string $proxyName)
    {
        $name = ucfirst(str_replace(['-', '_', ''], '', $proxyName));

        return "Payment\\Proxies\\{$name}Proxy";
    }

    /**
     *
     * @param string $className
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected static function makeProxy(string $className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Proxy [%s] not exists.', $className), Payment::CLASS_NOT_EXIST);
        }

        return new $className();
    }
}
