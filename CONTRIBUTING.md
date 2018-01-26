Payment只有在大家的使用反馈中才能得到不断的完善。

我希望通过真实的项目来驱动它不断发展，在为工作带来方便的同时尽力保持它的简洁。

# issue报Bug
由于Payment高度依赖第三方接口，因此第三方一个小的变动也会导致项目产生一个大版本号。当前主要有:
- 2.x 应该没多少人使用了，已经放弃维护
- 3.x 继续维护，只修bug，不做接口更新 
- 4.x 当前开发版本，均保持当前第三方的最新接口

由于版本比较多。因此报bug建议采用以下格式：

[3/4.x]版本，在什么环境下（沙盒还是正式），调用了什么接口，出现了什么错误（最好有截图）。自己尝试过哪些办法去解决未达到预期效果

推荐所有的bug在提交时，先使用demo代码运行一下，看看能否通过。

**只提供标题，或者没有重现步骤的，将不处理**

# 贡献代码
请代码书写遵循以下规则:

- [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
- 使用4个空格作为缩紧（不是tab）
- 命名使用驼峰命名（不准使用拼音）
- 请给类、方法、变量添加注释，注释需要包含：日期、修改人、含义

## 开发流程

1. Fork [helei112g/payment](https://github.com/helei112g/payment) 到本地
2. 创建新的分支：

    ```shell
    $ git checkout -b new_feature
    ```

3. 编写代码
4. Push 到你的分支:

    ```shell
    $ git push origin new_feature
    ```

5. 创建 Pull Request 并描述你完成的功能或者做出的修改

所提交的部分一定自己真实测试完毕，如果是新的支付功能，需要添加对应的demo以及

相关功能的文档(暂无开源文档地址，请根据功能名称，提供 `.md`的说明文档)。

## 代码说明
为了让大家快速理解代码结构，将项目相关结构图进行说明，这里从调用者的角度出发进行描述
![image](http://ol59nqr1i.bkt.clouddn.com/jiegou.jpeg)

这张图表现的是库的一个层次。用支付宝手机wap支付为例：
调用这只需要通过
```php
Charge::run()
```
这个方法即可完成所有调用。剩下的所有东西对调用者都应该是透明的

这个方法内部会首先通过
```php
ChargeContext::initCharge()
```
进行上下文的初始化，完成准备工作，并返回一个具体的对应支付实例，在这里是：`AliAppCharge`

接着内部会调用
```php
AliAppCharge::charge()
```
完成支付的请求，他会把结果返回给调用者

在 `AliAppCharge::charge()` 调用中完成了请求数据的组装，请求数据的签名，如果需要网络请求，会发送网络请求到支付宝网关。并把结果逐步返回。

## 核心类图
本库的所有功能，层次结构比较一致。这里以支付宝app支付为例，进行一下类图描述，方便大家以此进行类比

![uml](http://ol59nqr1i.bkt.clouddn.com/payment-uml.png)

如果看不清楚，可[点击下载](http://ol59nqr1i.bkt.clouddn.com/payment-uml.png)

---------
**`遇到bug，90%都是秘钥相关导致的，微信可能与后台配置有关。请仔细检查。`**