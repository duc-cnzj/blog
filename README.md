# duc's blog

一个花了两周左右时间完成的博客项目。

## 功能

- 目前支持的功能:
    - 游客评论
    - `markdown` 语法
    - `emoji` 🤨 表情
    - 文章编辑支持图片拖拽上传
    - 文章主题支持语法高亮
    - ~~暂时不支持搜索~~ 🔍

## Required

- docker-compose

## 安装

- `$ git clone --depth 1 git@github.com:DuC-cnZj/blog.git ducblog`
- `$ cd ducblog && cp .env.example .env` 复制配置文件
- `$ cd run && docker-compose up app` 进入到 run 目录下面 :smile:

done 🐳 👏

## 预览

- 博客主页 `http://localhost:8003`
- 博客后台 `http://localhost:8002`
- api `http://localhost:8001`


## License

MIT
