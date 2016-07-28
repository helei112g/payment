<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 17:42
 * @description: 退款统一接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment;


use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Refund\AliRefund;
use Payment\Refund\WxRefund;

class RefundContext
{
    /**
     * 退款的渠道
     * @var BaseStrategy
     */
    protected $refund;


    /**
     * 设置对应的退款渠道
     * @param string $channel 退款渠道
     *  - @see Config
     * 
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initRefund($channel, array $config)
    {
        try{
            switch ($channel) {
                case Config::ALI:
                    $this->refund = new AliRefund($config);
                    break;
                case Config::WEIXIN:
                    $this->refund = new WxRefund($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI WEIXIN两个常量');
            }
        } catch (PayException $e) {
            throw $e;
        }

    }

    /**
     * 通过环境类调用支付退款操作
     *
     * @param array $data
     *
     *      $data['refund_no'] = '',  退款单号，在系统内部唯一
     *      $data['refund_data'][] => [
     *          'transaction_id'    => '原付款支付宝交易号',
     *          'refund_fee' => '退款总金额', // 单位元
     *          'reason'     => '退款理由', // “退款理由”中不能有“^”、“|”、“$”、“#”
     *      ];// 如果有多笔数据， refund_data 就写入多个数据集
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function refund(array $data)
    {
        if (! $this->refund instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->refund->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}