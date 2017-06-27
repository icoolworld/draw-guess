# 关于你画我猜

> 说明：

这是一个你画我猜的小游戏，一个用户在画，其他用户打开猜的界面，可以实时看到对方正在画的内容。代码主要是简单的功能开发测试，慎用于生产环境。

> 核心原理:

通过websocket长连接TCP，实现全双工的数据实时传输，客户端，服务端可以互相发送消息，以实现你画我猜的游戏

> 技术要点：

基于nodej+npm+webpack+vue+websocket+swoole(php实现websocket通信)
通过npm可以方便安装第三方JS库
通过vue实现单页面应用
通过webpack打包模块
通过swoole实现服务端的websocket服务
相关前端代码借助vue-cli脚手架生成初始化开发环境 
`https://vuejs-templates.github.io/webpack/`
使用了vue-loader,来加载vue模块
`http://vue-loader.vuejs.org/zh-cn/`

```
npm install -g vue-cli
vue init webpack my-project
cd my-project
npm install
npm run dev
```


## 如何运行

git clone 本仓库

``` bash
# 安装依赖
npm install

# 运行测试环境,http://localhost:8080
npm run dev

# 构建输出供生产环境使用的代码
npm run build

# 构建输出供生产环境使用的代码，并生成分析报告
npm run build --report

# 单元测试
npm run unit

# run e2e tests
npm run e2e

# run all tests
npm test
```


## 启动websocket服务端

```
php websocket.php
```
