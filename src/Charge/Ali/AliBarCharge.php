<?php

namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\BarChargeData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;

/**
 * 商户扫用户的二维码
 *
 * Class AliBarCharge
 * @package Payment\Charge\Weixin
 *
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */
class AliBarCharge extends AliBaseStrategy
{
    public function getBuildDataClass()
    {
        $this->config->method = AliConfig::BAR_PAY_METHOD;
        return BarChargeData::class;
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     * $data = [
            'code' => 10000,
            'msg' => 'Success',
            'buyer_logon_id' => 'day***@gmail.com',
            'buyer_pay_amount' => '0.01',
            'buyer_user_id' => '2088002162809334',
            'fund_bill_list' => [
            ['amount' => '0.01', 'fund_channel' => 'ALIPAYACCOUNT'],
            ],
            'gmt_payment' => '2017-03-05 22:27:46',
            'open_id' => '20880008025007264081318860117433',
            'out_trade_no' => '14887240631516',
            'point_amount' => '0.00',
            'receipt_amount' => '0.01',
            'total_amount' =>  '0.01',
            'trade_no' =>  '2017030521001004330274482163',
        ];
     *
     * @throws PayException
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        $url = parent::retData($ret);

        // 发起网络请求
        try {
            $data = $this->sendReq($url);
        } catch (PayException $e) {
            throw $e;
        }

        return $data;
    }
}
