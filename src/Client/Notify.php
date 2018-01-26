<?php
namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;
use Payment\NotifyContext;

/**
 * @author: helei
 * @createTime: 2017-09-02 18:20
 * @description: 异步通知的客户端类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class Notify
 * @package Payment\Client
 */
class Notify
{
    private static $supportChannel = [
        Config::ALI_CHARGE,// 支付宝

        Config::WX_CHARGE,// 微信

        Config::CMB_CHARGE,// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * 异步通知类
     * @var NotifyContext
     */
    protected static $instance;

    protected static function getInstance($type, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new NotifyContext();
        }

        try {
            static::$instance->initNotify($type, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

    /**
     * 执行异步工作
     * @param string $type
     * @param array $config
     * @param PayNotifyInterface $callback
     * @return array
     * @throws PayException
     */
    public static function run($type, $config, $callback)
    {
        if (! in_array($type, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该异步方式，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($type, $config);

            $ret = $instance->notify($callback);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }

    /**
     * 返回异步通知的结果
     * @param $type
     * @param $config
     * @return array|false
     * @throws PayException
     */
    public static function getNotifyData($type, $config)
    {
        try {
            $instance = self::getInstance($type, $config);

            return $instance->getNotifyData();
        } catch (PayException $e) {
            throw $e;
        }
    }
}
