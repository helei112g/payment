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
 * @property string $appId   微信分配的公众账号ID
 * @property string $mchId  微信支付分配的商户号
 * @property string $nonceStr  随机字符串，不长于32位
 * @property string $notifyUrl  异步通知的url
 * @property string $feeType  符合ISO 4217标准的三位字母代码 默认位人民币
 * @property string $timeStart  交易开始时间 格式为yyyyMMddHHmmss
 * @property string $md5Key  用于加密的md5Key
 * @property string $signType  加密方式。默认md5
 * @property string $appCertPem 从apiclient_cert.p12中导出证书部分的文件，为pem格式，
 * @property string $appKeyPem 从apiclient_key.pem中导出密钥部分的文件，为pem格式
 * @property array $limitPay 限制的支付渠道
 * @property boolean $returnRaw  是否返回原始数据，只进行签名检查
 * @property string $tradeType   支付类型
 * @property string $terminal_id 终端设备号(门店号或收银设备ID)，默认请传"WEB"
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
                $sign = md5($signStr);
                break;
            case 'HMAC-SHA256':
                $sign = base64_encode(hash_hmac('sha256', $signStr, $this->md5Key));

                break;
            default:
                $sign = '';
        }

        return strtoupper($sign);
    }
}
