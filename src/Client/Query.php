<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/6
 * Time: 下午9:08
 */

namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\QueryContext;

/**
 * 查询的客户端类
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
    ];

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
            throw new PayException('sdk当前不支持该类型查询，当前仅支持：' . implode(',', self::$supportType));
        }

        $query = new QueryContext();

        try {
            $query->initQuery($queryType, $config);

            $ret = $query->query($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}