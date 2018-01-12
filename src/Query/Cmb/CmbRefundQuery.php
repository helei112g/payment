<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午5:22
 */

namespace Payment\Query\Cmb;

use Payment\Common\Cmb\CmbBaseStrategy;
use Payment\Common\Cmb\Data\Query\RefundQueryData;
use Payment\Common\CmbConfig;
use Payment\Config;

class CmbRefundQuery extends CmbBaseStrategy
{
    public function getBuildDataClass()
    {
        $this->config->getewayUrl = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?QuerySettledRefund';
        if ($this->config->useSandbox) {// 测试
            $this->config->getewayUrl = 'http://121.15.180.66:801/netpayment_dl/BaseHttp.dll?QuerySettledRefund';
        }

        return RefundQueryData::class;
    }

    protected function retData(array $ret)
    {
        $json = json_encode($ret, JSON_UNESCAPED_UNICODE);

        $postData = CmbConfig::REQ_FILED_NAME . '=' . $json;
        $retData = $this->sendReq($postData);

        if ($this->config->returnRaw) {
            $retData['channel'] = Config::CMB_REFUND;
            return $retData;
        }

        $list = $retData['dataList'];
        $list = str_replace('`', '', $list);
        $list = explode(PHP_EOL, $list);

        $header = array_shift($list);
        $header = explode(',', $header);

        foreach ($list as $key => $item) {
            $item = explode(',', $item);

            $list[$key] = array_combine($header, $item);
        }

        // 正确情况
        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'channel' => Config::CMB_REFUND,
                'refund_data' => $list,
            ],
        ];

        return $retData;
    }
}
