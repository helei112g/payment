<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:51
 * @description: 支付的策略接口
 */

namespace Payment\Charge;


interface ChargeStrategy
{
    /**
     * 获取用于支付的相关数据
     * @param array $data 支付的对应数据
     *
     *  $data['order_no']  必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
     *  $data['amount']  必须， 订单总金额, 人民币为元
     *  $data['client_ip']  必须， 发起支付请求客户端的 IP 地址，格式为 IPV4
     *  $data['subject']  必须， 商品的标题，该参数最长为 32 个 Unicode 字符
     *  $data['body']  必须， 商品的描述信息
     *  $data['success_url']  必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
     *
     *  $data['return_url']  可选， 支付宝 移动支付不要传这个参数（同步通知地址）
     *  $data['time_expire']  可选， 订单失效时间，单位是 分钟
     *  $data['extra_param']  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
     *
     * @return mixed
     * @author helei
     */
    public function charge(array $data);
}