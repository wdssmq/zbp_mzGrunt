## 投喂支持

爱发电：[https://afdian.net/a/wdssmq](https://afdian.net/a/wdssmq "沉冰浮水正在创作和 z-blog 相关或无关的各种有用或没用的代码 | 爱发电")

哔哩哔哩：[https://space.bilibili.com/44744006](https://space.bilibili.com/44744006 "沉冰浮水的个人空间\_哔哩哔哩\_bilibili")「投币或充电」「[大会员卡券领取 - bilibili](https://account.bilibili.com/account/big/myPackage "大会员卡券领取 - bilibili")」

RSS 订阅：[https://feed.wdssmq.com](https://feed.wdssmq.com "沉冰浮水博客的 RSS 订阅地址") 「[「言说」RSS 是一种态度！！](https://www.wdssmq.com/post/20201231613.html "「言说」RSS 是一种态度！！")」

在更多平台关注我：[https://www.wdssmq.com/guestbook.html#其他出没站点和信息](https://www.wdssmq.com/guestbook.html#%E5%85%B6%E4%BB%96%E5%87%BA%E6%B2%A1%E5%9C%B0%E7%82%B9%E5%92%8C%E4%BF%A1%E6%81%AF "在更多平台关注我")

更多「小代码」：[https://cn.bing.com/search?q=小代码+沉冰浮水](https://cn.bing.com/search?q=%E5%B0%8F%E4%BB%A3%E7%A0%81+%E6%B2%89%E5%86%B0%E6%B5%AE%E6%B0%B4 "小代码 沉冰浮水 - 必应搜索")

<!-- ##################################### -->

## 说明

> Grunt: JavaScript 世界的构建工具 | Grunt 中文网
>
> [https://www.gruntjs.cn/](https://www.gruntjs.cn/ "Grunt: JavaScript 世界的构建工具 | Grunt 中文网")

本工具可在指定主题目录下创建一个 compile 文件夹并放入 Grunt 所需文件，按说明部署相关依赖后即可通过 Grunt 构建主题的 CSS 和 JS；

- 支持 sass 和 less；
- 使用 BrowserSync 自动刷新页面；
- 模板文件及书写流程不变；

## 相关推荐

对于插件开发时的自动刷新，可以用下边的插件：

「开发工具」BrowserSync - Z-Blog 应用中心

[https://app.zblogcn.com/?id=27743](<https://app.zblogcn.com/?id=27743> "「开发工具」BrowserSync - Z-Blog 应用中心")

另外，也可以用 rollup 方案：

> acgMora/dev\_rollup · 沉冰浮水/ZBP\_THEME
>
> [https://gitee.com/wdssmq/ZBP_THEME/tree/master/acgMora/dev_rollup](https://gitee.com/wdssmq/ZBP_THEME/tree/master/acgMora/dev_rollup "acgMora/dev\_rollup · 沉冰浮水/ZBP\_THEME")

↑ 不过我 bulma 版本比较旧，升级好像也挺麻烦的；（0.7.5 → 0.9.4）

## 截图

插件后台↓

![001](<https://app.zblogcn.com/zb_users/upload/2019/06/201906021559472696862113.png> "001.png")

工作路径↓

![002](<https://app.zblogcn.com/zb_users/upload/2019/06/201906021559472696716240.png> "002.png")

使用 SASS 开发↓

![003](<https://app.zblogcn.com/zb_users/upload/2019/06/201906021559472696594235.png> "003.png")

自动编译为 CSS↓

![004](<https://app.zblogcn.com/zb_users/upload/2019/06/201906021559472697998077.png> "004.png")

### 更新日志

2024-05-18：增加 PurgeCSS 支持，通过清理无用 CSS 以减小文件体积；

2023-09-13：LiveReload 改用 BrowserSync，可以实现反代开发站点；

2022-04-15：适配开发站点在 Docker 内的情况；

2021-07-29：更换 sass 组件，编译速度提升；

~~2021-03-13：增加 VSCode Browser Sync 支持，虽然这个仍然不太好用；增加自动检测 LiveReload；~~

2020-12-02：增加真 · LiveReload 支持；可配合 Node.js 工具或编辑器插件实现自动刷新；

2019-06-21：CSS 刷新优化；
