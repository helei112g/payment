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
    public function getBuildDataClass()
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
            $rsqData = $this->sendReq($url);
        } catch (PayException $e) {
            throw $e;
        }
        $content = json_decode($data['biz_content'], true);
        $refundNo = $content['out_request_no'];

        if ($this->config->returnRaw) {
            $rsqData['channel'] = Config::ALI_REFUND;
            return $rsqData;
        }

        if ($rsqData['code'] !== '10000') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $rsqData['sub_msg']
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $rsqData['trade_no'],
                'order_no'  => $rsqData['out_trade_no'],
                'logon_id'   => $rsqData['buyer_logon_id'],
                'buyer_id'   => $rsqData['buyer_user_id'],
                'refund_no' => $refundNo,

                'fund_change' => $rsqData['fund_change'],// 本次退款是否发生了资金变化
                'refund_fee'    => $rsqData['refund_fee'],// 返回的总金额，这里支付宝会累计
                'refund_time'=> $rsqData['gmt_refund_pay'],

                'channel'   => Config::ALI_REFUND,
            ],
        ];

        return $retData;
    }
}
