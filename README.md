# duc's blog

一个花了两周左右时间完成的博客项目。

[![Build Status](https://travis-ci.org/DuC-cnZj/blog.svg?branch=master)](https://travis-ci.org/DuC-cnZj/blog)
![StyleCI build status](https://github.styleci.io/repos/152713623/shield)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DuC-cnZj/blog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DuC-cnZj/blog/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/DuC-cnZj/blog/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/DuC-cnZj/blog/?branch=master)

## Features

- 目前支持的功能:
    - 游客评论
    - 支持 `markdown` 语法
    - 支持 `emoji` 表情，留言区输入符号 `:` 自动补全表情 🍭
    - 文章编辑支持图片拖拽上传
    - 文章主题支持语法高亮
    - 支持 ElasticSearch 搜索 🔍
    - 支持搜索结果高亮显示


## 💡 Required

- docker-compose


## 🚀 Installation

- `$ git clone --depth 1 https://github.com/DuC-cnZj/blog.git ducblog`
- `$ cd ducblog && cp .env.example .env` 复制配置文件
- `$ cd run && docker-compose up app` 进入到 run 目录下面，运行 app

Done 🐳


## 🔧  Configuration

> 如果不修改，那么使用默认配置

❗️ __在 run 目录下__, 添加 `.env` 文件, 并且在其中添加相关配置
- `BACK_PORT=your_port` 博客后台端口,  `defaut: 8002`
- `FRONT_PORT=your_port` 博客主页端口, `defaut: 8003`

❗️ __在根目录下的 `.env` 中__
- `ADMIN_MOBILE=18888XXXXXX` 设置博客后台管理员的账号 `default: 123456789`
- `ADMIN_PWD=secret` 设置博客后台管理员的密码 `default: secret`


## 🎓 Usage

运行程序命令
- `docker-compose up app`
- 在此命令下按 `ctrl + c` 即可退出


停止运行，退出程序
- `docker-compose down`

删除 app 产生的数据
- `docker volume rm run_appdata run_dbdata run_webdata run_esdata`


## 👀 Preview

> 如果在 `run` 目录下配置了 `.env` 文件, 请修改相应的端口

- 博客主页 `http://localhost:8003`
- 博客后台 `http://localhost:8002`
- api `http://localhost:8001`


## 📬 License

[MIT](http://opensource.org/licenses/MIT)
