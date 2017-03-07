<?php
/**
 * @author: helei
 * @createTime: 2016-08-02 17:55
 * @description:
 */

namespace Payment\Query\Wx;

use Payment\Common\Weixin\Data\Query\RefundQueryData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;

/**
 *
 * 微信退款订单查询
 * Class WxRefudnQuery
 * @package Payment\Query
 * anthor helei
 */
class WxRefundQuery extends WxBaseStrategy
{
    protected function getBuildDataClass()
    {
        return RefundQueryData::class;
    }

    protected function getReqUrl()
    {
        return WxConfig::REFUDN_QUERY_URL;// 查询退款url
    }

    /**
     * 处理通知的返回数据
     * @param array $data
     * @return mixed
     * @author helei
     */
    protected function retData(array $data)
    {
        if ($this->config->returnRaw) {
            return $data;
        }

        // 请求失败，可能是网络
        if ($data['return_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['return_msg']
            ];
        }

        // 业务失败
        if ($data['result_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['err_code_des']
            ];
        }

        // 正确
        return $this->createBackData($data);
    }

    /**
     * 返回数据给客户端
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        $refund_count = $data['refund_count'];// 退款的笔数

        // 将金额处理为元
        $totalFee = bcdiv($data['total_fee'], 100, 2);
        $refundFee = bcdiv($data['refund_fee'], 100, 2);

        // 获取退款笔数
        $refundData = [];
        for ($i = 0; $i<$refund_count; $i++) {
            $refund_no = 'out_refund_no_' . $i;// 商户退款单号
            $refund_id = 'refund_id_' . $i;// 微信退款单号
            $refund_channel = 'refund_channel_' . $i;// 退款渠道
            $refund_fee = 'refund_fee_' . $i;// 申请退款金额
            //$settlement_refund_fee = 'settlement_refund_fee_' . $i;// 实际退款金额
            $refund_status = 'refund_status_' . $i;// 退款状态
            $recv_accout = 'refund_recv_accout_' . $i;// 退款入账账户

            $fee = bcdiv($data['refund_fee'], 100, 2);

            // 一笔订单可能被分为多笔,进行退款
            $refundData[] = [
                'refund_no' => $data[$refund_no],
                'refund_id' => $data[$refund_id],
                'refund_channel'    => $data[$refund_channel],
                'refund_fee'    => $fee,
                //'settlement_refund_fee' => $data[$settlement_refund_fee],
                'refund_status' => strtolower($data[$refund_status]),
                'recv_accout'   => $data[$recv_accout],
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'amount'   => $totalFee,// 订单总金额
                'order_no'   => $data['out_trade_no'],// 商户订单号
                'transaction_id'   => $data['transaction_id'],// 微信订单号
                'refund_count' => $data['refund_count'],// 退款总笔数
                'refund_fee' => $refundFee,// 退款总金额
                'refund_data'   => $refundData,// 退款信息
            ],
        ];

        return $retData;
    }
}
