<?php
namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\HelperContext;

/**
 * @author: helei
 * @createTime: 2017-09-02 18:20
 * @description: 招商银行的辅助类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
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
     * 辅助类实例
     * @var HelperContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new HelperContext();
        }

        try {
            static::$instance->initHelper($channel, $config);
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
