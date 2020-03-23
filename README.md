# XK-PHP

> 一个轻量的 PHP 框架

## 描述 Description

一个简单的 PHP "框架"？又或者可以说是应用模板。

ORM 使用 [Eloquent ORM](https://github.com/illuminate/database)，路由使用 [FastRoute](https://github.com/nikic/FastRoute)

大部分接口设计参考了 Laravel，采用门面模式 (Facade) 和单例模式(Instance) 的设计。

支持和 ReactJS 集成，其他前端框架也是可以的，不过我只写了 ReactJS 的配置。

支持中间件，不过我没有依据 PSR-15 标准进行设计，所以不兼容 PSR-15。

封装了请求和响应，同样没有依据 PSR-7 标准进行设计。

之所以不依据 PSR 标准进行设计是因为想把接口弄得和 Laravel 一样，而且相关标准的中间件，请求处理器已经有很多很好用的库了，本项目只是为了练手而已，所以就没依据相关标准进行设计。

由于首次写这种项目，所有会有很多设计缺陷和漏洞，不建议将该项目用于任何生产环境，仅用于学习就可以啦，若您有更好的建议或者发现不足的地方欢迎反馈。

写这个项目的目的是为了下一个博客项目准备的，因为不打算用任何框架，所以就有了这个项目 2333。

## 安装 Install

1. 克隆本仓库
```bash
git clone https://github.com/syfxlin/xkphp.git
```
2. 安装 PHP 依赖
```bash
composer install
```
3. 若您也需要使用 ReactJS，那么就需要安装 Node 依赖
```bash
yarn
```
4. 修改 .env 文件配置，并配置 API Key（可以通过 Laravel 生成，暂时还没制作生成脚本）
5. 运行数据库迁移
```bash
composer migration:up
```
6. 将 PHP 运行目录切换到 public，或者也可以通过 PHP 内置服务器启动。
```bash
php -S 0.0.0.0:8000 -t public
```

## 文档 Doc

暂无

## 维护者 Maintainer

XK-PHP 由[Otstar Lin](https://ixk.me/)和下列[贡献者](https://github.com/syfxlin/xkphp/graphs/contributors)的帮助下撰写和维护。

> Otstar Lin -[Personal Website](https://ixk.me/)·[Blog](https://blog.ixk.me/)·[Github](https://github.com/syfxlin)

## 许可证 License

![Lincense](https://img.shields.io/github/license/syfxlin/xkphp.svg?style=flat-square)

根据 Apache License 2.0 许可证开源。
