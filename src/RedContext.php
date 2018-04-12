<?php
namespace Payment;

use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Red\WxRed;

/**
 * 红包操作
 * Class RedContext
 * @package Payment
 */
class RedContext
{
    /**
     * 发送渠道
     * @var BaseStrategy
     */
    protected $red;

    /**
     * 设置对应的发送渠道
     * @param string $channel 退款渠道
     *  - @see Config
     *
     * @param array $config 配置文件
     * @throws PayException
     * @author IT
     */
    public function initRed($channel, array $config)
    {
        try {
            switch ($channel) {
                case Config::ALI_RED:
                    $this->red = new AliRed($config);
                    break;
                case Config::WX_RED:
                    $this->red = new WxRed($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI WEIXIN两个常量');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 通过环境类调用支付转款操作
     *
     * @param array $data
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function red(array $data)
    {
        if (! $this->red instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->red->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}
