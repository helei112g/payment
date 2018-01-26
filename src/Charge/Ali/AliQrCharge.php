<?php
namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\QrChargeData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;

/**
 * 支付宝扫码支付- 用户扫商户生成的二维码完成支付
 *
 * Class AliQrCharge
 * @package Payment\Charge\Weixin
 *
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class AliQrCharge extends AliBaseStrategy
{
    protected $method = 'alipay.trade.precreate';

    public function getBuildDataClass()
    {
        $this->config->method = $this->method;
        return QrChargeData::class;
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     *
     * @throws PayException
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        $url = parent::retData($ret);

        // 发起网络请求
        try {
            $data = $this->sendReq($url);
        } catch (PayException $e) {
            throw $e;
        }

        // 检查是否报错
        if ($data['code'] !== '10000') {
            new PayException($data['sub_msg']);
        }

        return $data['qr_code'];
    }
}
