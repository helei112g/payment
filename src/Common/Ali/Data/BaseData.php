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
abstract class BaseData
{
    /**
     * 支付的请求数据
     * @var array $data
     */
    protected $data;

    /**
     * 支付返回的数据
     * @var array $retData
     */
    protected $retData;


    public function __construct(AliConfig $config, array $reqData)
    {
        $this->data = array_merge($reqData, $config->toArray());

        try {
            $this->checkDataParam();
        } catch (PayException $e) {
            throw $e;
        }

        $this->signType = 'RSA';// 默认使用RSA 进行加密处理
    }

    /**
     * 获取变量，通过魔术方法
     * @param string $name
     * @return null|string
     * @author helei
     */
    protected function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * 设置变量
     * @param $name
     * @param $value
     * @author helei
     */
    protected function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 设置签名
     * @author helei
     */
    public function setSign()
    {
        $this->buildData();

        $values = ArrayUtil::removeKeys($this->retData, ['sign', 'sign_type']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $this->retData['sign'] = $this->makeSign($signStr);
    }

    /**
     * 返回处理之后的数据
     * @return array
     * @author helei
     */
    public function getData()
    {
        return $this->retData;
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

    /**
     * 设置支付相关参数，  该接口本可在此进行抽象，但为了便于后期维护，此处全部延迟到子类处理
     * @return mixed
     * @author helei
     */
    abstract protected function buildData();

    /**
     * 检查参数是否正确 错误以PayException返回
     * @return mixed
     * @throws PayException
     * @author helei
     */
    abstract protected function checkDataParam();
}