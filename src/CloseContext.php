<?php
namespace Payment;

use Payment\Close\AliClose;
use Payment\Close\WxClose;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;

/**
 * Class CloseContext
 * @package Payment
 */
class CloseContext{
    /**
     * 退款的渠道
     * @var BaseStrategy
     */
    protected $close;

    /**
     * BY biker
     * @param $channel
     * @param array $config
     * @throws PayException
     */
    public function initClose($channel, array $config)
    {
        try {
            switch ($channel) {
                case Config::ALI_CLOSE:
                    $this->close = new AliClose($config);
                    break;
                case Config::WX_CLOSE:
                    $this->close = new WxClose($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI WEIXIN');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * BY biker
     * @param array $data
     * @return mixed
     * @throws PayException
     */
    public function close(array $data)
    {
        if (!$this->close instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->close->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}
