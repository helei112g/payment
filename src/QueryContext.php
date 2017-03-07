<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:24
 * @description:
 */

namespace Payment;

use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Query\Ali\AliChargeQuery;
use Payment\Query\Ali\AliRefundQuery;
use Payment\Query\Ali\AliTransferQuery;
use Payment\Query\Wx\WxChargeQuery;
use Payment\Query\Wx\WxRefundQuery;
use Payment\Query\Wx\WxTransferQuery;

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
        try {
            switch ($channel) {
                case Config::ALI_CHARGE:
                    $this->query = new AliChargeQuery($config);
                    break;
                case Config::ALI_REFUND:// 支付宝退款订单查询
                    $this->query = new AliRefundQuery($config);
                    break;
                case Config::ALI_TRANSFER:
                    $this->query = new AliTransferQuery($config);
                    break;

                case Config::WX_CHARGE:// 微信支付订单查询
                    $this->query = new WxChargeQuery($config);
                    break;
                case Config::WX_REFUND:// 微信退款订单查询
                    $this->query = new WxRefundQuery($config);
                    break;
                case Config::WX_TRANSFER:// 微信转款订单查询
                    $this->query = new WxTransferQuery($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI_CHARGE ALI_REFUND WX_CHARGE WX_REFUND WX_TRANSFER');
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
