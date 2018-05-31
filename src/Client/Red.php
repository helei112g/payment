<?php
namespace Payment\Client;

use Payment\RedContext;
use Payment\Common\PayException;
use Payment\Config;

/**
 * @author: IT
 * @createTime: 2018-03-07 18:20
 * @description: 红包
 * Class Red
 * @package Payment\Client
 *
 */
class Red
{
    private static $supportChannel = [
        Config::ALI_RED,// 支付宝红包

        Config::WX_RED,// 微信红包
    ];

    /**
     * 支付实例
     * @var RedContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");
        
        if (is_null(self::$instance)) {
            static::$instance = new RedContext();
        }

        try {
            static::$instance->initRed($channel, $config);
        } catch (PayException $e) {
            throw $e;
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
    public static function run($channel, $config, $metadata)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该支付渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->red($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}
