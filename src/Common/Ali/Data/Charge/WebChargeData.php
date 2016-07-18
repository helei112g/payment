<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:28
 * @description:
 */

namespace Payment\Common\Ali\Data\Charge;

use Payment\Common\Ali\Data\BaseData;
use Payment\Utils\ArrayUtil;

/**
 * Class WebChargeData
 *
 * @inheritdoc
 *
 * @property $order_no
 * @property $amount
 * @property $client_ip
 * @property $subject
 * @property $body
 * @property $extra_param
 *
 * @package Payment\Charge\Ali\Data
 * anthor helei
 */
class WebChargeData extends BaseData
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
            'partner'   => $this->partner,
            '_input_charset'   => $this->inputCharset,
            'sign_type'   => $this->signType,
            'notify_url'    => $this->notifyUrl,
            'return_url'    => $this->returnUrl,

            // 业务参数
            'out_trade_no'  => $this->order_no,
            'subject'   => $this->subject,
            'payment_type'  => 1,
            'total_fee' => $this->amount,
            'seller_id' => $this->partner,
            'body'  => $this->body,
            'paymethod' => 'directPay',// 默认采用余额支付
            'exter_invoke_ip'   => $this->client_ip,
            'extra_common_param'    => $this->extra_param,
            'it_b_pay'  => $this->timeExpire . 'm',
            'qr_pay_mode'   => 2,
            'goods_type'    => 1, //默认为实物类型
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}