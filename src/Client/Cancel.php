<?php
namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\CancelContext;

/**
 * @author: dong
 * @description: 取消操作客户端接口
 *
 * Class Cancel
 * @package Payment\Client
 */
class Cancel
{
    private static $supportChannel = [
        Config::ALI_CANCEL,// 支付宝

    ];

    /**
     * 取消实例
     * @var CancelContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new CancelContext();
        }

        try {
            static::$instance->initCancel($channel, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

    public static function run($channel, $config, $cancelData)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该关闭渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);
            $ret = $instance->cancel($cancelData);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}
