<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:10
 * @description: 支付宝支付接口的基类。
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali;

use Payment\Common\Ali\Data\BaseData;
use Payment\Common\AliConfig;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

abstract class AliBaseStrategy implements BaseStrategy
{
    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * 支付数据
     * @var BaseData $reqData
     */
    protected $reqData;

    /**
     * AliCharge constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new AliConfig($config);
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
        if ('Payment\Charge\Ali\AliAppCharge' == get_called_class()) {
            // 如果是移动支付，直接返回数据信息。并且对sign做urlencode编码
            $data['sign'] = urlencode($data['sign']);
            return ArrayUtil::createLinkstring($data);
        }

        $retData = $this->config->getewayUrl . http_build_query($data);
        return $retData;
    }
}