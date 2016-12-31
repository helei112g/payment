<?php
/**
 * @author: helei
 * @createTime: 2016-07-26 18:18
 * @description:
 */

namespace Payment\Common\Ali\Data;

use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * Class RefundData
 *
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔(3～24位)
 * @property string $batch_num  本次退款的总笔数
 * @property array $refund_data
 *  $refund_data[] => [
 *      'transaction_id'    => '原付款支付宝交易号',
 *      'refund_fee' => '退款总金额', // 单位元
 *      'reason'     => '退款理由', // “退款理由”中不能有“^”、“|”、“$”、“#”
 *  ];
 *
 * @property string $transaction_id  支付宝生成的订单号，在支付通知中有返回  (支付宝新版本会用到)
 * @property int $refund_fee 退款总金额，订单总金额，只能为整数  (支付宝新版本会用到)
 * @property string $reason 	退款的原因说明  (支付宝新版本会用到)
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class RefundData extends AliBaseData
{
    // 退款理由中不能包含以下字符。直接进行过滤
    protected $danger = ['^', '|', '$', '#'];

    // 替换为安全的字符串
    protected $safe = ['', '', '', ''];

    /**
     * 检查退款数据是否正常
     * @author helei
     */
    protected function checkDataParam()
    {
        $refundNo = $this->refund_no;
        $data = $this->refund_data;

        $version = $this->version;
        if ($version === Config::ALI_API_VERSION && sizeof($data) != 1) {
            throw new PayException('支付宝新版本退款。目前仅支持1笔1次');
        }

        // 检查退款单号是否设置
        if (empty($refundNo) || mb_strlen($refundNo) < 3 || mb_strlen($refundNo) > 24) {
            throw new PayException('退款单号，不能为空，长度在3~24位之间');
        }
        $this->refund_no = date('Ymd') . $refundNo;// 生成支付宝需要的退款单号

        $refundData = '';// 退款数据集
        $count = 0;// 总退款笔数
        foreach ($data as $key => $item) {
            // 过滤理由中的敏感字符
            $reason = str_replace($this->danger, $this->safe, $item['reason']);
            if (empty($reason) || mb_strlen($reason) > 256) {
                throw new PayException("refund_data 索引为{$key}的数据，退款理由为空或者长度超过256");
            }

            // 检查金额是否正确。不能小于0.01
            if (bccomp($item['refund_fee'], '0.01', 2) === -1) {
                throw new PayException("refund_data 索引为{$key}的数据，交易金额小于0.01");
            }

            // 原支付宝交易号
            if (
                empty($item['transaction_id']) ||
                mb_strlen($item['transaction_id']) < 16 ||
                mb_strlen($item['transaction_id']) > 64
            ) {
                throw new PayException("refund_data 索引为{$key}的数据，原支付宝交易账号不合法");
            }

            if ($version === Config::ALI_API_VERSION) {
                // 新版本
                $this->transaction_id = $item['transaction_id'];// 支付宝交易号
                $this->refund_fee = $item['refund_fee'];// 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
                $this->reason = $reason;// 	退款的原因说明
                break;
            }

            $refundData .= "{$item['transaction_id']}^{$item['refund_fee']}^{$reason}#";
            $count++;
        }

        // 老版本
        if ($version !== Config::ALI_API_VERSION) {
            // 移除最后一个 # 号
            $refundData = trim($refundData, '#');

            if (empty($count) || empty($refundData) || $count > 1000) {
                throw new PayException('经过检查，传入的合法交易数据集为空，或者交易笔数大于1000');
            }

            $this->batch_num = $count;
            $this->refund_data = $refundData;
        }
    }

    /**
     * 构建 退款接口 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $version = $this->version;
        if ($version) {
            $signData = $this->alipay2_0Data();
        } else {
            $signData = $this->alipay1_0Data();
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    protected function alipay1_0Data()
    {
        // 设置加密的方式
        $signData = [
            // 基本参数
            'service'   => 'refund_fastpay_by_platform_pwd',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
            'notify_url'    => trim($this->notifyUrl),

            // 业务参数
            'seller_user_id' => trim($this->partner),
            'refund_date'   => date('Y-m-d H:i:s', time()),
            'batch_no'  => $this->refund_no,
            'batch_num' => $this->batch_num,
            'detail_data'   => $this->refund_data,
        ];

        return $signData;
    }

    protected function alipay2_0Data()
    {
        // 设置加密的方式
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'charset'       => $this->inputCharset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,

            // 业务参数  新版支付宝，将所有业务参数设置到改字段中了，  这样不错
            'biz_content'   => $this->getBizContent(),
        ];

        return $signData;
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return string
     */
    private function getBizContent()
    {
        $content = [
            'trade_no'      => strval($this->transaction_id),
            'refund_amount' => strval($this->refund_fee),
            'refund_reason' => strval($this->reason),
            'out_request_no'  => $this->refund_no,
        ];

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}