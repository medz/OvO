<?php
/**
 * Zip文件解压工具类
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwExtractZip.php 7688 2012-04-10 11:22:26Z long.shi $
 * @package wind
 */

class PwExtractZip {
	
	const EOF_CENTRAL_DIRECTORY = 0x06054b50;//end of central directory record
	const LOCAL_FILE_HEADER = 0x04034b50;//Local file header
	const CENTRAL_DIRECTORY = 0x02014b50;//Central directory
	
	private $fileHandle = '';
	
	/**
	 * 解压缩一个文件
	 * @param $zipfile string 待解压的ZIP文件包
	 * @param $zipfile string 获取单个压缩包中的文件名
	 * @return array 解压缩后的数据，其中包括时间、文件名、数据
	 */
	public function extract($zipPack, $aFile='') {
		if (!$zipPack || !is_file($zipPack)) return false;
		$extractedData = array();
		$this->fileHandle = fopen($zipPack, 'rb');
		$filesize = sprintf('%u', filesize($zipPack));
		$EofCentralDirData = $this->_findEOFCentralDirectoryRecord($filesize);
		if (!is_array($EofCentralDirData)) return false;
		$centralDirectoryHeaderOffset = $EofCentralDirData['centraldiroffset'];
		for ($i = 0; $i < $EofCentralDirData['totalentries']; $i++) {
			rewind($this->fileHandle);
			fseek($this->fileHandle, $centralDirectoryHeaderOffset);
			$centralDirectoryData = $this->_readCentralDirectoryData();
			if (!is_array($centralDirectoryData)) {
				$centralDirectoryHeaderOffset += 46;
				continue;
			}
			$centralDirectoryHeaderOffset += 46 + $centralDirectoryData['filenamelength'] + $centralDirectoryData['extrafieldlength'] + $centralDirectoryData['commentlength'];
			if (substr($centralDirectoryData['filename'], -1) === '/') continue;
			
			if (!$aFile) {
				$data = $this->_readLocalFileHeaderAndData($centralDirectoryData);
				if ($data === false) continue;
				$extractedData[$i] = array(
					'filename' => $centralDirectoryData['filename'],
					'timestamp' => $centralDirectoryData['time'],
					'data' => $data,
				);
			} elseif ($aFile === $centralDirectoryData['filename']) {
				$data = $this->_readLocalFileHeaderAndData($centralDirectoryData);
				if ($data === false) return false;
				$extractedData = $data;
				break;
			}
		}
		fclose($this->fileHandle);
		return $extractedData;
	}
	
	public function getFileLists($zipPack) {
		if (!$zipPack || !is_file($zipPack)) return false;
		$extractedData = array();
		$this->fileHandle = fopen($zipPack, 'rb');
		$filesize = sprintf('%u', filesize($zipPack));
		$EofCentralDirData = $this->_findEOFCentralDirectoryRecord($filesize);
		if (!is_array($EofCentralDirData)) return false;
		$centralDirectoryHeaderOffset = $EofCentralDirData['centraldiroffset'];
		for ($i = 0; $i < $EofCentralDirData['totalentries']; $i++) {
			rewind($this->fileHandle);
			fseek($this->fileHandle, $centralDirectoryHeaderOffset);
			$centralDirectoryData = $this->_readCentralDirectoryData();
			if (!is_array($centralDirectoryData)) {
				$centralDirectoryHeaderOffset += 46;
				continue;
			}
			$centralDirectoryHeaderOffset += 46 + $centralDirectoryData['filenamelength'] + $centralDirectoryData['extrafieldlength'] + $centralDirectoryData['commentlength'];
			if (substr($centralDirectoryData['filename'], -1) === '/') {
				$extractedData['folder'][$i] = $centralDirectoryData['filename'];
			} else {
				$extractedData['file'][$i] = $centralDirectoryData['filename'];
			}
		}
		fclose($this->fileHandle);
		return $extractedData;
	}
	
