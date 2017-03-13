<?php
/**
 * Windid工具库.
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidUtility.php 32085 2014-08-20 08:48:50Z gao.wanggao $
 */
class WindidUtility
{
    /**
     * 生成密码
     *
     * @param string $password 源密码
     * @param string $salt
     *
     * @return string
     */
    public static function buildPassword($password, $salt)
    {
        return md5(md5($password).$salt);
    }

    /**
     * 安全问题加密.
     *
     * @param string $question
     * @param string $answer
     *
     * @return bool
     */
    public static function buildQuestion($question, $answer)
    {
        return substr(md5($question.$answer), 8, 8);
    }

    public static function appKey($apiId, $time, $secretkey, $get, $post)
    {
        $array = ['windidkey', 'clientid', 'time', '_json', 'jcallback', 'csrf_token', 'Filename', 'Upload', 'token', '__data'];
        $str = '';
        ksort($get);
        ksort($post);
        foreach ($get as $k => $v) {
            if (in_array($k, $array)) {
                continue;
            }
            $str .= $k.$v;
        }
        foreach ($post as $k => $v) {
            if (in_array($k, $array)) {
                continue;
            }
            $str .= $k.$v;
        }

        return md5($time.$str.md5($apiId.'||'.$secretkey));
    }

    public static function buildMultiRequest($urls, $params = [])
    {
        $client = new \Guzzle\Http\Client();

        $result = [];
        foreach ($urls as $k => $url) {
            $request = $client->post($url, null, $params[$k]);
            $response = $client->send($request);
            $result[$k] = $response->getBody(true);
        }

        return $result;
    }

    public static function buildClientUrl($url, $notiFile)
    {
        $url = $url.'/'.$notiFile;
        $_url = parse_url($url);
        $query = isset($_url['query']) ? '&' : '?';

        return $url.$query;
    }

    public static function result($result)
    {
        if ($result instanceof WindidError) {
            return $result->getCode();
        }

        return $result ? WindidError::SUCCESS : WindidError::FAIL;
    }
}
