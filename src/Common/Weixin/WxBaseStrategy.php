<?php
namespace Payment\Common\Weixin;

use GuzzleHttp\Client;
use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Common\WxConfig;
use Payment\Utils\ArrayUtil;
use Payment\Utils\DataParser;

/**
 * Class WxBaseStrategy
 * 微信策略基类
 *
 * @package Payment\Common\Weixin
 * @anthor helei
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
abstract class WxBaseStrategy implements BaseStrategy
{
    /**
     * 需要像微信请求的url。默认是统一下单url
     * @var string $reqUrl
     */
    protected $reqUrl = 'https://api.mch.weixin.qq.com/{debug}/pay/unifiedorder';

    /**
     * 微信的配置文件
     * @var WxConfig $config
     */
    protected $config;

    /**
     * 支付数据
     * @var BaseData $reqData
     */
    protected $reqData;

    /**
     * WxBaseStrategy constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        try {
            $this->config = new WxConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 发送完了请求
     * @param string $xml
     * @return mixed
     * @throws PayException
     * @author helei
     */
    protected function sendReq($xml)
    {
        $url = $this->reqUrl;
        if (is_null($url)) {
            throw new PayException('目前不支持该接口。请联系开发者添加');
        }

        if ($this->config->useSandbox) {
            $url = str_ireplace('{debug}', WxConfig::SANDBOX_PRE, $url);
        } else {
            $url = str_ireplace('{debug}/', '', $url);
        }

        $client = new Client([
            'timeout' => '10.0'
        ]);
        // @note: 微信部分接口并不需要证书支持。这里为了统一，全部携带证书进行请求
        $options = [
            'body' => $xml,
            'cert' => $this->config->appCertPem,
            'ssl_key' => $this->config->appKeyPem,
            'verify' => $this->config->cacertPath,
            'http_errors' => false
        ];
        $response = $client->request('POST', $url, $options);
        if ($response->getStatusCode() != '200') {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $response->getReasonPhrase());
        }

        $body = $response->getBody()->getContents();

        // 格式化为数组
        $retData = DataParser::toArray($body);
        if (strtoupper($retData['return_code']) != 'SUCCESS') {
            throw new PayException('微信返回错误提示：' . $retData['return_msg']);
        }
        if (strtoupper($retData['result_code']) != 'SUCCESS') {
            $msg = $retData['err_code_des'] ? $retData['err_code_des'] : $retData['err_msg'];
            throw new PayException('微信返回错误提示：' . $msg);
        }

        return $retData;
    }

    /**
     * @param array $data
     * @author helei
     * @throws PayException
     * @return array|string
     */
    public function handle(array $data)
    {
        $buildClass = $this->getBuildDataClass();

        try {
            $this->reqData = new $buildClass($this->config, $data);
        } catch (PayException $e) {
            throw $e;
        }

        $this->reqData->setSign();

        $xml = DataParser::toXml($this->reqData->getData());
        try {
            $ret = $this->sendReq($xml);
        } catch (PayException $e) {
            throw $e;
        }

        // 检查返回的数据是否被篡改
        $flag = $this->verifySign($ret);
        if (!$flag) {
            throw new PayException('微信返回数据被篡改。请检查网络是否安全！');
        }

        return $this->retData($ret);
    }

    /**
     * 处理微信的返回值并返回给客户端
     * @param array $ret
     * @return mixed
     * @author helei
     */
    protected function retData(array $ret)
    {
        return $ret;
    }

    /**
     * 检查微信返回的数据是否被篡改过
     * @param array $retData
     * @return boolean
     * @author helei
     */
    protected function verifySign(array $retData)
    {
        $retSign = $retData['sign'];
        $values = ArrayUtil::removeKeys($retData, ['sign', 'sign_type']);

        $values = ArrayUtil::paraFilter($values);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $signStr .= '&key=' . $this->config->md5Key;
        switch ($this->config->signType) {
            case 'MD5':
                $sign = md5($signStr);
                break;
            case 'HMAC-SHA256':
                $sign = hash_hmac('sha256', $signStr, $this->config->md5Key);
                break;
            default:
                $sign = '';
        }

        return strtoupper($sign) === $retSign;
    }
}
