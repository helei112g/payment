<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 18:04
 * @description: 微信的策略基类
 */

namespace Payment\Common\Weixin;


use Payment\Charge\Weixin\WxAppCharge;
use Payment\Charge\Weixin\WxPubCharge;
use Payment\Charge\Weixin\WxQrCharge;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Common\Weixin\Data\BaseData;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;

abstract class WxBaseStrategy implements BaseStrategy
{

    /**
     * 支付宝的配置文件
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
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new WxConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 获取支付对应的数据完成类
     * @return BaseData
     * @author helei
     */
    abstract protected function getBuildDataClass();

    /**
     * 发送完了请求
     * @param string $xml
     * @return mixed
     * @throws PayException
     * @author helei
     */
    protected function sendReq($xml)
    {
        $url = $this->getReqUrl();

        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_HEADER'    => 0
        ])->post($xml)->submit($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试');
        }
        // 格式化为数组
        $retData = DataParser::toArray($responseTxt['body']);
        if ($retData['return_code']) {
            throw new PayException($retData['return_msg']);
        }

        return $retData;
    }

    /**
     * 获取需要的url
     * @author helei
     */
    protected function getReqUrl()
    {
        $class = get_called_class();

        $chargeClass = $this->getChargeClassName();
        if (in_array($class, $chargeClass)) {
            return WxConfig::UNIFIED_URL;
        }
    }

    /**
     * 返回可以进行支付的类
     * @return array
     * @author helei
     */
    protected function getChargeClassName()
    {
        return [
            WxAppCharge::class,
            WxPubCharge::class,
            WxQrCharge::class,
        ];
    }

    public function handle(array $data)
    {
        $buildClass = $this->getBuildDataClass();

        try {
            $this->reqData = new $buildClass($this->config, $data);
        } catch (PayException $e) {
            throw $e;
        }

        $this->reqData->setSign();
        $data = $this->reqData->getData();

        $xml = DataParser::toXml($data);
        $ret = $this->sendReq($xml);

        var_dump($ret);exit;
    }
}