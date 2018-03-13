<?php
namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\QueryContext;

/**
 * @author: helei
 * @createTime: 2017-09-02 18:20
 * @description: 查询的客户端类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class Query
 * @package Payment\Client
 */
class Query
{
    protected static $supportType = [
        Config::ALI_CHARGE,
        Config::ALI_REFUND,
        Config::ALI_TRANSFER,
        Config::ALI_RED,

        Config::WX_CHARGE,
        Config::WX_REFUND,
        Config::WX_RED,
        Config::WX_TRANSFER,

        Config::CMB_CHARGE,
        Config::CMB_REFUND,
    ];

    /**
     * 查询实例
     * @var QueryContext
     */
    protected static $instance;

    protected static function getInstance($queryType, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new QueryContext();
        }

        try {
            static::$instance->initQuery($queryType, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

    /**
     * @param string $queryType
     * @param array $config
     * @param array $metadata
     * @return array
     * @throws PayException
     */
    public static function run($queryType, $config, $metadata)
    {
        if (! in_array($queryType, self::$supportType)) {
            throw new PayException('sdk当前不支持该类型查询，当前仅支持：' . implode(',', self::$supportType) . __LINE__);
        }

        try {
            $instance = self::getInstance($queryType, $config);

            $ret = $instance->query($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}