	public function extract2($zipPack, $target) {
		if (!$zipPack || !is_file($zipPack)) return false;
		$extractedData = array();
		$target = rtrim($target, '/');
		WindFolder::mkRecur($target, 0777);
		$this->fileHandle = fopen($zipPack, 'rb');
		$filesize = sprintf('%u', filesize($zipPack));
		$EofCentralDirData = $this->_findEOFCentralDirectoryRecord($filesize);
		if (!is_array($EofCentralDirData)) return false;
		$centralDirectoryHeaderOffset = $EofCentralDirData['centraldiroffset'];
		for ($i = 0; $i < $EofCentralDirData['totalentries']; $i++) {
			rewind($this->fileHandle);
			fseek($this->fileHandle, $centralDirectoryHeaderOffset);
			$centralDirectoryData = $this->_readCentralDirectoryData();
			if (!is_array($centralDirectoryData)) {
				$centralDirectoryHeaderOffset += 46;
				continue;
			}
			$centralDirectoryHeaderOffset += 46 + $centralDirectoryData['filenamelength'] + $centralDirectoryData['extrafieldlength'] + $centralDirectoryData['commentlength'];
			if (substr($centralDirectoryData['filename'], -1) === '/') {
				WindFolder::mkRecur($target . '/' . $centralDirectoryData['filename'], 0777);
				$extractedData['folder'][$i] = $centralDirectoryData['filename'];
				continue;
			} else {
				$data = $this->_readLocalFileHeaderAndData($centralDirectoryData);
				if ($data === false) continue;
				WindFile::write($target . '/' . $centralDirectoryData['filename'] , $data);
				$extractedData['file'][$i] = $centralDirectoryData['filename'];
			}
		}
		fclose($this->fileHandle);
		return $extractedData;
	}
	
	/**
	 * 取得压缩数据中的'Local file header'区块跟压缩的数据
	 * @param $centralDirectoryData array 'Central directory' 区块数据
	 * @return array
	 */
	private function _readLocalFileHeaderAndData($centralDirectoryData) {
		fseek($this->fileHandle, $centralDirectoryData['localheaderoffset']);
		$localFileHeaderSignature = unpack('Vsignature', fread($this->fileHandle, 4));
		if ($localFileHeaderSignature['signature'] != PwExtractZip::LOCAL_FILE_HEADER) return false;
		$localFileHeaderData = fread($this->fileHandle, 26);
		$localFileHeaderData = unpack('vextractversion/vflag/vcompressmethod/vmodtime/vmoddate/Vcrc/Vcompressedsize/Vuncompressedsize/vfilenamelength/vextrafieldlength', $localFileHeaderData);
		$localFileHeaderData['filenamelength'] && $localFileHeaderData['filename'] = fread($this->fileHandle, $localFileHeaderData['filenamelength']);
		$localFileHeaderData['extrafieldlength'] && $localFileHeaderData['extrafield'] = fread($this->fileHandle, $localFileHeaderData['extrafieldlength']);
		if (!$this->_checkLocalFileHeaderAndCentralDir($localFileHeaderData, $centralDirectoryData)) return false;
		//文件加密过
		if ($localFileHeaderData['flag'] & 1) return false;
		$compressedData = fread($this->fileHandle, $localFileHeaderData['compressedsize']);
		$data = $this->_unCompressData($compressedData, $localFileHeaderData['compressmethod']);
		//crc32 校验不一致或长度不一致
		if (crc32($data) != $localFileHeaderData['crc'] || strlen($data) != $localFileHeaderData['uncompressedsize']) return false;
		return $data;
	}
	
	/**
	 * 解压被压缩的数据
	 * @param $data string 被压缩的数据
	 * @param $compressMethod int 压缩的方式
	 * @return string 解压后的数据
	 */
	private function _unCompressData($data, $compressMethod) {
		if (!$compressMethod) return $data;
		switch ($compressMethod) {
			case 8 : // compressed by deflate
				$data = gzinflate($data);
				break;
			default :
				return false;
				break;
		}
		return $data;
	}
	
	/**
	 * 校验 'Local file header' 跟 'Central directory'
	 * @param unknown_type $localFileHeaderData
	 * @param unknown_type $centralDirectoryData
	 * @return bool
	 */
	private function _checkLocalFileHeaderAndCentralDir($localFileHeaderData, $centralDirectoryData) { 
		return true; //暂时不验证，有需要时可扩展
	}
	
