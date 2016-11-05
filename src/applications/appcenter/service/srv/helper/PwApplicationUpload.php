<?php
Wind::import('LIB:upload.PwUpload');
/**
 * 應用上傳工具類
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwApplicationUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package appcenter.service.srv.helper
 */
class PwApplicationUpload {
	
	public $ftype = array('zip' => 10000);
	public $dir = ATTACH_PATH;
	
	public function execute() {
		$uploaddb = array();
		foreach ($_FILES as $key => $value) {
			if (!$this->isUploadedFile($value['tmp_name'])) {
				continue;
			}
			$file = new PwUploadFile($key, $value);
			if (($result = $this->checkFile($file)) !== true) {
				return $result;
			}
			$file->filename = $this->filterFileName($this->getSaveName($file));
			$file->savedir = Pw::getTime() . '/';
			$file->source = $this->dir . $file->savedir . $file->filename;
	
			if (!$this->moveUploadedFile($value['tmp_name'], $file->source)) {
				return new PwError('upload.fail');
			}
			if (($result = $file->operate($this->bhv, $this->store)) !== true) {
				return $result;
			}
			$uploaddb[] = $file->getInfo();
		}
		return $uploaddb;
	}
	
	public function getSaveName(PwUploadFile $file) {
		$filename = substr(md5(Pw::getTime() . $file->id . WindUtility::generateRandStr(8)), 10, 15);
		return $filename . '.' . $file->ext;
	}
	
	public function moveUploadedFile($tmp_name, $filename) {
		if (strpos($filename, '..') !== false || strpos($filename, '.php.') !== false || preg_match('/\.php$/i', $filename)) {
			return false;
		}
		WindFolder::mkRecur(dirname($filename));
		if (function_exists("move_uploaded_file") && @move_uploaded_file($tmp_name, $filename)) {
			@chmod($filename, 0777);
			return true;
		} elseif (@copy($tmp_name, $filename)) {
			@chmod($filename, 0777);
			return true;
		} elseif (is_readable($tmp_name)) {
			file_put_contents($filename, file_get_contents($tmp_name));
			if (file_exists($filename)) {
				@chmod($filename, 0777);
				return true;
			}
		}
		return false;
	}

	/**
	 * @param PwUploadFile $file
	 */
	public function checkFile($file) {
		if (!$file->ext || !isset($this->ftype[$file->ext])) {
			return new PwError(array('upload.ext.error', array('{ext}' => '.' . $file->ext)));
		}
		if ($file->size < 1) {
			return new PwError('upload.size.less');
		}
		if ($file->size > $this->ftype[$file->ext] * 1024) {
			return new PwError(array('upload.size.over', array('{size}' => $this->ftype[$file->ext])));
		}
		return true;
	}
	
	public function isUploadedFile($tmp_name) {
		if (!$tmp_name || $tmp_name == 'none') {
			return false;
		} elseif (function_exists('is_uploaded_file') && !is_uploaded_file($tmp_name) && !is_uploaded_file(str_replace('\\\\', '\\', $tmp_name))) {
			return false;
		} else {
			return true;
		}
	}
	
	public function filterFileName($filename) {
		return preg_replace('/\.(php|asp|jsp|cgi|fcgi|exe|pl|phtml|dll|asa|com|scr|inf)$/i', ".scp_\\1" , $filename);
	}
}
class PwUploadFile {

	public $key;
	public $id;
	public $attname;
	public $name;
	public $size;
	public $type = 'zip';
	public $ifthumb = 0;
	public $filename;
	public $savedir;
	public $fileuploadurl = '';
	public $ext;
	public $source;

	protected $_thumb = array();

	public function __construct($key, $value) {
		list($t, $i) = explode('_', $key);
		$this->id = intval($i);
		$this->attname = $t;
		$this->name = $value['name'];
		$this->size = intval($value['size']);
		$this->ext  = strtolower(substr(strrchr($this->name, '.'), 1));
	}

	public function getInfo() {
		return array(
			'id' => $this->id,
			'attname' => $this->attname,
			'name' => $this->name,
			'size' => $this->size,
			'type' => $this->type,
			'ifthumb' => $this->ifthumb,
			'fileuploadurl' => $this->fileuploadurl,
			'ext' => $this->ext
		);
	}

	public function operate($bhv, $store) {
		$this->size = ceil(filesize($this->source) / 1024);
		$this->fileuploadurl = $this->savedir . $this->filename;
		return true;
	}

}
?>