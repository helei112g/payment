<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:14
 * @description:
 */

namespace Payment\Common\Ali\Data;


use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * Class TradeQueryData
 *
 * @property string $transaction_id 支付宝交易号
 * @property string $order_no 商户网站唯一订单号
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class TradeQueryData extends AliBaseData
{

    protected function buildData()
    {
        // 设置加密的方式
        $signData = [
            // 基本参数
            'service'   => 'single_trade_query',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
        ];

        // 业务参数
        $transaction_id = $this->transaction_id;// 支付宝交易号，查询效率高
        $order_no = $this->order_no;// 商户订单号，查询效率低，不建议使用

        if (! empty($transaction_id)) {// 由于魔术方法，无法进行empty的判断，因此需要先取值出来
            // 如果支付宝交易号不为空
            $signData['trade_no'] = $transaction_id;
        } else {
            // 未提供交易号，则使用订单号
            $signData['out_trade_no'] = $order_no;
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 检查参数
     * @author helei
     */
    protected function checkDataParam()
    {
        $transaction_id = $this->transaction_id;// 支付宝交易号，查询效率高
        $order_no = $this->order_no;// 商户订单号，查询效率低，不建议使用

        // 二者不能同时为空
        if (empty($transaction_id) && empty($order_no)) {
            throw new PayException('必须提供支付宝交易号或者商户网站唯一订单号。建议使用支付宝交易号');
        }
    }
}