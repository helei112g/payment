[![Software license][ico-license]](LICENSE)
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

-----

最新文档使用 GitBook 编写，内容完整清晰
- [Payment使用文档](https://helei112g1.gitbooks.io/payment-sdk/content/)(未完结)

历史文档
- [Payment3.x 使用教程](https://helei112g.github.io/categories/payment-3/)
- [Payment2.x 使用教程](https://helei112g.github.io/categories/payment/)

----

>
Payment 需要 PHP &gt;= 5.6以上的版本，并且同时需要PHP安装以下扩展

```
- cUR extension

- mbstring

- BC Math
```

# Payment是什么？

Payment是一个集成了 **支付宝支付**、**微信支付**、**招商支付**的PHP SDK。服务端开发者将它集成到自己的项目中，可以方便的通过相同的操作方式进行各项支付操作。不在需要开发者去单独了解支付宝、微信、招商的接口文档。以发起支付举例，开发者只需要通过：

```
try {
    $str = Charge::run(支付类型, 配置文件, 支付数据);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}
```

就可以得到支付结果返回数据。关心的核心只需要正确了解如何组装**支付数据**，以及如何正确处理返回后的数据即可。

Payment支持所有的PHP项目，只要求PHP版本大于等于5.6即可。同时支持composer与手动引入两种安装方式。

# 当前支持的接口

当前sdk仅接入了支付宝支付、微信支付、招商支付（支付、退款）。中国现在电子支付的公司巨多，无法一一接入，欢迎各位发扬自己动手、丰衣足食的光荣传统，提交**PR**给我，代码贡献指南 [看这里](dai-ma-gong-xian.md)

## 支付宝接口

* **APP支付**（接入支付宝SDK，用户支付时唤起支付宝完成支付）
* **手机网站支付**（移动端唤起支付宝钱包或网页收银台完成支付）
* **电脑网站支付**（用户通过支付宝完成支付，交易款项即时到账）-原即时到账
* **当面付**（商户扫码首款或用户扫码付款）
* **单笔转账到支付宝**（支付宝商户向其它支付宝账户单笔转账）
* **交易支付、转账、退款查询接口**
* **交易退款接口**
* 对账单下载（待开发）
* 交易结算接口（待开发）
* 交易关闭接口（待开发）

## 微信支付接口

* **刷卡支付**（用户打开微信钱包的刷卡界面，商户扫码后提交完成交易）
* **公众号支付**（用户在微信内进入商家的H5页面，页面内调用JSSDK完成支付）
* **扫码支付**（用户打开扫一扫，扫码商户二维码完成支付）
* **APP支付**（商户APP中集成微信SDK，用户点击后跳转到微信完成支付）
* **H5支付**（用户在微信以外的浏览器请求微信支付的场景唤起微信支付）
* **小程序支付**（用户在微信小程序中使用微信支付）
* **企业付款**（企业向用户付款）
* **交易支付、转账、退款查询接口**
* **交易退款接口**
* 对账单下载（待开发）
* 现金红包（待开发）
* 代金券或立减优惠券（待开发）

## 招商支付

* **用户签约**（首次使用招商支付的用户完成绑卡操作）
* **招商一网通支付**（发起支付请求，招商支付仅此一个接口）
* **交易退款**
* **查询招商公钥**
* **交易支付、退款查询**
* 查询入账明细（待开发）
* 查询协议（待开发）
* 取消协议（待开发）

## 安装
* **安装方式一**

通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者直接运行下面的命令。

```php
    composer require "riverslei/payment:~4.0"
```

放入composer.json文件中

```php
    "require": {
        "riverslei/payment": "~4.0"
    }
```

然后运行

```
composer update
```

* **安装方式二**

直接下载放入自己的项目中，通过 `require` 的方式引用代码。极度不推荐

# Change Log #
- 接入支付宝电脑网站支付、微信服务商模式支持(from v4.0.0)
- 加入招商一网通支付，加入详细的demo(from v3.1.0)
- 支付宝密钥支持字符串、文件两种方式配置，微信支持HMAC-SHA256加密（from v3.0.1）
- 支持支付宝rsa2签名 加入支付宝当面付-条码支付(条码与声波两种模式)   微信加入刷卡支付、小程序支付、H5支付  提供客户端静态调用类 不再兼容支付宝老版本接口（from v3.0.0）
- 支持支付宝新版本支付接口（from v2.7.0）
- 配置文件控制权限由使用者控制（from v2.0.0）

----


# 联系&打赏 #

如果真心觉得项目帮助到你，为你节省了成本，欢迎鼓励一下。

如果有什么问题，可通过以下方式联系我。提供有偿技术服务。

也希望更多朋友可用提供代码支持。欢迎交流与大赏。

**邮箱**：dayugog@gmail.com


**个人公众号：** `icanfo`

![image](http://ol59nqr1i.bkt.clouddn.com/mp-qr.jpg)

感谢朋友们的支持：[支持名单](SUPPORT.md)

建了个微信群，二维码有过期时间，就不放出来了，如果想入群的可加我微信号: `helei543345`
![image](http://ol59nqr1i.bkt.clouddn.com/pay-qr.jpg?imageView2/2/w/500/h/400)

# Contribution #
[Contribution Guide](CONTRIBUTING.md)

# License #

The code for Payment is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).


[ico-license]: https://img.shields.io/github/license/helei112g/payment.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/riverslei/payment.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/riverslei/payment.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/riverslei/payment
[link-downloads]: https://packagist.org/packages/riverslei/payment/stats