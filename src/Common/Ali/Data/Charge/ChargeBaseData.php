<?php
/**
 * @author: helei
 * @createTime: 2016-07-20 14:33
 * @description:
 */

namespace Payment\Common\Ali\Data\Charge;


use Payment\Common\Ali\Data\BaseData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\RsaEncrypt;

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
 *
 * @package Payment\Common\Ali\Data\Charge
 * anthor helei
 */
abstract class ChargeBaseData extends BaseData
{
    protected $sign_type;

    public function __construct(AliConfig $config, array $reqData)
    {
        parent::__construct($config, $reqData);

        $this->checkPayDataParam();

        $this->sign_type = 'MD5';// 默认使用MD5 进行加密处理
    }

    /**
     * 签名算法实现
     * @param string $signStr
     * @return string
     * @author helei
     */
    protected function makeSign($signStr)
    {
        $sign = '';
        switch ($this->sign_type) {
            case 'MD5' :
                $signStr .= $this->md5Key;
                $sign = md5($signStr);
                break;
            case 'RSA' :
                $rsa_private_key = @file_get_contents($this->rsaPrivatePath);
                $rsa = new RsaEncrypt($rsa_private_key);

                $sign = $rsa->encrypt($signStr);
                break;
            default :
                $sign = '';
        }

        return $sign;
    }

    /**
     * 检查传入的支付参数是否正确
     *
     * 如果输入参数不符合规范，直接抛出异常
     *
     * @author helei
     */
    protected function checkPayDataParam()
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