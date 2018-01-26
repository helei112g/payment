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
        $info = $this->scene_info;
        $sceneInfo = [];
        if ($info && is_array($info)) {
            $sceneInfo['store_info'] = $info;
        }

        $signData = [
            'appid' => trim($this->appId),
            'mch_id'    => trim($this->mchId),
            'device_info'   => $this->terminal_id,
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,
            'body'  => trim($this->subject),
            //'detail' => json_encode($this->body, JSON_UNESCAPED_UNICODE),
            'attach'    => trim($this->return_param),
            'out_trade_no'  => trim($this->order_no),
            'fee_type'  => $this->feeType,
            'total_fee' => $this->amount,
            'spbill_create_ip'  => trim($this->client_ip),
            'time_start'    => $this->timeStart,
            'time_expire'   => $this->timeout_express,
            //'goods_tag' => '订单优惠标记',
            'notify_url'    => $this->notifyUrl,
            'trade_type'    => $this->tradeType, //设置APP支付
            //'product_id' => '商品id',
            'limit_pay' => $this->limitPay,  // 指定不使用信用卡
            //'openid' => '用户标识',
            'scene_info' => $sceneInfo ? json_encode($sceneInfo, JSON_UNESCAPED_UNICODE) : '',

            // 服务商
            'sub_appid' => $this->sub_appid,
            'sub_mch_id' => $this->sub_mch_id,
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}
