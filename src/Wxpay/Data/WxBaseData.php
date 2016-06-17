<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 11:29
 * @description:
 */

namespace Payment\Wxpay\Data;



use Payment\Contracts\BaseData;
use Payment\Utils\ArrayUtil;
use Payment\Wxpay\WxConfig;

class WxBaseData extends BaseData
{
    protected $values = array();

    // 微信的配置信息
    protected $config;

    public function __construct()
    {
        $this->config = new WxConfig();
    }

    /**
     * 设置签名，详见签名生成算法
     * @return string
     **/
    public function setSign()
    {
        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($this->values, 'sign');

        //2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);

        //3. 按字典序排序参数
        $values = ArrayUtil::arraySort($values);

        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $prestr = ArrayUtil::createLinkstring($values);

        $this->values['sign'] = $this->makeSign($prestr);
        return $this->values['sign'];
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return string
     **/
    public function getSign()
    {
        return $this->values['sign'];
    }

    /**
     * 生成签名
     * @param string $prestr
     * @return string 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($prestr)
    {
        $string = $prestr . "&key=" . $this->config->getMd5Key();

        $string = md5($string);

        $sign = strtoupper($string);
        return $sign;
    }

    /**
     * 获取设置的值
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppid($value)
    {
        $this->values['appid'] = $value;
    }
    /**
     * 获取微信分配的公众账号ID的值
     * @return string 值
     **/
    public function getAppid()
    {
        if (array_key_exists('appid', $this->values)) {
            return $this->values['appid'];
        }

        return null;
    }


    /**
     * 设置微信支付分配的商户号
     * @param string $value
     **/
    public function setMchId($value)
    {
        $this->values['mch_id'] = $value;
    }
    /**
     * 获取微信支付分配的商户号的值
     * @return string 值
     **/
    public function getMchId()
    {
        if (array_key_exists('mch_id', $this->values)) {
            return $this->values['mch_id'];
        }

        return null;
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return string 值
     **/
    public function getNonceStr()
    {
        if (array_key_exists('nonce_str', $this->values)) {
            return $this->values['nonce_str'];
        }

        return null;
    }

    /**
     * 设置签名方式，当前统一使用md5
     * @param string $value
     **/
    public function setsignType($value)
    {
        $this->values['signType'] = $value;
    }
    
    /**
     * 获取使用的签名方式
     * @return string 值
     **/
    public function getsignType()
    {
        if (array_key_exists('signType', $this->values)) {
            return $this->values['signType'];
        }

        return null;
    }
}