<?php
/**
 * @author: helei
 * @createTime: 2016-07-26 18:18
 * @description:
 */

namespace Payment\Common\Ali\Data;


use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * Class RefundData
 *
 * @property string $refund_no  商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔(3～24位)
 * @property string $batch_num  本次退款的总笔数
 * @property array $detail_data
 *  $detail_data[] => [
 *      'transaction_id'    => '原付款支付宝交易号'
 *      'refund_fee' => '退款总金额', // 单位元
 *      'reason'     => '退款理由',
 *  ];
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class RefundData extends BaseData
{

    public function __construct(AliConfig $config, array $reqData)
    {
        parent::__construct($config, $reqData);

        $this->sign_type = 'RSA';

        $this->checkRefundDataParam();
    }

    /**
     * 检查退款数据是否正常
     * @author helei
     */
    protected function checkRefundDataParam()
    {
        $refundNo = $this->refund_no;
        $detailData = $this->detail_data;

        // 检查退款单号是否设置
        if (empty($refundNo) || mb_strlen($refundNo) < 3 || mb_strlen($refundNo) > 24) {
            throw new PayException('退款单号，不能为空，长度在3~24位之间');
        }
        $this->refund_no = date('Ymd') . $refundNo;// 生成支付宝需要的退款单号

        // 检查退款的数据集
        $this->batch_num = $count = count($detailData);
    }

    /**
     * 构建 退款接口 加密数据
     * @author helei
     */
    protected function buildData()
    {
        // 设置加密的方式
        $this->signType = $this->sign_type;

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
            'batch_no'  => '',
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }
}