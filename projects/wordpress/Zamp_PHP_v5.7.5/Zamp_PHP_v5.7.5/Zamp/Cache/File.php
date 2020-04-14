<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Cache_Abstract
 */

/**
 *    File Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_File
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: File.php 376 2017-11-24 09:17:20Z phpmathan $
 */
class Zamp_Cache_File extends Zamp_Cache_Abstract {
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		if(empty($cache_config['path']))
			$cache_config['path'] = _PROJECT_PATH_.'data/tmp/cache/'.getConf('viewTemplate/template_name');
		
		$cache_config['path'] = rtrim($cache_config['path'], "/ ");
		
		if(!is_dir($cache_config['path'])) {
			if(!mkdir($cache_config['path'], 0775, true))
				throw new Zamp_Cache_Exception('Cache Directory `<b>'.$cache_config['path'].'</b>` can not accessible.', 26);
		}
		
		parent::__construct($cache_config);
	}

	/**
	 *    store the data into the Cache
	 *
	 * @access public
	 * @param string $key identifier name
	 * @param mixed $data
	 * @param integer $ttl Cache life time. If not given it taken from the application configuration. [default: 3600]
	 * @return bool|string
	 */
	public function store($key, $data, $ttl = null) {
		$ttl = isset($ttl) ?$ttl :(($this->_cache_config['duration']) ?$this->_cache_config['duration'] :3600);
		$ttl = (int) $ttl;
		
		list($key, $data) = $this->getKeyValue($key, $data);
		
		$h = fopen($this->_getFileName($key), 'a+');
		if(!$h)
			throw new Zamp_Cache_Exception('Could not open to cache file');
		
		flock($h, LOCK_EX);
		fseek($h, 0);
		ftruncate($h, 0);
		
		$data = serialize([
			time() + $ttl,
			$data
		]);
		
		if(!fwrite($h, $data))
			throw new Zamp_Cache_Exception('Could not write to cache');

		fclose($h);

		return true;
	}

	/**
	 *    fetch the cache data for given identifier. If cache not set the returns boolean false.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function fetch($key) {
		list($parent) = $this->getKeyValue($key);
		
		$filename = $this->_getFileName($parent);

		try {
			if(!$h = fopen($filename, 'r'))
				return false;
		}
		catch(Exception $e) {
			return false;
		}
		
		flock($h, LOCK_SH);
		$data = file_get_contents($filename);
		fclose($h);

		$data = unserialize($data);

		if(!$data || time() > $data[0]) {
			unlink($filename);

			return false;
		}

		return $this->getCache($key, $data[1]);
	}

	/**
	 *    delete the Cache for given identifier
	 *
	 * @access protected
	 * @since 1.4.7
	 * @param string $key
	 * @return bool|string
	 */
	protected function deleteCache($key) {
		$filename = $this->_getFileName($key);
		
		if(file_exists($filename))
			return unlink($filename);
		else
			return false;
	}

	/**
	 *    get the cache filename
	 *
	 * @access private
	 * @param string $key
	 * @return string
	 */
	private function _getFileName($key) {
		return $this->_cache_config['path'].'/'.md5($key);
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return bool|string
	 */
	public function clear() {
		$cache_folder = $this->_cache_config['path'];
		if($dh = opendir($cache_folder)) {
			while(($filename = readdir($dh)) !== false) {
				if($h = fopen($filename, 'r')) {
					flock($h, LOCK_SH);
					$data = file_get_contents($filename);
					fclose($h);
					$data = unserialize($data);
					if(!$data || time() > $data[0])
						unlink($filename);
				}
			}
			closedir($dh);
		}

		return true;
	}
}
/** End of File **/