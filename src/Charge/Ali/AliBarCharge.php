<?php
namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\BarChargeData;
use Payment\Common\PayException;

/**
 * 商户扫用户的二维码
 *
 * Class AliBarCharge
 * @package Payment\Charge\Ali
 *
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AliBarCharge extends AliBaseStrategy
{
    // app 支付接口名称
    protected $method = 'alipay.trade.pay';

    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
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
        $reqData = parent::retData($ret);

        // 发起网络请求
        try {
            $data = $this->sendReq($reqData);
        } catch (PayException $e) {
            throw $e;
        }

        // 检查是否报错
        if ($data['code'] !== '10000') {
            new PayException($data['sub_msg']);
        }

        return $data;
    }
}
