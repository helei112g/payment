<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:36
 * @description:
 */

namespace Payment\Refund;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\RefundData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;

class AliRefund extends AliBaseStrategy
{
    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::TRADE_REFUND_METHOD;
        return RefundData::class;
    }

    /**
     * 返回数据
     * @param array $data
     * @return array|string
     * @throws PayException
     */
    protected function retData(array $data)
    {
        $url = parent::retData($data);

        try {
            $data = $this->sendReq($url);
        } catch (PayException $e) {
            throw $e;
        }

        if ($this->config->returnRaw) {
            return $data;
        }

        return $this->createBackData($data);
    }

    /**
     * 处理返回的数据
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        // 新版本
        if ($data['code'] !== '10000') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['sub_msg']
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $data['trade_no'],
                'order_no'  => $data['out_trade_no'],
                'logon_id'   => $data['buyer_logon_id'],
                'fund_change' => $data['fund_change'],
                'refund_fee'    => $data['refund_fee'],// 返回的总金额，这里支付宝会累计
                'refund_time'=> $data['gmt_refund_pay'],
                'refund_detail_item_list' => $data['refund_detail_item_list	'],
                'store_name' => $data['store_name'],
                'buyer_id'   => $data['buyer_user_id'],
                'channel'   => Config::ALI_REFUND,

            ],
        ];

        return $retData;
    }
}