	/**
	 * 读取'Central directory' 区块数据
	 * @return string
	 */
	private function _readCentralDirectoryData() {
		$centralDirectorySignature = unpack('Vsignature', fread($this->fileHandle, 4)); // 'Central directory' 区块的标记
		if ($centralDirectorySignature['signature'] != PwExtractZip::CENTRAL_DIRECTORY) return false;
		$centralDirectoryData = fread($this->fileHandle, 42); // 'Central directory' 区块除标记, file name, extra field, file comment 外的数据
		$centralDirectoryData = unpack('vmadeversion/vextractversion/vflag/vcompressmethod/vmodtime/vmoddate/Vcrc/Vcompressedsize/Vuncompressedsize/vfilenamelength/vextrafieldlength/vcommentlength/vdiskstart/vinternal/Vexternal/Vlocalheaderoffset', $centralDirectoryData);
		$centralDirectoryData['filenamelength'] && $centralDirectoryData['filename'] = fread($this->fileHandle, $centralDirectoryData['filenamelength']); //读取文件名
		$centralDirectoryData['extrafieldlength'] && $centralDirectoryData['extrafield'] = fread($this->fileHandle, $centralDirectoryData['extrafieldlength']); //读取extra field
		$centralDirectoryData['commentlength'] && $centralDirectoryData['comment'] = fread($this->fileHandle, $centralDirectoryData['commentlength']); //读取 file comment
		$centralDirectoryData['time'] = $this->_recoverFromDosFormatTime($centralDirectoryData['modtime'], $centralDirectoryData['moddate']); //读取时间信息
		return $centralDirectoryData;
	}
	
	/**
	 * 读取'end of central directory record'区块数据
	 * @param $filesize int 文件大小
	 * @return string 
	 */
	private function _findEOFCentralDirectoryRecord($filesize) {
		fseek($this->fileHandle, $filesize - 22); // 'End of central directory record' 一般在没有注释的情况下位于该位置
		$EofCentralDirSignature = unpack('Vsignature', fread($this->fileHandle, 4));
		if ($EofCentralDirSignature['signature'] != PwExtractZip::EOF_CENTRAL_DIRECTORY) { // 'End of central directory record' 不在末尾22个字节的位置，即有注释的情况
			$maxLength = 65535 + 22; //'End of central directory record' 区块最大可能的长度，因为保存注释长度的区块的长度为2字节，2个字节最大可保存的长度是65535，即0xFFFF。22为'End of central directory record' 除去注释后的长度
			$maxLength > $filesize && $maxLength = $filesize; //最大不能超多整个文件的大小
			fseek($this->fileHandle, $filesize - $maxLength);
			$searchPos = ftell($this->fileHandle);
			while ($searchPos < $filesize) {
				fseek($this->fileHandle, $searchPos);
				$sigData = unpack('Vsignature', fread($this->fileHandle, 4));
				if ($sigData['signature'] == PwExtractZip::EOF_CENTRAL_DIRECTORY) {
					break;
				}
				$searchPos++;
			}
		}
		$EofCentralDirData = unpack('vdisknum/vdiskstart/vcentraldirnum/vtotalentries/Vcentraldirsize/Vcentraldiroffset/vcommentlength', fread($this->fileHandle, 18)); // 'End of central directory record'区块除signature跟注释外的数据
		$EofCentralDirData['commentlength'] && $EofCentralDirData['comment'] = fread($this->fileHandle, $EofCentralDirData['commentlength']);
		return $EofCentralDirData;
	}
	
	/**
	 * 还原DOS格式的时间为时间戳
	 * @param $time
	 * @param $date
	 * @return int
	 */
	private function _recoverFromDosFormatTime($time, $date) {
		$year = (($date & 0xFE00) >> 9) + 1980;
		$month = ($date & 0x01E0) >> 5;
		$day = $date & 0x001F;
		$hour = ($time & 0xF800) >> 11;
		$minutes = ($time & 0x07E0) >> 5;
		$seconds = ($time & 0x001F)*2;
		return mktime($hour, $minutes, $seconds, $month, $day, $year);
	}
}