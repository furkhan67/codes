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
 *    APC Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_APCu
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: APCu.php 367 2016-02-22 09:48:41Z phpmathan $
 */
class Zamp_Cache_APCu extends Zamp_Cache_Abstract {
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		if(!function_exists('apcu_cache_info'))
			throw new Zamp_Cache_Exception('Could not Initialize APCu Cache', 26);
		
		parent::__construct($cache_config);
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
		
		return $this->getCache($key, apcu_fetch($parent));
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
		$ttl = (int) (isset($ttl) ?$ttl :$this->_cache_config['duration']);

		list($key, $data) = $this->getKeyValue($key, $data);

		return apcu_store($key, $data, $ttl);
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
		return apcu_delete($key);
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return bool|string
	 */
	public function clear() {
		return apcu_clear_cache();
	}
}
/** End of File **/
