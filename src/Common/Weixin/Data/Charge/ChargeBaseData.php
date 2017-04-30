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
 * @property string $client_ip  用户端实际ip
 * @property string $subject  商品详情  商品详细列表，使用Json格式，传输签名前请务必使用CDATA标签将JSON文本串保护起来。  暂时未使用
 * @property string $body
 * @property string $return_param  附加数据，在查询API和支付通知中原样返回
 * @property integer $timeout_express  订单失效时间   格式为yyyyMMddHHmmss
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
        $subject = $this->subject;
        $body = $this->body;
        $deviceInfo = $this->terminal_id;

        // 检查订单号是否合法
        if (empty($orderNo) || mb_strlen($orderNo) > 64) {
            throw new PayException('订单号不能为空，并且长度不能超过64位');
        }

        // 检查金额不能低于0.01
        if (bccomp($amount, Config::PAY_MIN_FEE, 2) === -1) {
            throw new PayException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }

        // 检查 商品名称 与 商品描述
        if (empty($subject) || empty($body)) {
            throw new PayException('必须提供商品名称与商品详情');
        }

        // 初始 微信订单过期时间，最短失效时间间隔必须大于5分钟
        if ($this->timeout_express - strtotime($this->timeStart) < 5) {
            throw new PayException('必须设置订单过期时间,且需要大于5分钟.如果不正确请检查是否正确设置时区');
        } else {
            $this->timeout_express = date('YmdHis', $this->timeout_express);
        }

        // 微信使用的单位位分.此处进行转化
        $this->amount = bcmul($amount, 100, 0);

        // 设置ip地址
        $clientIp = $this->client_ip;
        if (empty($clientIp)) {
            $this->client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        }

        // 设置设备号
        if (empty($deviceInfo)) {
            $this->terminal_id = 'WEB';
        }
    }
}
