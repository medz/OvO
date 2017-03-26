#快速开始

你可以通过拷贝一份作为你开发的基础。可实现功能：独立插件页面，前台应用列表显示，后台应用管理菜单。也可以通过访问 站点/admin.php?m=appcenter&c=develop 建立（会直接安装好）,建立好的应用在src/extensions/下，请自行提取。
拷贝完要做的事：
	1、修改文件夹名为你的准备开发的应用别名，将会用于系统唯一标识该应用，
	   格式为(公司或个人名_英文别名)，例如phpwind_bank。
	2、修改Manifest.xml中alias值为你刚刚修改的文件夹名，搜索包里的代码把这个值替换为更改后的值
	3、开始开发吧～
	
#phpwind应用开发基础

##目录结构

以下教程由[phpwind9.0应用开发基础教程](http://www.phpwind.net/read/2572193)修改而来
 
应用包将会被安装到src/extensions下， 包名和应用别名应保持一致。
应用目录结构：

*admin    (可选，后台管理模块)
*controller （可选，前台管理模块）
*conf      （配置目录，存储配置信息，数据表安装信息，默认数据信息等，如果这些都没有该目录可选）
*service   （可选，数据服务目录，如果没有核心服务，例如你只输出hello那么该目录可选）
*template  （模板目录，可选）
Manifest.xml     （必须， 应用安装配置文件， 在应用安装包根目录下）
##Manifest.xml

在应用包中，Manifest.xml是一个必不可少的文件，它提供了安装的基本信息和配置，应用安装将依据它来进行安装流程。
在phpwind9的src/applications/appcenter/conf/Manifest.xml中详细描述了Manifest.xml文件每个配置的说明，这里不再详细展开说明，只是简单介绍下用到的基础配置。
```
<application>        
    <name>wind management</name>                  
<!-- 必填 应用名称，将显示在应用列表中 -->        
    <alias>alias</alias>                      
<!-- 必填 不可重复 应用别名，和目录包名保持一致， 用于系统唯一标识该应用，格式为(公司或个人名_英文别名)，例如phpwind_bank-->        
    <version>1.0.0</version>                     
 <!-- 必填 应用版本信息 -->        
    <pw-version>0.8.0</pw-version>                
<!-- 必填 支持的PW版本信息，多个版本用逗号分割 -->            
    <description>wind management</description>    <!-- 可选 应用描述信息，将显示在应用列表，用来描述应用特点、案例、使用方式等 -->        
    <charset>UTF-8</charset>                  
<!-- 必填 应用编码信息，标识该应用包的编码类型-->
    <website>http://path/to/homepage</website>      <!-- 可选 应用主页信息-->    
    <author-name></author-name>                 
 <!-- 可选 作者名称-->
    <author-email></author-email>         
 <!-- 可选 作者email-->
    <author-icon></author-icon>                     
 <!-- 可选 应用的图标地址-->
</application>
```
**注意上面的alias值，alias是应用包的唯一标识，为了防止应用冲突，我们强烈建议采用(公司或个人名_应用别名)的格式，否则将可能安装失败。**
##创建目录
在src/extensions下新建一个应用文件夹shilong_hello，（即你的alias哦），然后依据上面的目录结构规范在文件夹内创建controller目录、template目录、service目录以及Manifest.xml。
在Manifest.xml中填写下应用的基本信息，如下：
```
<?xml version="1.0" encoding="UTF-8"?>
<manifest>
<application>
<name>插件教程</name>

<!--插件唯一标识，不可重复，建议以（公司/个人名称）_(插件名称)定义  -->
<alias>shilong_hello</alias>
        
<version>1.0</version>
<pw-version>9.0</pw-version>
<type>app</type>
<description>插件教程实例</description>
<charset>utf-8</charset>
<logo>http://tiyan.phpwind.net/nextwind/attachment/1206/photo/thumb/mini/1307_db5b7fadfdd105a.jpg</logo>
<website>http://www.phpwind.net</website>
<author-name>long.shi</author-name>
<author-email>pw@aliyun-inc.com</author-email>
<author-icon>http://www.phpwind.net</author-icon>
</application>
    
<!--添加到前台应用-应用中心列表-->
<installation-service>appList</installation-service>
    
</manifest>
```
以上你可能注意到，我多加了一项配置为<installation-service>appList</installation-service>，它是可选的，作用是将把这个应用添加到前台应用-应用中心列表里。
接下来，研究过windframework的人应该会比较熟悉之后的流程了。

##service 层
在phpwind9.0中提供了丰富的服务接口，在这个例子里，我们想编写自己服务接口，并调用勋章、用户等提供的基本接口。
在service目录里新建一个服务类PwTestService，(服务名称自由定义)，编码过程如下：
```
class PwTestService {
    /**
     * 获取勋章日志
     *
     * @return array
     */
    public function getMedalLogList($page) {
        $logDs = Wekit::load('medal.PwMedalLog');
        list($start, $limit) = Pw::page2limit($page, 5);
        $logs = $logDs->getInfoList(0, 0, 0, $start, $limit);// 调用勋章日志接口
        $status = array(
            '1' => '正在申请',
            '2' => '已申请',
            '3' => '领取了',
            '4' => '领取了'
            );
        $uids = $medalids = array();
        foreach ($logs as $id => ;$v) {
            $uids[$v['uid']] = '';
            $medalids[$v['medal_id']] = '';
        }
        
        //调用用户接口
        $usernames = Wekit::load('user.PwUser')->fetchUserByUid(array_keys($uids));
        
        //调用勋章基本信息接口
        $medals = Wekit::load('medal.PwMedalInfo')->fetchMedalInfo(array_keys($medalids));
        
        //调用勋章服务
        $medalService = Wekit::load('SRV:medal.srv.PwMedalService');
        foreach ($logs as $id => ;$v) {
            $v['status'] = $status[$v['award_status']];
            $v['username'] = $usernames[$v['uid']]['username'];
            $v['medalname'] = $medals[$v['medal_id']]['name'];
            $v['medalpath'] = $medalService->getMedalImage($medals[$v['medal_id']]['path'], $medals[$v['medal_id']]['icon']);
        }
        return $logs;
    }
    
}
```
##controller层
在前台模块文件夹controller中新建IndexController类继承基类PwBaseController，并提供一个run()方法。它就是我们前台访问的入口。
```
public function run() {
        //待续

    }
```
上面我们编写了自己的服务接口，接下来在run()里我们将调用刚刚编写的服务接口获取数据。
```
/* (non-PHPdoc)
     * [url=u.php?uid=22279]@see[/url]    WindController::run()
     * 
     * 我的应用入口
     */
    public function run() {
        $logs = $this->_loadService()->getMedalLogList(1);
        
        /* 赋予模板中的变量 */
        $this->setOutput($logs, 'logs');
    }
    
    /**
     * 调用自身服务接口，路径为SRC:extensions.{alias}下
     *
     * @return PwTestService
     */
    private function _loadService() {
        return Wekit::load('SRC:extensions.shilong_hello.service.PwTestService');
    }
    ```
##template层
在phpwind9中，默认的模板文件名为controller_action，所以我们在template目录中新建模板文件index_run.htm
即上面的run()这个action将去渲染index_run.htm这个模板文件。你也可以在controller中 调用setTemplate()自定义模板名。编码内容如下：
```
<!doctype html>
<html>
<head>
<!-- 调用论坛公共头部 -->
<template source='TPL:common.head' load='true' />
<!-- 调用整站css文件 -->
<link rel="stylesheet" href="{[url=u.php?uid=132338]@theme[/url]  :css}/message.css" />
</head>
<body>
<div class="wrap">
<!-- 调用论坛公共头部 -->
<template source='TPL:common.header' load='true' />
    <div class="main_wrap">
        <div class="bread_crumb">
            <a href="{[url=u.php?uid=1848949]@url[/url]  :}" class="home">首页</a><em>></em>插件教程
        </div>
        <div class="main cc">
        <div class="box message_list" id="home_push_list">
        <!-- 调用当前目录的index_list.htm模板文件 -->
        <template source="index_list" />
        </div>
        <div class="more_loading"><a href="#" id="home_push_list_more">查看更多<em class="core_arrow"></em></a></div>
        </div>
    </div>
<!-- 调用全站统一底部文件 -->    
<template source='TPL:common.footer' load='true' />
</div>

</body>
</html>
```
##压缩包规范
1.**安装包格式是zip**
2.**安装包最大不能超过10m**
3.**安装包格式必须为gbk或者utf8。上传后会自动转换为gbk，utf8，big5 3种格式的包**
4.**安装包内的应用放置路径为：zip->应用包名（如：bank）->应用文件（必须有Manifest文件）**
5.**添加应用时类型选择了应用，Manifest中的type必须为app**
进入我们的src/extensions目录，把我们刚刚开发的shilong_hello文件夹打包成zip格式吧。**注意上面的d.4说明项，不可在shilong_hello文件夹内打包。**
至此，一个简单的插件已经成型了，想本地安装测试下吗？进入后台-应用管理-本地安装中上传我们刚刚的压缩包进行安装.
**安装前注意先删除原有的src/extensions/shilong_hello文件夹，否则文件夹名冲突，导致安装写入失败。**
