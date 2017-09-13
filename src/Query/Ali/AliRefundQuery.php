<?php
namespace Payment\Query\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Query\RefundQueryData;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 *
 * 支付宝退款订单查询
 * Class AliRefundQuery
 * @package Payment\Query
 * anthor helei
 */
class AliRefundQuery extends AliBaseStrategy
{
    protected $method = 'alipay.trade.fastpay.refund.query';

    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        return RefundQueryData::class;
    }

    protected function retData(array $data)
    {
        $reqData = parent::retData($data);

        try {
            $ret = $this->sendReq($reqData);
        } catch (PayException $e) {
            throw $e;
        }

        if ($this->config->returnRaw) {
            $ret['channel'] = Config::ALI_REFUND;
            return $ret;
        }


        return $this->createBackData($ret);
    }

    /**
     * 返回数据给客户端  未完成，目前没有数据提供
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        if ($data['code'] !== '10000') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['sub_msg'],
                'channel'   => Config::ALI_REFUND,
            ];
        }

        // 这里有个诡异情况。查询数据全部为空。仅返回一个成功的标记
        if (empty($data['out_trade_no'])) {
            return [
                'is_success'    => 'T',
                'msg'   => strtolower($data['msg']),
                'channel'   => Config::ALI_REFUND,
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => ArrayUtil::get($data, 'trade_no'),// 支付宝订单号
                'order_no'   => ArrayUtil::get($data, 'out_trade_no'),// 商户订单号
                'refund_no' => ArrayUtil::get($data, 'out_request_no'),// 本笔退款对应的退款请求号
                'reason'   => ArrayUtil::get($data, 'refund_reason'),// 退款理由
                'amount'   => ArrayUtil::get($data, 'total_amount'),// 订单总金额
                'refund_amount'   => ArrayUtil::get($data, 'refund_amount'),// 退款金额
                'channel'   => Config::ALI_REFUND,
            ]
        ];

        return $retData;
    }
}
