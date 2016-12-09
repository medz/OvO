<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreXml
{
    public function createXML($docs, $charset, $totalPages = null)
    {
        $charset = strtolower($charset);
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="'.$charset.'"?>';
        $xml .= '<root>';
        $xml .= '<docinfo '.self::createAttribute('version', ACloudSysCoreDefine::ACLOUD_XML_VERSION).' '.self::createAttribute('charset', $charset).' '.self::createAttribute('totalpages', $totalPages).' />';
        $xml .= '<docs>';
        $xml .= self::buildXML($docs);
        $xml .= '</docs>';
        $xml .= '</root>';

        return $xml;
    }

    public function createAttribute($key, $value)
    {
        return ($value !== null) ? sprintf('%s="%s"', $key, $value) : '';
    }

    public function buildXML($docs)
    {
        if (!$docs || !is_array($docs)) {
            return '';
        }
        $docStr = '';
        foreach ($docs as $doc) {
            $docStr .= '<doc>';
            foreach ($doc as $k => $v) {
                $docStr .= sprintf('<%s><![CDATA[%s]]></%s>', $k, $v, $k);
            }
            $docStr .= '</doc>';
        }

        return $docStr;
    }
}
