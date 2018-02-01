<a name="list"></a>
# Fans 安装教程

- [环境要求](#manual-need)
- [下载安装](#manual-install)
- [优雅链接](#pretty-urls)

这里会讲如何在服务器中或者你的电脑中使用集成环境又或者手动编译环境来对 Fans 进行安装。

<a name="manual-need"></a>
## 环境要求

- [PHP & 拓展](#manual-need-php-ext)
- [PHP 函数](#manual-need-php-functions)
- [数据库](#manual-need-database)

### PHP & 拓展

- PHP 必须大于或等于 7.0
- 必须安装扩展 dom
- 必须安装扩展 fileinfo
- 必须安装扩展 gd
- 必须安装扩展 json
- 必须安装扩展 mbstring
- 必须安装扩展 openssl
- 必须安装 PDO
- 使用 MySQL 数据库则必须安装 PHP 扩展 pdo_mysql
- 使用 PostgreSQL 数据库则必须安装 PHP 扩展 pdo_pgsql
- 使用 SQLite 数据库则必须安装 PHP 拓展 pdo_sqlite
- 使用 SQL Server 数据库则必须安装 PHP 拓展 pdo_dblib

<a name="manual-need-php-functions"></a>
### PHP 函数

- `exec`
- `system`
- `scandir`
- `shell_exec`
- `proc_open`
- `proc_get_status`

> 这些是在 Console 环境下使用的，尽量确保你的系统没有禁止。

<a name="manual-need-database"></a>
### 数据库

- [MySQL](#manual-need-database-mysql)
- [MariaDB](#manual-need-database-mariadb)
- [PostgreSQL](#manual-need-database-pgsql)
- [SQLite](#manual-need-database-sqlite)
- [Microsoft SQL Server](#manual-need-database-sql-server)

> 推荐使用 PostgreSQL。

<a name="manual-need-database-mysql"></a>
##### MySql

使用 MySQL 建议使用 `>=5.7` 版本，必须 `>=5.6` 版本，如果你的是 5.6 版本，则自行解决索引过长导致的 SQL 执行错误问题。

<a name="manual-need-database-mariadb"></a>
##### MariaDB

使用 MariaDB 必须 `>=10.3` 版本，因为只有该版本是建立在 MySQL 5.6 & 5.7 之上的，得以支持 Emoji。

> 使用 MariaDB 按照 MySQL 进行配置即可。

<a name="manual-need-database-pgsql"></a>
##### PostgreSQL

PostgreSQL 数据库天然支持 Emoji，无需任何版本要求，但是我们还是建议使用最新版本的 PostgreSQL 稳定版本的以支持更完善的空间计算。

<a name="manual-need-database-sqlite"></a>
##### SQLite

首先，这个数据库不建议使用，因为这种轻量级的数据库适合在 App 中来解决数据本地化需求，服务器应用场景很小。

> 虽然 Fans 不允许使用 SQLite，但是您仍然可以在系统中使用该数据库，但是例如 Emoji 储存等问题自行解决。

<a name="manual-need-database-sql-server"></a>
##### Microsoft SQL Server

就像不推荐 SQLite 一样，我们同样不推荐 Microsoft SQL Server 除非你确定你的系统不适用 Emoji 那么你可以无顾虑的使用 Microsoft SQL Server 了，因为 Microsoft SQL Server 同样支持 utf8 字符集，却无法支持四位长度的 Emoji 字符。

> Fans 不建议使用 SQL Server，但是你仍然可以在系统中使用，出现的 emoji 存储问题自行解决。

<a name="manual-install"></a>
## 下载安装

需要软件：

- git
- [Composer](https://getcomposer.org/)

> 之后操作我们拟定目录为 `/var/www`

### 下载 Fans

```shell
git clone https://github.com/medz/phpwind ./fans
```

克隆完成后我们进入 `fans` 目录：

```shell
cd fans
```

我们看在已经下载了 Fans 源码，但是这不足以运行，因为 Fans 还依赖了其他的软件包，我们来安装依赖：

```shell
composer install
```
Or
```shell
php composer.phar install
```

<a name="manual-install-config"></a>
### 基础配置

安装完成依赖后，依旧无法运行，因为 Fans 需要使用数据库来存储一些数据。运行：`cp .env.example .env` 将 `.env.example` 复制一份命名为 `.env` 然后我们编辑这个文件，下面我简单描述各个配置用处：

| 配置名称 | 类型 | 描述 |
|:----:|:----:|----|
| APP_NAME | String | 应用名称 |
| APP_ENV | String | 运行环境，可选值 `local`, `production` |
| APP_DEBUG | Bool | 是否开启 debug，选择 `true` 或者 `false` |
| APP_LOG | String | 日志分卷，可选 `single`, `daily`, `syslog`, `errorlog` |
| APP_LOG_LEVEL | String | 记录日志级别，可选 `debug`, `info`, `warning`, `error`, 'critical' |
| APP_URL | String | 你的网站地址，此项 **必须** 设置，因为生成的所有地址都基于该地址 |
| DB_CONNECTION | String | 数据库连接方式，可选 `mysql`, `sqlite`, `pgsql`, `sqlsrv` |
| DB_HOST | String | 数据库连接地址 |
| DB_PORT | Integer | 数据库连接端口 |
| DB_DATABASE | String | 数据库名称 |
| DB_USERNAME | String | 数据库用户名 |
| DB_PASSWORD | String | 数据库用户密码 |
| DB_SOCKET | String | Mysql unix socket |
| CACHE_DRIVER | String | 缓存驱动，可选 `apc`, `array`, `database`, `file`, `memcached`, `redis` |
| MEMCACHED_PERSISTENT_ID | Any | Memcached 持久 ID |
| MEMCACHED_USERNAME | String | Memcached 用户名 |
| MEMCACHED_PASSWORD | String | Memcached 密码 |
| MEMCACHED_HOST | String | Memcached 连接 host |
| MEMCACHED_PORT | Integer | Memcached 连接端口 |
| MAIL_DRIVER | String | 邮件驱动，可选 `smtp`, `sendmail`, `mailgun`, `mandrill`, `ses`, `sparkpost`, `log`, `array` |
| MAIL_HOST | String | 邮件 SMTP 地址 |
| MAIL_PORT | Integer | 邮件 SMTP 端口 |
| MAIL_FROM_ADDRESS | String | 邮件发信地址 |
| MAIL_FROM_NAME | String | 邮件发信名称 |
| MAIL_ENCRYPTION | String | 邮件加密方式，可选 `tls`, `ssl` |
| MAIL_USERNAME | String | SMTP 验证用户名 |
| MAIL_PASSWORD | String | SMTP 验证密码 |
| QUEUE_DRIVER | String | 列队驱动，可选 `sync`, `database`, `beanstalkd`, `sqs`, `redis`, `null` |
| SESSION_DRIVER | String | Session 驱动，可选 `file`, `cookie`, `database`, `apc`, `memcached`, `redis`, `array` |
| SESSION_COOKIE | String | Session 存储在 Cokkie 的 key |
| SESSION_DOMAIN | String | Session 作用域 |

你可以最简单的配置，只需要配置 `APP_URL` 以及 `DB_*` 信息即可。

<a name="manual-install-database"></a>
### 生成数据表 & 填充数据

2. 生成唯一站点加密 Key：
```shell
php fans key:generate
```
2. 发布拓展包资源：
```shell
php fans vendor:publish --all
```
3. 迁移数据表：
```shell
php fans migrate
```
4. 填充数据：
```shell
php fans db:seed
```

至此，你已安装完成，你可以访问你的域名看看效果吧。

> 后台账号密码都是 `root`

### 目录权限 & 公开资源

大多数时候为了方便，我们在服务器都是使用 `root` 作为服务器管理账户，可能你在下载 Fans 的时候也适用的 `root` 账户，这会产生一个问题，php-fpm 或者 nginx 不是运行在 root 账户下的，导致你实际运行站点的时候会出现莫名其妙的错误，你应该将你整个 `fans` 目录指定给 php 或者 nginx 的运行角色：

| 目录 | 权限 |
|:----:|----|
| /* |  0755 |
| /storage | 0777 |

所有资源都存储在 `/storage` 目录下，所以你需要将公开资源链接到 `/public` 目录下，请务必执行：

```shell
php fans storage:link
```

<a name="pretty-urls"></a>
## 优雅链接

<a name="pretty-urls-nginx"></a>
### Nginx

如果你使用的是 Nginx，在你的站点配置中加入以下内容，它将会将所有请求都引导到 index.php 前端控制器：

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### Example

```
server {
    listen 443 ssl http2;
    server_name fans.io;
    ssl on;
    ssl_certificate /var/www/fans/fans.io.crt;
    ssl_certificate_key /var/www/fans/fans.io.key.unsecure;
    root /var/www/fans/public;
    index index.php index.html index.htm;

    location / {
         try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_pass php-upstream;
        fastcgi_index index.php;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

<a name="pretty-urls-apache"></a>
### Apache

在 Fans 中，已经在根目录 `/plulic` 中已经提供了 `.htaccess` 文件，其中已经为您配置好了优雅的地址配置。如果在你的 Apache 中不生效或者由其他位置提供配置，请设置：

```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

<a name="pretty-urls-caddy"></a>
### Caddy

Caddy 是一个小巧精悍的 http 软件，在开发环境，测试环境等下也是我们推荐使用的软件。因为它无需特殊的安装，无需特殊的配置，您只需下载一个 Caddy 运行文件，写一份你的站点配置即可运行。

```
rewrite { 
    to {path} {path}/ /index.php?{query}
}
```
