<?php
namespace Payment\Query\Cmb;


use Payment\Common\Cmb\CmbBaseStrategy;
use Payment\Common\Cmb\Data\Query\ChargeQueryData;
use Payment\Common\CmbConfig;
use Payment\Config;

/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/28
 * Time: 下午12:51
 */
class CmbChargeQuery extends CmbBaseStrategy
{

    public function getBuildDataClass()
    {
        $this->config->getewayUrl = 'https://payment.ebank.cmbchina.com/NetPayment/BaseHttp.dll?QuerySingleOrder';
        if ($this->config->useSandbox) {// 测试
            $this->config->getewayUrl = 'http://121.15.180.66:801/NetPayment_dl/BaseHttp.dll?QuerySingleOrder';
        }

        return ChargeQueryData::class;
    }

    protected function retData(array $ret)
    {
        $json = json_encode($ret, JSON_UNESCAPED_UNICODE);

        $postData = CmbConfig::REQ_FILED_NAME . '=' . $json;
        $ret = $this->sendReq($postData);

        if ($this->config->returnRaw) {
            $ret['channel'] = Config::CMB_CHARGE;
            return $ret;
        }

        // 正确情况
        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'amount'   => $ret['orderAmount'],
                'channel'   => Config::CMB_CHARGE,
                'order_no'   => $ret['orderNo'],
                'trade_state'   => $this->getTradeStatus($ret['orderStatus']),
                'transaction_id'   => $ret['bankSerialNo'],
                'time_end'   => date('Y-m-d H:i:s', strtotime($ret['settleDate'] . $ret['settleTime'])),// Y-m-d H:i:s
                'fee' => $ret['fee'],// 招商抽取的金额
                'time_start' => date('Y-m-d H:i:s', strtotime($ret['bankDate'] . $ret['bankTime'])),
                'discount_fee' => $ret['discountAmount'],// 优惠金额,格式：xxxx.xx  无优惠时返回0.00
                'card_type' => $ret['cardType'],// 卡类型,02：一卡通；03：信用卡；07：他行卡
                'return_param' => $ret['merchantPara'],
            ],
        ];

        return $retData;
    }
}