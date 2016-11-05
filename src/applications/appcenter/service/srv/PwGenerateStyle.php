<?php
class PwGenerateStyle {
	protected $name;
	protected $description;
	protected $alias;
	protected $version;
	protected $pwversion;
	protected $baseDir;
	protected $defaultDir;
	protected $author;
	protected $email;
	protected $website;
	protected $style_type;
	
	/**
	 * @param field_type $style_type
	 */
	public function setStyle_type($style_type) {
		$this->style_type = $style_type;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param field_type $alias
	 */
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	 * @param field_type $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * @param field_type $pwversion
	 */
	public function setPwversion($pwversion) {
		$this->pwversion = $pwversion;
	}

	/**
	 * @param field_type $baseDir
	 */
	public function setBaseDir($baseDir) {
		$this->baseDir = $baseDir;
	}

	/**
	 * @param field_type $defaultDir
	 */
	public function setDefaultDir($defaultDir) {
		$this->defaultDir = $defaultDir;
	}

	/**
	 * @param field_type $manifest
	 */
	public function setManifest($manifest) {
		$this->manifest = $manifest;
	}

	/**
	 * @param field_type $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @param field_type $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param field_type $website
	 */
	public function setWebsite($website) {
		$this->website = $website;
	}

	public function generate() {
		$addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig(
			'style-type');
		$base = str_replace('/', '.', $addons[$this->style_type][1]);
		$this->defaultDir = Wind::getRealDir('THEMES:' . $base . '.default');
		if (!is_dir($this->defaultDir)) return new PwError('APPCENTER:generate.style.unsupport');
		$this->baseDir = Wind::getRealDir('THEMES:' . $base . '.' . $this->alias);
		if (is_dir($this->baseDir)) return new PwError('APPCENTER:alias.exist');
		WindFolder::mkRecur($this->baseDir);
		Wind::import('APPCENTER:service.srv.helper.PwSystemHelper');
		$writable = PwSystemHelper::checkWriteAble($this->baseDir . '/');
		if (!$writable) {
			return new PwError('APPCENTER:generate.copy.fail');
		}
		PwApplicationHelper::copyRecursive($this->defaultDir, $this->baseDir);
		$file = $this->baseDir . '/Manifest.xml';
		Wind::import('WIND:parser.WindXmlParser');
		$parser = new WindXmlParser();
		$manifest = $parser->parse($file);
		$manifest['application']['name'] = $this->name;
		$manifest['application']['alias'] = $this->alias;
		$manifest['application']['description'] = $this->description;
		$manifest['application']['version'] = $this->version;
		$manifest['application']['pw-version'] = $this->pwversion;
		$manifest['application']['website'] = $this->website;
		$manifest['application']['author-name'] = $this->author;
		$manifest['application']['author-email'] = $this->email;
		$parser = new WindXmlParser();
		$manifest = str_replace('><', ">\n\t<", $parser->parseToXml(array('manifest' => $manifest), Wind::getApp()->getResponse()->getCharset()));
		WindFile::write($this->baseDir . '/Manifest.xml', $manifest);
		return 'THEMES:' . $base . '.' . $this->alias;
	}
}

?>