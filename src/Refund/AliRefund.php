<?php
namespace Payment\Refund;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\RefundData;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * 退款业务
 * Class AliRefund
 * @package Payment\Refund
 */
class AliRefund extends AliBaseStrategy
{
    protected $method = 'alipay.trade.refund';

    /**
     * 返回退款数据构造类
     * @return string
     */
    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
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
        $reqData = parent::retData($data);

        try {
            $ret = $this->sendReq($reqData);
        } catch (PayException $e) {
            throw $e;
        }
        $content = \GuzzleHttp\json_decode($data['biz_content'], true);
        $refundNo = $content['out_request_no'];

        if ($this->config->returnRaw) {
            $ret['channel'] = Config::ALI_REFUND;
            $ret['refund_no'] = $refundNo;
            return $ret;
        }

        if ($ret['code'] !== '10000') {
            return [
                'is_success'    => 'F',
                'error' => $ret['sub_msg'],
                'refund_no' => $refundNo
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $ret['trade_no'],
                'order_no'  => $ret['out_trade_no'],
                'logon_id'   => $ret['buyer_logon_id'],
                'fund_change' => $ret['fund_change'],// 本次退款是否发生了资金变化
                'refund_fee'    => $ret['refund_fee'],// 返回的总金额，这里支付宝会累计
                'refund_time' => $ret['gmt_refund_pay'],
                'refund_detail_item_list' => ArrayUtil::get($ret, 'refund_detail_item_list'),// 退款使用的资金渠道
                'refund_no' => $refundNo,
                'channel'   => Config::ALI_REFUND,
                'buyer_id'   => $ret['buyer_user_id'],
                'store_name' => ArrayUtil::get($ret, 'store_name'),
            ],
        ];

        return $retData;
    }
}
