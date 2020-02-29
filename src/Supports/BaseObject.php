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

/**
 * Class Object
 * @package Payment\Supports
 * @author  : Leo
 * @date    : 2019/3/30 2:39 PM
 * @version : 1.0.0
 * @desc    : 整个lib的基础类
 *
 */
abstract class BaseObject
{
    const VERSION = '5.0.1';

    /**
     * @var Config
     */
    public static $config = null;

    /**
     * 获取版本号
     * @return string
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

    /**
     * 获取类名
     * @return string
     */
    public function className()
    {
        return get_called_class();
    }

    /**
     * 设置配置文件
     * @param array $config
     */
    public function setConfig(array $config)
    {
        self::$config = new Config($config);
    }

    /**
     * 项目根路径
     */
    public function getBasePath()
    {
        $path = realpath(dirname(dirname(__FILE__)));
        return $path;
    }
}
