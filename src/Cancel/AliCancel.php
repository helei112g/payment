<?php

namespace Payment\Cancel;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\CancelData;
use Payment\Common\PayException;
use Payment\Config;

/**
 * 取消订单业务
 * Class AliRefund
 * @package Payment\Refund
 */
class AliCancel extends AliBaseStrategy
{
    protected $method = 'alipay.trade.cancel';

    /**
     * 返回取消数据构造类
     * @return string
     */
    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        return CancelData::class;
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
            $ret['channel'] = Config::ALI_CANCEL;
            $ret['refund_no'] = $refundNo;
            return $ret;
        }

        if ($ret['code'] !== '10000') {


            return [
                'is_success' => 'F',
                'error' => $ret['sub_msg'],
                'code' => $ret['code']
            ];
        }


        $retData = [
            'is_success' => 'T',
            'response' => [
                'transaction_id' => $ret['trade_no'],
                'order_no' => $ret['out_trade_no'],
                'action' => $ret['action'],
                'retry_flag' => $ret['retry_flag']
            ],
        ];

        return $retData;
    }
}
