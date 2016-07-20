<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description: 即时到帐 接口的数据处理类
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali\Data\Charge;

use Payment\Common\Ali\Data\BaseData;
use Payment\Utils\ArrayUtil;

/**
 * Class WebChargeData
 *
 * @inheritdoc
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
 */
class WebChargeData extends ChargeBaseData
{

    const SIGN_TYPE = 'MD5';

    /**
     * 即时到帐接口  使用 MD5 加密
     * @param string $signStr
     * @return string
     * @author helei
     */
    protected function makeSign($signStr)
    {
        $signStr .= $this->md5Key;

        return md5($signStr);
    }

    /**
     * 构建 即时到帐 加密数据
     * @author helei
     */
    protected function buildData()
    {
        // 设置加密的方式
        $this->signType = self::SIGN_TYPE;

        $signData = [
            // 基本参数
            'service'   => 'create_direct_pay_by_user',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
            'notify_url'    => trim($this->notifyUrl),
            'return_url'    => trim($this->returnUrl),

            // 业务参数
            'out_trade_no'  => trim($this->order_no),
            'subject'   => trim($this->subject),
            'payment_type'  => 1,
            'total_fee' => trim($this->amount),
            'seller_id' => trim($this->partner),
            'body'  => trim($this->body),
            'paymethod' => 'directPay',// 默认采用余额支付
            'exter_invoke_ip'   => trim($this->client_ip),
            'extra_common_param'    => trim($this->extra_param),
            'it_b_pay'  => trim($this->timeExpire) . 'm',
            'qr_pay_mode'   => 2,
            'goods_type'    => 1, //默认为实物类型
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}