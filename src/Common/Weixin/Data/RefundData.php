<?php
/**
 * @author: helei
 * @createTime: 2016-08-03 15:14
 * @description:
 */

namespace Payment\Common\Weixin\Data;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;


/**
 * Class RefundData
 *
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
 * @property string $transaction_id  微信生成的订单号，在支付通知中有返回
 * @property int $total_fee 订单总金额，单位为分，只能为整数
 * @property int $refund_fee 退款总金额，订单总金额，单位为分，只能为整数
 * @property array $refund_data
 *  $refund_data[] => [
 *      'transaction_id'    => '原付款微信交易号',
 *      'amount'   => '原订单总金额',// 单位为元
 *      'refund_fee' => '退款总金额', // 单位元
 *      'reason'     => '退款理由', // “退款理由”中不能有“^”、“|”、“$”、“#”
 *  ];
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class RefundData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'mch_id'    => $this->mchId,
            'nonce_str' => $this->nonceStr,
            'transaction_id'    => $this->transaction_id,
            'out_refund_no'  => $this->refund_no,// 商户退款单号
            'total_fee' => $this->total_fee,// 订单总金额
            'refund_fee' => $this->refund_fee,// 退款总金额
            'refund_fee_type'   => 'CNY',
            'op_user_id'    => $this->mchId,//操作员帐号, 默认为商户号
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    /**
     * 检查参数
     * @author helei
     */
    protected function checkDataParam()
    {
        $refundNo = $this->refund_no;// 商户退款单号
        $data = $this->refund_data;// 退款的数据，微信不支持批量退款。因此该数组应该仅包含一个退款单元

        if (sizeof($data) != 1) {
            throw new PayException('当前版本，不支持微信批量退款。目前仅支持1笔');
        }

        foreach ($data as $key => $item) {

            if (empty($item['transaction_id'])) {
                throw new PayException('微信交易号必须设置');
            }
            $this->transaction_id = $item['transaction_id'];

            if (empty($item['amount']) || bccomp($item['amount'], 0, 2) === -1) {
                throw new PayException('必须设置订单总金额，并且必须大于0');
            }
            $this->total_fee = bcmul($item['amount'], 100, 0);// 微信以分为单位

            if (empty($item['refund_fee']) || bccomp($item['refund_fee'], $item['amount'], 2) === 1) {
                throw new PayException('必须设置退款金额，且退款金额不能大于订单总金额');
            }
            $this->refund_fee = bcmul($item['refund_fee'], 100 ,0);
        }

        // 该接口，微信配置文件，必须提供cert  key  两个pem文件
        $certPath = $this->certPath;
        $keyPath = $this->keyPath;
        if (empty($certPath)) {
            throw new PayException('退款接口，必须提供 apiclient_cert.pem 证书');
        }

        if (empty($keyPath)) {
            throw new PayException('退款接口，必须提供 apiclient_key.pem 证书');
        }
    }
}