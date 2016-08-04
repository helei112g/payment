<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:24
 * @description:
 */

namespace Payment;


use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Query\AliTradeQuery;
use Payment\Query\WxRefundQuery;
use Payment\Query\WxTradeQuery;

class QueryContext
{
    /**
     * 查询的渠道
     * @var BaseStrategy
     */
    protected $query;


    /**
     * 设置对应的查询渠道
     * @param string $channel 查询渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initQuery($channel, array $config)
    {
        try{
            switch ($channel) {
                case Config::ALI:
                    $this->query = new AliTradeQuery($config);
                    break;
                case Config::WEIXIN:
                    $this->query = new WxTradeQuery($config);
                    break;
                case Config::WEIXIN_REFUND:
                    $this->query = new WxRefundQuery($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI WEIXIN WEIXIN_REFUND');
            }
        } catch (PayException $e) {
            throw $e;
        }

    }

    /**
     * 通过环境类调用支付异步通知
     *
     * @param array $data
     *      // 二者设置一个即可
     *      $data => [
     *          'transaction_id'    => '原付款支付宝交易号',
     *          'order_no' => '商户订单号',
     *      ];
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function query(array $data)
    {
        if (! $this->query instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->query->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}