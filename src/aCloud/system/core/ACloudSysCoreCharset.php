<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreCharset
{
    public $TableHandle = 0;
    public $EncodeLang = '';
    public $IconvEnabled = false;
    public $TableIndex = array();
    public $TableEncode = array();
    public $IndexPoint = array(
        'GBKtoUTF8'     => 0,
        'GBKtoUNICODE'  => 0,
        'UTF8toGBK'     => 512,
        'BIG5toUTF8'    => 1024,
        'BIG5toUNICODE' => 1024,
        'UTF8toBIG5'    => 1536,
        'CHSStoCHST'    => 2048,
        'CHSTtoCHSS'    => 2560,
    );

    public function ACloudSysCoreCharset($SourceLang = '', $TargetLang = '', $ForceTable = false)
    {
        if ($SourceLang && $TargetLang) {
            $this->initConvert($SourceLang, $TargetLang, $ForceTable);
        }
    }

    public function initConvert($SourceLang, $TargetLang, $ForceTable)
    {
        if (($SourceLang = $this->_getCharset($SourceLang)) && ($TargetLang = $this->_getCharset($TargetLang)) && $SourceLang != $TargetLang) {
            $this->EncodeLang = $SourceLang.'to'.$TargetLang;
            $this->IconvEnabled = (function_exists('iconv') && !$ForceTable && !in_array($TargetLang, array('BIG5', 'CHSS', 'CHST'))) ? true : false;
            $this->IconvEnabled || is_resource($this->TableHandle) || $this->TableHandle = fopen($this->_getCharsetFilePath().'encode.table', 'r');
        }
    }

    public function _getCharset($lang)
    {
        switch (strtoupper(substr($lang, 0, 2))) {
            case 'SI':
                $lang = 'CHSS'; break;
            case 'TR':
                $lang = 'CHST'; break;
            case 'GB':
                $lang = 'GBK'; break;
            case 'UT':
                $lang = 'UTF8'; break;
            case 'UN':
                $lang = 'UNICODE'; break;
            case 'BI':
                $lang = 'BIG5'; break;
            default:
                $lang = '';
        }

        return $lang;
    }

    public function _UNICODEtoUTF8($c)
    {
        if ($c < 0x80) {
            $c = chr($c);
        } elseif ($c < 0x800) {
            $c = chr(0xC0 | $c >> 6).chr(0x80 | $c & 0x3F);
        } elseif ($c < 0x10000) {
            $c = chr(0xE0 | $c >> 12).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
        } elseif ($c < 0x200000) {
            $c = chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
        } elseif ($c < 0x4000000) {
            $c = chr(0xF8 | $c >> 24).chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
        } else {
            $c = chr(0xF8 | $c >> 30).chr(0xF8 | $c >> 24).chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
        }

        return $c;
    }

    public function _CHSUTF8toU($c)
    {
        switch (strlen($c)) {
            case 1:
                return ord($c);
            case 2:
                return ((ord($c[0]) & 0x3F) << 6) + (ord($c[1]) & 0x3F);
            case 3:
                return ((ord($c[0]) & 0x1F) << 12) + ((ord($c[1]) & 0x3F) << 6) + (ord($c[2]) & 0x3F);
            case 4:
                return ((ord($c[0]) & 0x0F) << 18) + ((ord($c[1]) & 0x3F) << 12) + ((ord($c[2]) & 0x3F) << 6) + (ord($c[3]) & 0x3F);
        }

        return 32;
    }

    public function _getTableIndex()
    {
        if (!isset($this->TableIndex[$this->EncodeLang])) {
            fseek($this->TableHandle, $this->IndexPoint[$this->EncodeLang]);
            $tmpData = fread($this->TableHandle, 512);
            $pFirstEncode = hexdec(bin2hex(substr($tmpData, 4, 4)));
            for ($i = 8; $i < 512; $i += 4) {
                $item = unpack('nkey/nvalue', substr($tmpData, $i, 4));
                if (isset($this->TableIndex[$this->EncodeLang][$item['key']])) {
                    break;
                }
                $this->TableIndex[$this->EncodeLang][$item['key']] = $pFirstEncode + $item['value'];
            }
        }
    }

    public function _CHStoUTF8($srcText)
    {
        $this->_getTableIndex();
        $tarText = '';
        for ($i = 0; $i < strlen($srcText); $i += 2) {
            $h = ord($srcText[$i]);
            if ($h > 127 && isset($this->TableIndex[$this->EncodeLang][$h])) {
                $l = ord($srcText[$i + 1]);
                if (!isset($this->TableEncode[$this->EncodeLang][$h][$l])) {
                    fseek($this->TableHandle, $l * 2 + $this->TableIndex[$this->EncodeLang][$h]);
                    $this->TableEncode[$this->EncodeLang][$h][$l] = $this->_UNICODEtoUTF8(hexdec(bin2hex(fread($this->TableHandle, 2))));
                }
                $tarText .= $this->TableEncode[$this->EncodeLang][$h][$l];
            } elseif ($h < 128) {
                $tarText .= $srcText[$i];
                $i--;
            }
        }

        return $tarText;
    }

    public function _CHSConvertST($srcText)
    {
        $this->_getTableIndex();
        $tarText = '';
        for ($i = 0; $i < strlen($srcText); $i += 2) {
            $h = ord($srcText[$i]);
            if ($h > 127 && isset($this->TableIndex[$this->EncodeLang][$h])) {
                $l = ord($srcText[$i + 1]);
                if (!isset($this->TableEncode[$this->EncodeLang][$h][$l])) {
                    fseek($this->TableHandle, $l * 2 + $this->TableIndex[$this->EncodeLang][$h]);
                    $tmpChar = fread($this->TableHandle, 2);
                    $this->TableEncode[$this->EncodeLang][$h][$l] = $tmpChar != 'PW' ? $tmpChar : $srcText[$i].$srcText[$i + 1];
                }
                $tarText .= $this->TableEncode[$this->EncodeLang][$h][$l];
            } elseif ($h < 128) {
                $tarText .= $srcText[$i];
                $i--;
            } else {
                $tarText .= $srcText[$i].$srcText[$i + 1];
            }
        }

        return $tarText;
    }

    public function _UTF8toCHS($srcText)
    {
        $this->_getTableIndex();
        $tarText = '';
        $i = 0;
        while ($i < strlen($srcText)) {
            $c = ord($srcText[$i++]);
            switch ($c >> 4) {
                case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
                    $tarText .= chr($c);
                break;
                case 12: case 13:
                    $c = (($c & 0x1F) << 6) | (ord($srcText[$i++]) & 0x3F);
                    $h = $c >> 8;
                    if (isset($this->TableIndex[$this->EncodeLang][$h])) {
                        $l = $c & 0xFF;
                        if (!isset($this->TableEncode[$this->EncodeLang][$h][$l])) {
                            fseek($this->TableHandle, $l * 2 + $this->TableIndex[$this->EncodeLang][$h]);
                            $this->TableEncode[$this->EncodeLang][$h][$l] = fread($this->TableHandle, 2);
                        }
                        $tarText .= $this->TableEncode[$this->EncodeLang][$h][$l];
                    }
                break;
                case 14:
                    $c = (($c & 0x0F) << 12) | ((ord($srcText[$i++]) & 0x3F) << 6) | ((ord($srcText[$i++]) & 0x3F));
                    $h = $c >> 8;
                    if (isset($this->TableIndex[$this->EncodeLang][$h])) {
                        $l = $c & 0xFF;
                        if (!isset($this->TableEncode[$h][$l])) {
                            fseek($this->TableHandle, $l * 2 + $this->TableIndex[$this->EncodeLang][$h]);
                            $this->TableEncode[$h][$l] = fread($this->TableHandle, 2);
                        }
                        $tarText .= $this->TableEncode[$h][$l];
                    }
                break;
            }
        }

        return $tarText;
    }

    public function _CHStoUNICODE($srcText, $SourceLang = '')
    {
        $tarText = '';
        if ($this->IconvEnabled && $SourceLang) {
            for ($i = 0; $i < strlen($srcText); $i += 2) {
                if (ord($srcText[$i]) > 127) {
                    $tarText .= '&#x'.dechex($this->_CHSUTF8toU(iconv($SourceLang, 'UTF-8', $srcText[$i].$srcText[$i + 1]))).';';
                } else {
                    $tarText .= $srcText[$i--];
                }
            }
        } else {
            $this->_getTableIndex();
            for ($i = 0; $i < strlen($srcText); $i += 2) {
                $h = ord($srcText[$i]);
                if ($h > 127 && isset($this->TableIndex[$this->EncodeLang][$h])) {
                    $l = ord($srcText[$i + 1]);
                    if (!isset($this->TableEncode[$this->EncodeLang][$h][$l])) {
                        fseek($this->TableHandle, $l * 2 + $this->TableIndex[$this->EncodeLang][$h]);
                        $this->TableEncode[$this->EncodeLang][$h][$l] = '&#x'.bin2hex(fread($this->TableHandle, 2)).';';
                    }
                    $tarText .= $this->TableEncode[$this->EncodeLang][$h][$l];
                } elseif ($h < 128) {
                    $tarText .= $srcText[$i--];
                }
            }
        }

        return $tarText;
    }

    public function Convert($srcText, $SourceLang = '', $TargetLang = '', $ForceTable = false)
    {
        if ($SourceLang && $TargetLang) {
            $this->initConvert($SourceLang, $TargetLang, $ForceTable);
        }

        switch ($this->EncodeLang) {
            case 'GBKtoUTF8':
                return $this->IconvEnabled ? iconv('GBK', 'UTF-8', $srcText) : $this->_CHStoUTF8($srcText); break;
            case 'BIG5toUTF8':
                return $this->IconvEnabled ? iconv('BIG5', 'UTF-8', $srcText) : $this->_CHStoUTF8($srcText); break;
            case 'UTF8toGBK':
                return $this->IconvEnabled ? iconv('UTF-8', 'GBK', $srcText) : $this->_UTF8toCHS($srcText); break;
            case 'UTF8toBIG5':
                return $this->_UTF8toCHS($srcText); break;
            case 'GBKtoUNICODE':
                return $this->_CHStoUNICODE($srcText, 'GBK'); break;
            case 'BIG5toUNICODE':
                return $this->_CHStoUNICODE($srcText, 'BIG5'); break;
            case 'CHSStoCHST': case 'CHSTtoCHSS':
                return $this->_CHSConvertST($srcText); break;
            case 'GBKtoBIG5': case 'BIG5toGBK':
                return $this->CHSConvert($srcText, $this->EncodeLang); break;
            default:
                return $srcText;
        }
    }

    public function CHSConvert($srcText, $SourceLang = 'GBK')
    {
        if (strtoupper(substr($SourceLang, 0, 3)) == 'GBK') {
            $handle = fopen($this->_getCharsetFilePath().'gb-big5.table', 'r');
        } else {
            $handle = fopen($this->_getCharsetFilePath().'big5-gb.table', 'r');
        }
        $encode = array();
        for ($i = 0; $i < strlen($srcText) - 1; $i++) {
            $h = ord($srcText[$i]);
            if ($h >= 160) {
                $l = ord($srcText[$i + 1]);
                if (!isset($encode[$h][$l])) {
                    fseek($handle, ($h - 160) * 510 + ($l - 1) * 2);
                    $encode[$h][$l] = fread($handle, 2);
                }
                $srcText[$i++] = $encode[$h][$l][0];
                $srcText[$i] = $encode[$h][$l][1];
            }
        }
        fclose($handle);

        return $srcText;
    }

    public function _getCharsetFilePath()
    {
        return ACloud_Pri_Core_Common::getDirName(__FILE__).'/encode/';
    }
}
