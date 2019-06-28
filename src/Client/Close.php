<?php
/**
 * Created by PhpStorm.
 * User: biker
 * Date: 2019/6/28
 * Time: 17:39
 */

namespace Payment\Client;
# by biker 2019/6/28 17:39

use Payment\CloseContext;
use Payment\Common\PayException;
use Payment\Config;

class Close{
    /**
     * 支持的渠道
     * @var array
     */
    private static $supportChannel = [
        Config::ALI_CLOSE,// 支付宝
        Config::WX_CLOSE,// 微信
    ];

    /**
     * 交易关闭
     * @var CloseContext
     */
    protected static $instance;

    /**
     * BY biker
     * @param $channel
     * @param $config
     * @return CloseContext
     * @throws PayException
     */
    protected static function getInstance($channel, $config){
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new CloseContext();
        }

        try {
            static::$instance->initClose($channel, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

    /**
     * BY biker
     * @param $channel
     * @param $config
     * @param $closeData
     * @return mixed
     * @throws PayException
     */
    public static function run($channel, $config, $closeData){
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该交易关闭渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }
        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->close($closeData);
        } catch (PayException $e) {
            throw $e;
        }
        return $ret;
    }
}