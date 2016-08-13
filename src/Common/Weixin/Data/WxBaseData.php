<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 18:05
 * @description: 微信支付相关接口的数据基类
 */

namespace Payment\Common\Weixin\Data;

use Payment\Common\BaseData;
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
 * @property string $certPath 从apiclient_cert.p12中导出证书部分的文件，为pem格式，
 * @property string $keyPath 从apiclient_key.pem中导出密钥部分的文件，为pem格式
 *
 * @package Payment\Common\Weixin\Dataa
 */
abstract class WxBaseData extends BaseData
{

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
                $signStr .= '&key=' . $this->md5Key;
                $sign = strtoupper(md5($signStr));
                break;
            default :
                $sign = '';
        }

        return strtoupper($sign);
    }
}