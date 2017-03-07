<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:25
 * @description:
 */

namespace Payment\Query\Wx;

use Payment\Common\Weixin\Data\Query\ChargeQueryData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;
use Payment\Config;
use Payment\Utils\ArrayUtil;

class WxChargeQuery extends WxBaseStrategy
{

    /**
     * 返回查询订单的数据
     * @author helei
     */
    protected function getBuildDataClass()
    {
        return ChargeQueryData::class;
    }

    /**
     * 返回微信查询的url
     * @return string
     * @author helei
     */
    protected function getReqUrl()
    {
        return WxConfig::CHARGE_QUERY_URL;
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
        // 将金额处理为元
        $totalFee = bcdiv($data['total_fee'], 100, 2);

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'amount'   => $totalFee,
                'channel'   => Config::WX_CHARGE,// 支付查询
                'order_no'   => $data['out_trade_no'],
                'buyer_id'   => $data['openid'],
                'trade_state'   => strtolower($data['trade_state']),
                'transaction_id'   => $data['transaction_id'],
                'time_end'   => date('Y-m-d H:i:s', strtotime($data['time_end'])),
                'return_param'    => $data['attach'],
                'terminal_id' => $data['device_info'],
                'trade_type' => $data['trade_type'],
                'bank_type' => $data['bank_type'],
                'trade_state_desc' => $data['trade_state_desc'],
            ],
        ];

        return ArrayUtil::paraFilter($retData);
    }
}
