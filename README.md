# 版本介绍

此版本在1.0的基础上进行了重构，两个版本互不兼容。

# 功能介绍

支付宝SDK接口文档

* 1. **[支付宝即时到帐](https://helei112g.github.io/2016/07/18/%E6%94%AF%E4%BB%98%E5%AE%9D%EF%BC%9A%E5%8D%B3%E6%97%B6%E5%88%B0%E8%B4%A6%E6%8E%A5%E5%8F%A3%E6%8E%A5%E5%85%A5/)** 
* 2. **[支付宝异步通知回调](https://helei112g.github.io/2016/07/29/%E6%94%AF%E4%BB%98%E7%9A%84%E5%9B%9E%E8%B0%83%E7%BB%9F%E4%B8%80%E5%A4%84%E7%90%86/)**
* 3. **[支付宝手机网站](https://helei112g.github.io/2016/07/29/PHP%E6%8E%A5%E5%85%A5%E6%94%AF%E4%BB%98%E5%AE%9D%E6%89%8B%E6%9C%BA%E7%BD%91%E7%AB%99%E6%94%AF%E4%BB%98%E3%80%81%E7%A7%BB%E5%8A%A8%E6%94%AF%E4%BB%98%E6%8E%A5%E5%8F%A3/)**
* 4. **[支付宝移动支付](https://helei112g.github.io/2016/07/29/PHP%E6%8E%A5%E5%85%A5%E6%94%AF%E4%BB%98%E5%AE%9D%E6%89%8B%E6%9C%BA%E7%BD%91%E7%AB%99%E6%94%AF%E4%BB%98%E3%80%81%E7%A7%BB%E5%8A%A8%E6%94%AF%E4%BB%98%E6%8E%A5%E5%8F%A3/)**
* 5. **[支付宝有密退款](https://helei112g.github.io/2016/08/03/PHP%E6%8E%A5%E5%85%A5%E6%94%AF%E4%BB%98%E5%AE%9D%E6%9C%89%E5%AF%86%E9%80%80%E6%AC%BE%E6%8E%A5%E5%8F%A3/)**
* 6. **[支付宝有密批量付款](https://helei112g.github.io/2016/08/03/PHP%E6%8E%A5%E5%85%A5%E6%94%AF%E4%BB%98%E5%AE%9D%E6%9C%89%E5%AF%86%E6%89%B9%E9%87%8F%E8%BD%AC%E6%AC%BE%E6%8E%A5%E5%8F%A3/)**
* 7. **[支付宝订单查询](https://helei112g.github.io/2016/08/03/PHP%E6%8E%A5%E5%85%A5%E6%94%AF%E4%BB%98%E5%AE%9D%E5%8D%95%E7%AC%94%E8%AE%A2%E5%8D%95%E6%9F%A5%E8%AF%A2%E6%8E%A5%E5%8F%A3/)**

微信支付SDK接口文档

* 1. **[微信公众号支付\微信扫码支付\APP支付 接口](https://helei112g.github.io/2016/08/10/%E5%BE%AE%E4%BF%A1%E7%9A%84%E4%B8%89%E7%A7%8D%E6%94%AF%E4%BB%98%E6%96%B9%E5%BC%8F%E6%8E%A5%E5%85%A5%EF%BC%9AAPP%E6%94%AF%E4%BB%98%E3%80%81%E5%85%AC%E4%BC%97%E5%8F%B7%E6%94%AF%E4%BB%98%E3%80%81%E6%89%AB%E7%A0%81%E6%94%AF%E4%BB%98/)**
* 2. **[订单查询接口/退款订单查询接口/企业付款查询接口](https://helei112g.github.io/2016/08/10/%E5%BE%AE%E4%BF%A1%E6%94%AF%E4%BB%98%E8%AE%A2%E5%8D%95%E3%80%81%E9%80%80%E6%AC%BE%E8%AE%A2%E5%8D%95%E3%80%81%E8%BD%AC%E6%AC%BE%E8%AE%A2%E5%8D%95%E7%9A%84%E6%9F%A5%E8%AF%A2/)**
* 3. **[微信退款接口](https://helei112g.github.io/2016/08/16/PHP%E6%8E%A5%E5%85%A5%E5%BE%AE%E4%BF%A1%E9%80%80%E6%AC%BE%E6%8E%A5%E5%8F%A3/)**
* 4. **[微信异步通知回调接口](https://helei112g.github.io/2016/07/29/%E6%94%AF%E4%BB%98%E7%9A%84%E5%9B%9E%E8%B0%83%E7%BB%9F%E4%B8%80%E5%A4%84%E7%90%86/)**
* 5. **[企业付款接口](https://helei112g.github.io/2016/08/16/PHP%E6%8E%A5%E5%85%A5%E5%BE%AE%E4%BF%A1%E4%BC%81%E4%B8%9A%E4%BB%98%E6%AC%BE%E5%8A%9F%E8%83%BD/)**

# 使用说明

> [文档地址](https://helei112g.github.io/2016/07/18/%E6%94%AF%E4%BB%98%E5%AE%9D%E3%80%81%E5%BE%AE%E4%BF%A1%E6%94%AF%E4%BB%98%E6%8E%A5%E5%85%A5%E9%9B%86%E6%88%90/)

为了便于大家开发，本次2.0以博客的形式，完成完整的文档。文档中还包含了部分代码构建的介绍。

## 安装

* 方式一

通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令。SDK 包已经放到这里 riverslei/payment

```php
    composer require "riverslei/payment:~2.0"
```

放入composer.json文件中

```php
    "require": {
        "riverslei/payment": "~2.0"
    }
```

* 方式二
直接下载安装，SDK 没有依赖其他第三方库，但需要参照 composer的autoloader，增加一个自己的autoloader程序。代码中以提供一个默认autolaod.php  可直接使用.

## 运行环境

Payment SDK | PHP版本
---|---
2.x | cURL extension, mbstring, 5.5 ~ 7.0
1.x | cURL extension, 5.3 ~ 5.6

具体使用规则可参考 `examples/*` 中的示例.本SDK可直接运行.进行测试

# 说明

在开发1.0版本的时候，主要是考虑到自己项目的使用。因此很多朋友说他们有多个账户，配置文件该怎么写？

有的配置文件以前在redis中，或者db中，又该如何完成？

本次2.0版本解决了以上问题，并且提供了更加简单的调用接口。

1.0发出后，虽然配备了示例代码，还是有很多朋友无法灵活运用于代码中。

本次开发过程中将配置完善的文档，会介绍使用，以及此SDK开发思路，便于大家自己根据情况修改或者增加新功能。

若需要技术支持，可添加微信： helei543345  (此服务不免费哦！)

# 赞助说明

非常感谢以下企业、朋友的赞赏，感谢你们的认可与支持。

名字 | 金额 | 说明 | 时间
---|---|---|---
张松 | 15.00 | 不错，已用到项目中 | 2016-6-17
[一米市集](http://yimishiji.com/) | 1000.00 | 希望提供技术长期合作 | 2016-7-20
尊称韦爵爷 | 1.00 | 赶紧出个yii的扩展 | 2016-7-22
小兵~招UI前端 | 50.00 | 继续努力,喝杯水吧:-) | 2016-8-14
哈罗Joe | 1.00 | 加油~~ | 2016-8-23
