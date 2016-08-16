<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description: 支付宝相关数据的基类
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali\Data;

use Payment\Common\AliConfig;
use Payment\Common\BaseData;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;
use Payment\Utils\RsaEncrypt;

/**
 * Class BaseData
 *
 * @property string $getewayUrl  支付宝网关
 * @property string $inputCharset   参数编码字符集
 * @property string $partner  合作身份者ID，以2088开头
 * @property string $md5Key  配置的md5秘钥
 * @property string $notifyUrl  异步通知的url
 * @property string $returnUrl  同步通知的url
 * @property integer $timeExpire  订单过期时间
 * @property string $rsaPrivatePath  rsa私钥路径
 * @property string $rsaAliPubPath  rsa支付宝公钥路径
 * @property string $cacertPath  请求证书路径
 * @property string $signType  加密方式。默认md5
 * @property string $account  卖家支付宝账号，手机号或者邮箱
 * @property string $account_name  卖家支付宝昵称
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
 */
abstract class AliBaseData extends BaseData
{

    /**
     * AliBaseData constructor.
     * @param AliConfig $config
     * @param array $reqData
     * @throws PayException
     */
    public function __construct(AliConfig $config, array $reqData)
    {
        parent::__construct($config, $reqData);

        $this->signType = $config->signType;// 默认使用RSA 进行加密处理
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
        switch ($this->signType) {
            case 'MD5' :
                $signStr .= $this->md5Key;// 此处不需要通过 & 符号链接
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
}