<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:28
 * @description: 支付宝批量付款接口
 */

namespace Payment\Trans;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\TransData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;

class AliTransfer extends AliBaseStrategy
{
    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::TRANS_TOACCOUNT_METHOD;
        return TransData::class;
    }

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
                'transaction_id'   => $data['order_id'],
                'trans_no'  => $data['out_biz_no'],
                'pay_date'   => $data['pay_date'],
                'channel'   => Config::ALI_TRANSFER,
            ],
        ];

        return $retData;
    }
}
