<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:42
 * @description: 暴露给客户端调用的接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment;


use Payment\Charge\Ali\AliAppCharge;
use Payment\Charge\Ali\AliWapCharge;
use Payment\Charge\Ali\AliWebCharge;
use Payment\Charge\Ali\AliQrCharge;
use Payment\Charge\Weixin\WxAppCharge;
use Payment\Charge\Weixin\WxPubCharge;
use Payment\Charge\Weixin\WxQrCharge;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;

/**
 * Class ChargeContext
 *
 * 支付的上下文类
 *
 * @package Payment
 * anthor helei
 */
class ChargeContext
{
    /**
     * 支付的渠道
     * @var BaseStrategy
     */
    protected $payWay;


    /**
     * 设置对应的支付渠道
     * @param string $channel 支付渠道
     *  - @see Config
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initCharge($channel, array $config)
    {
        // 初始化时，可能抛出异常，再次统一再抛出给客户端进行处理
        try {
            switch ($channel) {
                case Config::ALI_CHANNEL_WEB:// 老版本不支持
                    $this->payWay = new AliWebCharge($config);
                    break;
                case Config::ALI_CHANNEL_WAP:
                    $this->payWay = new AliWapCharge($config);
                    break;
                case Config::ALI_CHANNEL_APP:
                    $this->payWay = new AliAppCharge($config);
                    break;
                case Config::ALI_CHANNEL_QR:
                    $this->payWay = new AliQrCharge($config);
                    break;
                case Config::WX_CHANNEL_QR:
                    $this->payWay = new WxQrCharge($config);
                    break;
                case Config::WX_CHANNEL_PUB:
                    $this->payWay = new WxPubCharge($config);
                    break;
                case Config::WX_CHANNEL_APP:
                    $this->payWay = new WxAppCharge($config);
                    break;
                default :
                    throw new PayException('当前仅支持：支付宝 与 微信');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 通过环境类调用支付
     * @param array $data
     *
     * ```php
     * $payData = [
     *      "order_no"	=> createPayid(),
     *      "amount"	=> '0.01',// 单位为元 ,最小为0.01
     *      "client_ip"	=> '127.0.0.1',
     *      "subject"	=> '测试支付',
     *      "body"	=> '支付接口测试',
     *      "extra_param"	=> '',
     * ];
     * ```
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function charge(array $data)
    {
        if (! $this->payWay instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->payWay->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}