[![Software license][ico-license]](LICENSE)
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]

-----

所有的支付接入请看文档操作。如果文档遇到问题可通过后面提供的方式联系到我。

- [Payment3.x 使用教程](https://helei112g.github.io/categories/payment-3/) (推荐的版本)
- [Payment2.x 使用教程](https://helei112g.github.io/categories/payment/)

----

当前支持 `微信`、`支付宝`、`招商一网通` 三个渠道： 
- **微信支付**：刷卡支付、公众号支付、扫码支付、APP支付、H5支付、小程序支付全部支持；

- **支付宝支付**：手机网站支付、APP支付、扫码支付、条码支付、电脑网站支付（即时到账）全部支持；

- **招商一网通**：app支付、手机网站支付（实际上是同一种支付，但是可用于该两种场景）。

支持所有第三方的支付订单查询、退款操作、退款订单状态查询。同时支持支付宝与微信的转账操作。

# 安装Payment #

`Payment` 需要 `PHP >= 5.6`，并且需要安装以下扩展：
```
- cURL extension

- mbstring

- BC Math
```


* **安装方式一**

通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者直接运行下面的命令。

```php
    composer require "riverslei/payment:~3.1"
```

放入composer.json文件中

```php
    "require": {
        "riverslei/payment": "~3.1"
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

# Payment 能够做什么 #

Paymeng 主要帮助 php 开发者在服务端快速接入主流的支付平台(支付宝支付、微信支付等)。节省时间少走弯路。

Payment 针对不同支付平台，提供了统一的调用方式，开发者无需再一个平台一个平台的去阅读文档、调试。所有的支付平台后台服务统一用一套代码，在支付对接模块的代码维护量大大减少，可以把更多的时间和精力花在自身产品的核心业务上

## 为什么要用Payment SDK ##

所有的支付官方都提供了demo，方便开发者学习使用。但是其中每一个支付 demo 都不尽相同，并且不少 官方 demo 还有不少错误，让开发者使用的时候莫名其妙。

针对不同的支付，官方demo写法各异，很多还使用了老旧的 php 语法。

而 **Payment SDK** 针对不同服务商的支付功能，都提供统一的调用方式，大大降低学习与使用成本。

## 与其他聚合支付服务的对比 ##
这里最主要的对比对象是ping++。当然我这个个人开发者肯定没法与之相比。首先ping++服务更多，接入的支付种类更多。开放出来的接口也更多。

但是本sdk的优势也非常明显。
- 使用项目自己部署，只需向第三方支付服务提供者付费（阿里、腾讯）。
- 项目开源，遵循 **MIT** 许可证，大家可自由更改。
- 根据自己需求，可以自己动手定义个性化。
- 通过composer安装管理，方便升级。
- 就算我以后不维护升级了，也保证你代码可用，如果用第三方聚合的支付，与第三方就有了强关联。

# 联系&打赏 #

如果真心觉得项目帮助到你，为你节省了成本，欢迎鼓励一下。

如果有什么问题，可通过以下方式联系我。提供有偿技术服务。

也希望更多朋友可用提供代码支持。欢迎交流与大赏。

**邮箱**：dayugog@gmail.com


**个人公众号：** `icanfo`

![image](http://ol59nqr1i.bkt.clouddn.com/mp-qr.jpg)

感谢朋友们的支持：[支持名单](SUPPORT.md)

# Contribution／贡献 #
贡献指南：[Contribution Guide](CONTRIBUTING.md)

# License #

The code for Payment is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).


[ico-license]: https://img.shields.io/github/license/helei112g/payment.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/riverslei/payment.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/riverslei/payment.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/riverslei/payment
[link-downloads]: https://packagist.org/packages/riverslei/payment/stats