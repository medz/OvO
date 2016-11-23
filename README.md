# phpwind fans - 目前最新：F 1.0.5
phpwind Fans版本是原本暂定的phpwind10版本而来。基于官方最新的phpwind9.0.1开发，同步官方所有代码的基础上进行改良和长期维护。
## 关于下载
点击[这里](https://github.com/medz/phpwind/releases)选择最新的版本
选择phpwind-fans-x.x.zip格式的文件点击即可下载。PS：注意格式！注意格式！注意格式！重要的事情说三遍！

## 首次安装
1. 将下载下来的文件解压，然后将phpwind-x文件夹下的内容放网站根目录。
2. 访问`http://你的域名/install.php`按照说明进行安装即可。

## 官方版本升级到phpwind fans
1.将下载下来的文件解压，然后将phpwind-x文件夹下的内容覆盖到你现在的phpwind9程序即可。
## 常见问题
1. 安装时显示：
```
you must set up the project dependencies,run the following commands:
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```
解决：你下载的是源码，请看上方 关于下载

2.插件应用zip包在系统后台-插件与模板-应用管理-本地上传后安装失败，而且所有插件应用都安装失败。

可能存在服务器zip包解压问题。解决：试试解压后直接上传到 网站根目录/src/extensions/下，到系统后台-插件与模板-应用管理-未安装应用-安装。

3. 环境配置问题

解决：请先百度

如果你在使用或者升级phpwind fans的时候发现什么问题，请随时提交issues给我们

你也可以加入QQ群：30568679 对升级时或者使用过程中上文没有提到的问题进行交流。

fans版本将会是一个长期维护版本，也会听取用户意见修改。有什么想法👏一点要告诉我们哦。

## 插件快速开发demo请看
[/src/extensions/demo](https://github.com/medz/phpwind/tree/master/src/extensions/demo)
