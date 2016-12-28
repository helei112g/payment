<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:40
 * @description:
 */

namespace Payment;


use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Trans\AliTransfer;
use Payment\Trans\WxTransfer;

class TransferContext
{
    /**
     * 转款渠道
     * @var BaseStrategy
     */
    protected $transfer;

    /**
     * 设置对应的退款渠道
     * @param string $channel 退款渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initTransfer($channel, array $config)
    {
        try{
            switch ($channel) {
                case Config::ALI:
                    $this->transfer = new AliTransfer($config);
                    break;
                case Config::WEIXIN:
                    $this->transfer = new WxTransfer($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI WEIXIN两个常量');
            }
        } catch (PayException $e) {
            throw $e;
        }

    }

    /**
     * 通过环境类调用支付转款操作
     *
     * @param array $data
     *
     * $data['trans_no']    = '';// 转款单号
     * $data['trans_data'][] = [
     *      'serial_no' => '流水号',
     *      'user_account'   => '收款账号',
     *      'user_name'     => '收款人姓名',
     *      'trans_fee'       => '付款金额',
     *      'desc'      => '付款备注说明',
     *  ];
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function transfer(array $data)
    {
        if (! $this->transfer instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->transfer->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}