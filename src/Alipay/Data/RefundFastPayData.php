<?php
/**
 * @author: helei
 * @createTime: 2016-06-22 10:02
 * @description:
 */

namespace Payment\Alipay\Data;


class RefundFastPayData extends AliBaseData
{
    public function __construct()
    {
        parent::__construct();

        $this->values = [
            'service'   => 'refund_fastpay_by_platform_pwd',
            'partner'   => $this->config->getPartner(),
            '_input_charset'    => $this->config->getInputCharset(),
            'sign_type' => 'MD5',
            'seller_user_id'    => $this->config->getPartner(),
            'refund_date'   => date('Y-m-d H:i:s', time()),//退款请求时间
        ];
    }

    /**
     * 设置 卖家支付宝账号
     * @param $seller_email
     * @author helei
     */
    public function setSellerEmail($seller_email)
    {
        $this->values['seller_email'] = $seller_email;
    }

    /**
     * 获取 卖家支付宝账号
     * @author helei
     */
    public function getSellerEmail()
    {
        if (array_key_exists('seller_email', $this->values)) {
            return $this->values['seller_email'];
        }

        return null;
    }

    /**
     * 设置 卖家用户ID
     * @param $seller_user_id
     * @author helei
     */
    public function setSellerUserId($seller_user_id)
    {
        $this->values['seller_user_id'] = $seller_user_id;
    }

    /**
     * 获取 卖家用户ID
     * @author helei
     */
    public function getSellerUserId()
    {
        if (array_key_exists('seller_user_id', $this->values)) {
            return $this->values['seller_user_id'];
        }

        return null;
    }

    /**
     * 设置 退款请求时间
     * @param $refund_date
     * @author helei
     */
    public function setRefundDate($refund_date)
    {
        $this->values['refund_date'] = $refund_date;
    }

    /**
     * 获取 退款请求时间
     * @author helei
     */
    public function getRefundDate()
    {
        if (array_key_exists('refund_date', $this->values)) {
            return $this->values['refund_date'];
        }

        return null;
    }

    /**
     * 设置 退款批次号
     * @note 每进行一次即时到账批量退款，都需要提供一个批次号，
     * 通过该批次号可以查询这一批次的退款交易记录，对于每一个合作伙伴，传递的每一个批次号都必须保证唯一性。
     * 格式为：退款日期（8位）+流水号（3～24位）。
     * 不可重复，且退款日期必须是当天日期。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000”。
     * @param $batch_no
     * @author helei
     */
    public function setBatchNo($batch_no)
    {
        $this->values['batch_no'] = $batch_no;
    }

    /**
     * 获取 退款批次号
     * @author helei
     */
    public function getBatchNo()
    {
        if (array_key_exists('batch_no', $this->values)) {
            return $this->values['batch_no'];
        }

        return null;
    }

    /**
     * 设置 退款总笔数
     *
     * @note 即参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔
     * @param $batch_num
     * @author helei
     */
    public function setBatchNum($batch_num)
    {
        $this->values['batch_num'] = $batch_num;
    }

    /**
     * 获取 退款总笔数
     * @author helei
     */
    public function getBatchNum()
    {
        if (array_key_exists('batch_num', $this->values)) {
            return $this->values['batch_num'];
        }

        return null;
    }

    /**
     * 设置 退款单笔数据集
     *
     * @note 单笔数据集格式为：第一笔交易退款数据集#第二笔交易退款数据集#第三笔交易退款数据集…#第N笔交易退款数据集；
     * 交易退款数据集的格式为：原付款支付宝交易号^退款总金额^退款理由；
     *
     * detail_data中的退款笔数总和要等于参数batch_num的值；
     * “退款理由”长度不能大于256字节
     * detail_data中退款总金额不能大于交易总金额
     *
     * @param $detail_data
     * @author helei
     */
    public function setDetailData($detail_data)
    {
        $this->values['detail_data'] = $detail_data;
    }

    /**
     * 获取 退款单笔数据集
     * @author helei
     */
    public function getDetailData()
    {
        if (array_key_exists('detail_data', $this->values)) {
            return $this->values['detail_data'];
        }

        return null;
    }

    /**
     * 进行签名数据
     * @param $prestr
     * @return string
     * @author helei
     */
    protected function makeSign($prestr)
    {
        $prestr .= $this->config->getMd5Key();

        return md5($prestr);
    }
}