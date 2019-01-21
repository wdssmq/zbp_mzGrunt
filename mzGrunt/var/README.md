
## Grunt For Z-Blog

### 置百丈玄冰而崩裂，掷须臾池水而漂摇。

--------------------

使用说明要写完整要很大一坨呢，，而这个只有我自己可能会用的样子。。

Grunt怎么用自己百度，按需配置package.json文件。

-------------------

准备工作：

1. 克隆本项目到本地;
2. 复制以下文件到 \zb_users\theme\ 中

> - \config\
> - \s\
> - \Gruntfile.js
> - \package.json
>

3. 

基本命令：

npm install -g cnpm --registry=https://registry.npm.taobao.org

  —— 安装 cnpm（国内推荐）

cnpm install -g grunt-cli

  —— 安装 grunt

cnpm install

  —— 安装依赖包（项目路径下运行）

grunt new

  —— 创建子项目，在子项目文件夹中下边命令同样适用

grunt dist-css

  —— 编译并压缩CSS

grunt dist-js

  —— 合并并压缩js

grunt dist

  —— 调用dist-css、dist-js然后开启监听（自动编译Less或者刷新网页）

grunt test-css

  —— 检查并提示CSS语法错误，同样有test-js

--------------------

如果出现了奇怪的依赖错误，，比如:

`Error: Cannot find module 'coffeescript'`

请删除node_modules文件夹，然后执行:

`npm cache clean --force`

之后重新安装依赖包。

--------------------