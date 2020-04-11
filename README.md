# XK-PHP

> 一个轻量的 PHP 框架

## 描述 Description

一个简单的 PHP "框架"？又或者可以说是应用模板。

ORM 使用 [Eloquent ORM](https://github.com/illuminate/database) ，路由使用 [FastRoute](https://github.com/nikic/FastRoute)

大部分的接口设计参考了 Laravel，采用门面模式 (Facade) ，单例模式 (Singleton) ，控制反转 (IoC) ，以及 依赖注入 (DI) 的设计，部分也用到了其他的设计模式。

集成了一个 IoC 容器，兼容 PSR-11

支持和 ReactJS 集成，其他前端框架也是可以的，不过我只写了 ReactJS 的配置。

支持 PSR-15 标准的中间件。

封装了请求和响应，根据 PSR-7 标准进行设计，但增加了类似 Laravel 的接口。

支持通过注解设置依赖注入，路由以及中间件

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

某些情况下 `laminas/laminas-httphandlerrunner` 可能无法正常安装，此时就需要先安装 `laminas/laminas-diactoros`

3. 若您也需要使用 ReactJS，那么就需要安装 Node 依赖

```bash
yarn
```

4. 修改 .env 文件配置，并配置 API Key（可以通过 Laravel 生成，暂时还没制作生成脚本）,或者执行以下代码生成

```bash
php -r "echo base64_encode(openssl_random_pseudo_bytes(32));"
```

5. 运行数据库迁移

```bash
composer migration:up
```

6. 将 PHP 运行目录切换到 public，或者也可以通过 PHP 内置服务器启动。

```bash
php -S 0.0.0.0:8000 -t public
```

## 文档 Doc

暂无

## 维护者 Maintainer

XK-PHP 由 [Otstar Lin](https://ixk.me/) 和下列 [贡献者](https://github.com/syfxlin/xkphp/graphs/contributors) 的帮助下撰写和维护。

> Otstar Lin -[Personal Website](https://ixk.me/) · [Blog](https://blog.ixk.me/) · [Github](https://github.com/syfxlin)

## 许可证 License

![Lincense](https://img.shields.io/github/license/syfxlin/xkphp.svg?style=flat-square)

根据 Apache License 2.0 许可证开源。
