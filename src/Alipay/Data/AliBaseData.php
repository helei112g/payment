<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 19:56
 * @description:
 */

namespace Payment\Alipay\Data;



use Payment\Alipay\AlipayConfig;
use Payment\Contracts\BaseData;
use Payment\Utils\ArrayUtil;

abstract class AliBaseData extends BaseData
{
    // 保存数据信息
    protected $values = array();

    // 支付宝的配置信息
    protected $config;

    public function __construct()
    {
        $this->config = new AlipayConfig();
    }

    /**
     * 设置签名
     * @author helei
     */
    public function setSign()
    {
        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($this->values, ['sign', 'sign_type']);

        //  2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);

        // 3. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($values);

        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $prestr = ArrayUtil::createLinkstring($values);

        $sign = $this->makeSign($prestr);
        $this->values['sign'] = $sign;
        return $sign;
    }

    /**
     * 获取签名
     * @return mixed
     * @author helei
     */
    public function getSign()
    {
        return $this->values['sign'];
    }

    /**
     * 返回参数信息
     * @return array
     * @author helei
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * 对传入的参数，进行签名。
     *  具体实现方式在子类中进行
     * @return string
     * @author helei
     */
    abstract protected function makeSign($prestr);

    /**
     * 设置编码方式
     * @param string $input_chart
     * @author helei
     */
    public function setInputChart($input_chart)
    {
        $this->values['_input_charset'] = $input_chart;
    }

    /**
     * 获取当前字符的编码方式
     * @return mixed|null
     * @author helei
     */
    public function getInputChart()
    {
        if (array_key_exists('_input_charset', $this->values)) {
            return $this->values['_input_charset'];
        }

        return null;
    }

    /**
     * 接口名称
     * @param string $service
     * @author helei
     */
    public function setService($service)
    {
        $this->values['service'] = $service;
    }

    /**
     * 获取接口名称
     * @return string|null
     * @author helei
     */
    public function getService()
    {
        if (array_key_exists('service', $this->values)) {
            return $this->values['service'];
        }

        return null;
    }

    /**
     * 设置合作者身份ID
     * @param string $partner
     * @author helei
     */
    public function setPartner($partner)
    {
        $this->values['partner'] = $partner;
    }

    /**
     * 获取接口名称
     * @return string|null
     * @author helei
     */
    public function getPartner()
    {
        if (array_key_exists('partner', $this->values)) {
            return $this->values['partner'];
        }

        return null;
    }

    /**
     * 设置签名方式
     * @param string $signType
     * @author helei
     */
    public function setSignType($signType)
    {
        $this->values['sign_type'] = $signType;
    }

    /**
     * 获取签名方式
     * @return string|null
     * @author helei
     */
    public function getSignType()
    {
        if (array_key_exists('sign_type', $this->values)) {
            return $this->values['sign_type'];
        }

        return null;
    }

    /**
     * 设置 服务器异步通知页面路径
     * @param string $notify_url
     * @author helei
     */
    public function setNotifyUrl($notify_url)
    {
        $this->values['notify_url'] = $notify_url;
    }

    /**
     * 获取 服务器异步通知页面路径
     * @return mixed|null
     * @author helei
     */
    public function getNotifyUrl()
    {
        if (array_key_exists('notify_url', $this->values)) {
            return $this->values['notify_url'];
        }

        return null;
    }

    /**
     * 页面跳转同步通知页面路径
     * @param string $return_url
     * @author helei
     */
    public function setReturnUrl($return_url)
    {
        $this->values['return_url'] = $return_url;
    }

    /**
     * 获取 页面跳转同步通知页面路径
     * @return mixed|null
     * @author helei
     */
    public function getReturnUrl()
    {
        if (array_key_exists('return_url', $this->values)) {
            return $this->values['return_url'];
        }

        return null;
    }
}