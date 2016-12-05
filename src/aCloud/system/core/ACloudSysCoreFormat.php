<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreFormat
{
    private $formats = array('json', 'serialize');

    public function format($data, $format, $charset)
    {
        $format = ACloudSysCoreS::inArray(strtolower($format), $this->formats) ? strtolower($format) : 'json';
        $action = $format.'Format';

        return $this->$action ($data, $charset);
    }

    public function jsonFormat($data, $charset)
    {
        $data = ACloudSysCoreCommon::convert($data, 'UTF-8', $charset);

        return ACloudSysCoreCommon::jsonEncode($data);
    }

    public function serializeFormat($data, $charset = '')
    {
        return serialize($data);
    }

    public function dataFlowXmlFormat($docs, $charset, $totalPages = null)
    {
        $docs = ACloudSysCoreCommon::convert($docs, 'UTF-8', $charset);
        require_once ACLOUD_PATH.'/system/core/ACloudSysCoreXml.php';

        return ACloudSysCoreXml::createXML($docs, 'UTF-8', $totalPages);
    }

    public function dataFlowXmlFormatWithDOMDocument($docs, $charset, $totalPages = null)
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xmlRoot = $xml->createElement('root');
        $xmlDocInfo = $xml->createElement('docinfo');
        $xmlDocInfo->setAttribute('charset', 'utf-8');

        !is_null($totalPages) && $xmlDocInfo->setAttribute('totalpages', $totalPages);

        $xmlDocs = $xml->createElement('docs');
        foreach ($docs as $doc) {
            $xmlDoc = $xml->createElement('doc');
            foreach ($doc as $k => $v) {
                $element = $xml->createElement($k);
                $element->appendChild($xml->createCDATASection($v));
                $xmlDoc->appendChild($element);
            }
            $xmlDocs->appendChild($xmlDoc);
        }

        $xmlRoot->appendChild($xmlDocInfo);
        $xmlRoot->appendChild($xmlDocs);
        $xml->appendChild($xmlRoot);
        $output = $xml->saveXML();

        return $output;
    }
}
