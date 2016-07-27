<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:28
 * @description: 支付宝批量付款接口
 */

namespace Payment\Trans;


use Payment\Common\Ali\Data\TransData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;

class AliTransfer implements TransferStrategy
{

    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * AliCharge constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
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
     * @return string
     * @author helei
     * @throws PayException
     */
    public function handle(array $data)
    {
        try {
            $ret = new TransData($this->config, $data);
        } catch (PayException $e) {
            throw $e;
        }

        $ret->setSign();
        
        $retData = $this->config->getewayUrl . http_build_query($ret->getData());

        return $retData;
    }
}