<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:47
 * @description: 支付相关的基础配置  无法被继承
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 *
 * @version 2.6.1
 */

namespace Payment;


final class Config
{
    const VERSION = 'dev-2.7.1';

    //========================= ali相关接口 =======================//
    const ALI = 'ali';

    const ALI_CHANNEL_WEB = 'ali_pc_direct';// 支付宝 PC 网页支付

    const ALI_CHANNEL_WAP = 'ali_wap';// 支付宝 手机网页 支付

    const ALI_CHANNEL_APP = 'ali_app';// 支付宝 手机app 支付

    const ALI_CHANNEL_QR = 'ali_web';// 支付宝 扫码支付  主要用于pc站点

    const ALI_REFUND = 'ali_refund';// 支付宝 退款查询


    //========================= 微信相关接口 =======================//
    const WEIXIN = 'wx';

    const WEIXIN_REFUND = 'wx_refund';// 微信退款单查询

    const WEIXIN_TRANS = 'wx_transfer';// 微信企业付款查询

    const WX_CHANNEL_QR = 'wx_web';// 微信公众账号 扫码支付  主要用于pc站点

    const WX_CHANNEL_PUB = 'wx_pub';// 微信 公众账号 支付

    const WX_CHANNEL_APP = 'wx_app';// 微信 APP 支付


    //========================= 金额问题设置 =======================//
    const PAY_MIN_FEE = '0.01';// 支付的最小金额

    const PAY_MAX_FEE = '100000.00';// 支付的最大金额


    //======================= 交易状态常量定义 ======================//
    const TRADE_STATUS_SUCC = 'success';// 交易成功

    const TRADE_STATUS_FAILD  = 'not_pay';// 交易未完成

    const TRADE_STATUS_REFUND_SUCC = 'refund_succ';// 交易退款成功


    //======================= 回调通知类型 =========================//
    const TRADE_NOTIFY = 'trade';// 支付的交易通知

    const REFUND_NOTIFY = 'refund';// 退款的通知

    const TRANSFER_NOTIFY = 'transfer';// 转账的通知


    // ================ 2016.12.26 支付宝新版本信息 ================//
    const ALI_API_VERSION = '1.0';// 支付宝新版本号
}