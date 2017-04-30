<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 上午10:44
 */

namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\HelperContext;

/**
 * 提供给客户端的辅助类
 * Class Helper
 * @package Payment\Client
 */
class Helper
{
    private static $supportChannel = [
        Config::CMB_BIND,// 招商绑卡操作
        Config::CMB_PUB_KEY,// 招商公钥查询操作
    ];

    /**
     * 异步通知类
     * @var HelperContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        if (is_null(self::$instance)) {
            static::$instance = new HelperContext();

            try {
                static::$instance->initHelper($channel, $config);
            } catch (PayException $e) {
                throw $e;
            }
        }

        return static::$instance;
    }

    /**
     * @param string $channel
     * @param array $config
     * @param array $metadata
     *
     * @return mixed
     * @throws PayException
     */
    public static function run($channel, $config, array $metadata = [])
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->helper($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}