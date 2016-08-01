<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 16/7/30
 * Time: 下午11:08
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Weixin\Data\Charge;
use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Config;

/**
 * Class ChargeBaseData
 *
 * @inheritdoc
 *
 * @property string $order_no
 * @property string $amount
 * @property string $client_ip
 * @property string $subject
 * @property string $body
 * @property string $extra_param
 * @property string $show_url
 * @property string $product_id  扫码支付时,必须设置该参数
 * @property string $openid  trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识
 *
 * @package Payment\Common\Weixin\Data\Charge
 */
abstract class ChargeBaseData extends WxBaseData
{

    /**
     * 检查传入的支付信息是否正确
     */
    protected function checkDataParam()
    {
        $orderNo = $this->order_no;
        $amount = $this->amount;
        $clientIp = $this->client_ip;
        $subject = $this->subject;
        $body = $this->body;

        // 检查订单号是否合法
        if (empty($orderNo) || mb_strlen($orderNo) > 64) {
            throw new PayException('订单号不能为空，并且长度不能超过64位');
        }

        // 检查金额不能低于0.01，不能大于 100000.00
        if (bccomp($amount, Config::PAY_MIN_FEE, 2) === -1) {
            throw new PayException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }
        if (bccomp($amount, Config::PAY_MAX_FEE, 2) === 1) {
            throw new PayException('支付金额不能大于 ' . Config::PAY_MAX_FEE . ' 元');
        }
        // 微信使用的单位位分.此处进行转化
        $this->amount = bcmul($amount, 100, 0);

        // 检查ip地址
        if (empty($clientIp) || ! preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $clientIp)) {
            throw new PayException('IP 地址必须上传，并且以IPV4的格式');
        }

        // 检查 商品名称 与 商品描述
        if (empty($subject) || empty($body)) {
            throw new PayException('必须提供商品名称与商品描述');
        }
    }
}