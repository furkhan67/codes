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
 *    XCache Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_XCache
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: XCache.php 376 2017-11-24 09:17:20Z phpmathan $
 */
class Zamp_Cache_XCache extends Zamp_Cache_Abstract {
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		if(!function_exists('xcache_set'))
			throw new Zamp_Cache_Exception('Could not Initialize XCache Cache', 26);
		elseif(!ini_get('xcache.var_size'))
			throw new Zamp_Cache_Exception('You must set the "xcache.var_size" variable to a value greater than 0');
		
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
		
		return xcache_isset($parent) ?$this->getCache($key, xcache_get($parent)) :false;
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

		return xcache_set($key, $data, $ttl);
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
		return xcache_unset($key);
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return bool|string
	 */
	public function clear() {
		for($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
			if(false === xcache_clear_cache(XC_TYPE_VAR, $i))
				return false;
		}

		return true;
	}
}
/** End of File **/