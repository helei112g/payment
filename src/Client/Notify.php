<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午6:29
 */

namespace Payment\Client;


use Payment\Common\PayException;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;
use Payment\NotifyContext;

class Notify
{
    private static $supportChannel = [
        Config::ALI_CHARGE,// 支付宝

        Config::WX_CHARGE,// 微信

        'cmb_wallet',// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * 异步通知类
     * @var NotifyContext
     */
    protected static $instance;

    protected static function getInstance($type, $config)
    {
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

        $instance = self::getInstance($type, $config);

        try {
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
     */
    public static function getNotifyData($type, $config)
    {
        $instance = self::getInstance($type, $config);

        return $instance->getNotifyData();
    }
}