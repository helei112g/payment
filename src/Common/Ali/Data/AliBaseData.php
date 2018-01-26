<?php
namespace Payment\Common\Ali\Data;

use Payment\Common\BaseData;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Rsa2Encrypt;
use Payment\Utils\RsaEncrypt;

/**
 * Class BaseData
 *
 * @property string $getewayUrl  支付宝网关
 * @property string $appId   支付宝分配给开发者的应用ID
 * @property string $method  接口名称
 * @property string $format  	仅支持JSON
 * @property string $returnUrl  	HTTP/HTTPS开头字符串
 * @property string $charset  请求使用的编码格式，如utf-8,gbk,gb2312等 当前仅支持  utf-8
 * @property string $timestamp  发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
 * @property string $version   调用的接口版本，固定为：1.0
 * @property string $notifyUrl  支付宝服务器主动通知商户服务器里指定的页面http/https路径
 *
 * @property string $rsaPrivateKey  rsa私钥路径
 * @property string $rsaAliPubKey  rsa支付宝公钥路径
 *
 * @property string $partner  合作id
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
 */
abstract class AliBaseData extends BaseData
{
    public function getData()
    {
        $data = parent::getData();

        // 新版需要对数据进行排序
        $data = ArrayUtil::arraySort($data);
        return $data;
    }

    /**
     * 签名算法实现
     * @param string $signStr
     * @return string
     * @author helei
     */
    protected function makeSign($signStr)
    {
        switch ($this->signType) {
            case 'RSA':
                $rsa = new RsaEncrypt($this->rsaPrivateKey);

                $sign = $rsa->encrypt($signStr);
                break;
            case 'RSA2':
                $rsa = new Rsa2Encrypt($this->rsaPrivateKey);

                $sign = $rsa->encrypt($signStr);
                break;
            default:
                $sign = '';
        }

        return $sign;
    }

    /**
     * 构建 支付 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $bizContent = $this->getBizContent();
        $bizContent = ArrayUtil::paraFilter($bizContent);// 过滤掉空值，下面不用在检查是否为空

        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->charset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,
            'notify_url'    => $this->notifyUrl,

            // 业务参数
            'biz_content'   => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];

        // 电脑支付  wap支付添加额外参数
        if (in_array($this->method, ['alipay.trade.page.pay', 'alipay.trade.wap.pay'])) {
            $signData['return_url'] = $this->returnUrl;
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 支付宝构建请求查询的数据
     * @return mixed
     */
    abstract protected function getBizContent();
}
