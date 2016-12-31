<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:14
 * @description:
 */

namespace Payment\Common\Ali\Data;

use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * Class TradeQueryData
 *
 * @property string $transaction_id 支付宝交易号
 * @property string $order_no 商户网站唯一订单号
 * @property string $refund_no  退款单号
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class TradeQueryData extends AliBaseData
{

    protected function buildData()
    {
        $version = $this->version;

        // 业务参数
        $transaction_id = $this->transaction_id;// 支付宝交易号，查询效率高
        $order_no = $this->order_no;// 商户订单号，查询效率低，不建议使用

        if (! empty($transaction_id)) {// 由于魔术方法，无法进行empty的判断，因此需要先取值出来
            // 如果支付宝交易号不为空
            $queryData['trade_no'] = $transaction_id;
        } else {
            // 未提供交易号，则使用订单号
            $queryData['out_trade_no'] = $order_no;
        }

        if ($version === Config::ALI_API_VERSION) {
            $signData = $this->alipay2_0Data($queryData);
        } else {
            $signData = $this->alipay1_0Data($queryData);
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 老版本查询数据
     * @param array $queryData
     * @return array
     */
    protected function alipay1_0Data($queryData)
    {
        // 设置加密的方式
        $data = array_merge($queryData, [
            // 基本参数
            'service'   => 'single_trade_query',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
        ]);

        return $data;
    }

    /**
     * 新版本数据
     * @param array $signData
     * @return array
     */
    protected function alipay2_0Data($signData)
    {
        if ($this->method === AliConfig::ALI_REFUND_QUERY) {
            $signData['out_request_no'] = $this->refund_no;
        }

        $data = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->inputCharset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,

            // 业务参数  新版支付宝，将所有业务参数设置到改字段中了，  这样不错
            'biz_content'   => json_encode($signData, JSON_UNESCAPED_UNICODE),
        ];

        return $data;
    }

    /**
     * 检查参数
     * @author helei
     */
    protected function checkDataParam()
    {
        $transaction_id = $this->transaction_id;// 支付宝交易号，查询效率高
        $order_no = $this->order_no;// 商户订单号，查询效率低，不建议使用

        if ($this->method === AliConfig::ALI_REFUND_QUERY) {
            $refund_no = $this->refund_no;
            if (empty($refund_no)) {
                throw new PayException('支付宝查询退款，必须传入退款时商家自己生成的退款单号');
            }
        }

        // 二者不能同时为空
        if (empty($transaction_id) && empty($order_no)) {
            throw new PayException('必须提供支付宝交易号或者商户网站唯一订单号。建议使用支付宝交易号');
        }
    }
}