<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 18:05
 * @description: 微信支付相关接口的数据基类
 */

namespace Payment\Common\Weixin\Data;

use Payment\Common\PayException;
use Payment\Common\WxConfig;
use Payment\Utils\ArrayUtil;

/**
 * Class BaseData
 *
 * @property string $getewayUrl  微信支付的网关
 * @property string $appId   微信分配的公众账号ID
 * @property string $mchId  微信支付分配的商户号
 * @property string $nonceStr  随机字符串，不长于32位
 * @property string $notifyUrl  异步通知的url
 * @property string $feeType  符合ISO 4217标准的三位字母代码 默认位人民币
 * @property integer $timeExpire  订单过期时间  格式为yyyyMMddHHmmss 与开始时间必须大于等于5分钟
 * @property string $timeStart  交易开始时间 格式为yyyyMMddHHmmss
 * @property string $md5Key  用于加密的md5Key
 * @property string $signType  加密方式。默认md5
 *
 * @package Payment\Common\Weixin\Dataa
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

    /**
     * BaseData constructor.
     * @param WxConfig $wxConfig
     * @param array $reqData
     * @throws PayException
     */
    public function __construct(WxConfig $wxConfig, array $reqData)
    {
        $this->data = array_merge($reqData, $wxConfig->toArray());

        try {
            $this->checkDataParam();
        } catch (PayException $e) {
            throw $e;
        }

        $this->signType = 'MD5';// 默认使用RSA 进行加密处理
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
     * 签名算法实现  便于后期扩展微信不同的加密方式
     * @param string $signStr
     * @return string
     */
    protected function makeSign($signStr)
    {
        $sign = '';
        switch ($this->signType) {
            case 'MD5':
                $signStr .= $this->md5Key;
                $sign = md5($signStr);
                break;
            default :
                $sign = '';
        }

        return strtoupper($sign);
    }

    /**
     * 构建用于支付的签名相关数据
     * @return array
     */
    abstract protected function buildData();

    /**
     * 检查传入的参数. $reqData是否正确.
     * @return mixed
     * @throws PayException
     */
    abstract protected function checkDataParam();
}