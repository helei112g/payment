<?php
namespace Payment\Common\Weixin\Data\Charge;

use Payment\Utils\ArrayUtil;

/**
 * Class AppChargeData
 * 微信APP支付
 * @package Payment\Common\Weixin\Data\Charge
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AppChargeData extends ChargeBaseData
{
    protected function buildData()
    {
        $signData = [
            'appid' => trim($this->appId),
            'mch_id'    => trim($this->mchId),
            'device_info'   => $this->terminal_id,
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,
            'body'  => trim($this->subject),
            //'detail' => json_encode($this->body, JSON_UNESCAPED_UNICODE);
            'attach'    => trim($this->return_param),
            'out_trade_no'  => trim($this->order_no),
            'fee_type'  => $this->feeType,
            'total_fee' => $this->amount,
            'spbill_create_ip'  => trim($this->client_ip),
            'time_start'    => $this->timeStart,
            'time_expire'   => $this->timeout_express,
            //'goods_tag' => 'WXG', // 订单优惠标记
            'notify_url'    => $this->notifyUrl,
            'trade_type'    => $this->tradeType, //设置APP支付
            //'product_id' => '',// 商品id
            'limit_pay' => $this->limitPay,  // 指定不使用信用卡
            //'openid' => '用户标识',
            /*'scene_info' => \GuzzleHttp\json_encode([
                'store_info' => [
                    'id' => 'SZTX001', // 门店唯一标识
                    'name' => '腾讯大厦腾大餐厅',// 门店名称
                    'area_code' => '440305', // 门店所在地行政区划码
                    'address' => '科技园中一路腾讯大厦',// 门店详细地址
                ]
            ])*/
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}
