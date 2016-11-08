<?php

class ACloudAppSearchGuiding
{
    public function execute()
    {
        list($a) = ACloudSysCoreS::gp(array('a'));
        $action = ($a) ? $a.'Action' : 'searchAction';
        if ($action && method_exists($this, $action)) {
            $this->$action ();
        }
    }
    public function runAction()
    {
        $this->searchAction();
    }

    public function searchAction()
    {
        require_once Wind::getRealPath('ACLOUD:app.search.ACloudAppSearchDefine');
        $_Service = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        $appConfigs = ACloudSysCoreCommon::arrayCombination($_Service->getAppConfigsByAppId(APP_SEARCH_APPID), 'app_key', 'app_value');
        if ($appConfigs && isset($appConfigs ['search_domain']) && $appConfigs ['search_domain']) {
            header('Location:http://'.$appConfigs ['search_domain'].'/?'.$this->getSearchData());
            exit();
        }
        $unique = (isset($appConfigs ['search_unique']) && $appConfigs ['search_unique']) ? $appConfigs ['search_unique'] : ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER ['SERVER_NAME']);
        ACloudSysCoreCommon::refresh(sprintf('http://%s/?%s', APP_SEARCH_HOST, $this->getSearchData(array('n' => $unique))));
        exit();
    }

    public function getSearchData($params = array())
    {
        list($keyword, $type, $fid, $username) = ACloudSysCoreS::gp(array('keyword', 'type', 'fid', 'username'));
        $data = array();
        $data ['k'] = $keyword;
        $data ['type'] = $type;
        $data ['fid'] = intval($fid);
        $data ['username'] = $username;
        $data ['charset'] = ACloudSysCoreCommon::getGlobal('g_charset', Wekit::app()->charset);
        $data ['url'] = ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER ['SERVER_NAME']);
        $data ['sv'] = 'svp9';
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');

        return ACloudSysCoreHttp::httpBuildQuery(array_merge($data, $params));
    }

    public function getSearchPage($title, $url, $charset)
    {
        return <<<EOT
<!doctype html>
<html>
	<head>
		<meta charset="$charset">
		<title>$title</title>
		<style type="text/css">html,body{margin:0;padding:0;}</style>
	</head>
	<body>
		<iframe id="searchiframe" style="border:none;overflow:hidden;" width="100%" src="$url" frameborder="0" scrolling="no"></iframe>
	</body>
</html>
EOT;
    }

    public function proxyAction()
    {
        print_r($this->getProxyIframe(ACloudSysCoreCommon::getGlobal('g_charset', Wekit::app()->charset)));
        exit();
    }

    public function getProxyPage($charset)
    {
        return <<<EOT
<!doctype html>
<html>
	<head>
		<meta charset="$charset">
	</head>
	<body>
	</body>
	<script type="text/javascript">
		(function(){
			var getObj=function(id,parent){
				return (parent?parent:document).getElementById(id);
		    }
			var currHash="";
			var pParentFrame =top.document;
			setInterval(function(){
				var locationUrlHash =location.hash;
				if(typeof locationUrlHash!="undefined"){
					if(locationUrlHash!=currHash){
						if(locationUrlHash.split("#")[1]){
							var size=locationUrlHash.split("#")[1];
							var w=size.split("|")[0];
							var h=size.split("|")[1];
							pParentFrame.getElementById("searchiframe").style.height=h+"px";
						}
						currHash=locationUrlHash;
					}
				}
			},100)
		})();
	</script>
</html>
EOT;
    }
}
