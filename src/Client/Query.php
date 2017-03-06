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

    const NAME_SELF_QUERY = 'self';// 自己生成的单号（交易单号，退款单号，转账单号等）

    const NAME_THIRD_QUERY = 'third';// 第三方返回的单号

    /**
     * @param string $queryType
     * @param array $config
     * @param string $queryName 查询的字段名称，可取值如下：
     *  - 第三方（微信、支付宝）订单号  third
     *  - 商户自己的订单号  self
     *
     * @param string $queryValue
     * @return array
     * @throws PayException
     */
    public static function run($queryType, $config, $queryName, $queryValue)
    {
        if (! in_array($queryType, self::$supportType)) {
            throw new PayException('sdk当前不支持该类型查询，当前仅支持：' . implode(',', self::$supportType));
        }

        if (! in_array($queryName, [self::NAME_SELF_QUERY, self::NAME_THIRD_QUERY])) {
            throw new PayException('当前可通过商户自身生成的订单号或者第三方返回的交易号进行查询');
        }

        $query = new QueryContext();

        try {
            $metadata = self::getQueryData($queryType, $queryName, $queryValue);

            $query->initQuery($queryType, $config);

            $ret = $query->query($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }

    /**
     * 返回查询的数组结构
     * @param $queryType
     * @param $queryName
     * @param $queryValue
     *
     * @return array
     */
    protected static function getQueryData($queryType, $queryName, $queryValue)
    {
        if ($queryName === self::NAME_SELF_QUERY) {
            if (in_array($queryType, [Config::WX_CHARGE, Config::ALI_CHARGE])) {
                return ['out_trade_no' => $queryValue];
            }
        } else {
            if ($queryType === Config::WX_CHARGE) {
                return ['transaction_id' => $queryValue];
            } elseif ($queryType === Config::ALI_CHARGE) {
                return ['trade_no' => $queryValue];
            }
        }

    }
}