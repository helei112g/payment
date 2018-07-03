<?php

namespace Payment;

use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Cancel\Alicancel;

/**
 *
 * Class CancelContext
 * @package Payment
 */
class CancelContext
{
    /**
     * 取消的渠道
     * @var BaseStrategy
     */
    protected $cancel;


    /**
     * 设置对应的取消渠道
     * @param string $channel 取消渠道
     *  - @see Config
     *
     * @param array  $config 配置文件
     * @throws PayException
     * @author dong
     */
    public function initCancel($cancel, array $config)
    {
        try {
            switch ($cancel) {
                case Config::ALI_CANCEL:
                    $this->cancel = new Alicancel($config);
                    break;
                default:
                    throw new PayException('当前仅支持：ALI');
            }
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 通过环境类调用支付取消操作
     *
     * @param array $data
     *
     * @return array
     * @throws PayException
     * @author dong
     */
    public function cancel(array $data)
    {
        if (!$this->cancel instanceof BaseStrategy) {
            throw new PayException('请检查初始化是否正确');
        }

        try {
            return $this->cancel->handle($data);
        } catch (PayException $e) {
            throw $e;
        }
    }
}
