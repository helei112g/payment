<?php
namespace Payment\Refund;

use Payment\Common\Weixin\Data\RefundData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Config;

/**
 * Class WxRefund
 * 微信退款操作
 * @package Payment\Refund
 * anthor helei
 */
class WxRefund extends WxBaseStrategy
{
    protected $reqUrl = 'https://api.mch.weixin.qq.com/{debug}/secapi/pay/refund';

    public function getBuildDataClass()
    {
        return RefundData::class;
    }

    /**
     * 处理退款的返回数据
     * @param array $ret
     * @return mixed
     * @author helei
     */
    protected function retData(array $ret)
    {
        if ($this->config->returnRaw) {
            $ret['channel'] = Config::WX_REFUND;
            return $ret;
        }

        // 请求失败，可能是网络
        if ($ret['return_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['return_msg']
            ];
        }

        // 业务失败
        if ($ret['result_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['err_code_des']
            ];
        }

        return $this->createBackData($ret);
    }

    /**
     * 处理返回的数据
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        // 将订单总金额金额处理为元
        $total_fee = bcdiv($data['total_fee'], 100, 2);
        // 将订单退款金额处理为元
        $refund_fee = bcdiv($data['refund_fee'], 100, 2);

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $data['transaction_id'],
                'order_no'  => $data['out_trade_no'],
                'refund_no' => $data['out_refund_no'],
                'refund_id' => $data['refund_id'],
                'refund_fee'    => $refund_fee,
                'refund_channel' => $data['refund_channel'],
                'amount'   => $total_fee,
                'channel'   => Config::WX_REFUND,

                'coupon_refund_fee' => bcdiv($data['coupon_refund_fee'], 100, 2),
                'coupon_refund_count' => $data['coupon_refund_count'],
                'cash_fee' => bcdiv($data['cash_fee'], 100, 2),
                'cash_refund_fee' => bcdiv($data['cash_refund_fee'], 100, 2),
            ],
        ];

        return $retData;
    }
}
