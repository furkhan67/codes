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
 *    NoCache Cache class
 *
 * @category Zamp
 * @package Zamp_Cache_Abstract
 * @subpackage Zamp_Cache_NoCache
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: NoCache.php 376 2017-11-24 09:17:20Z phpmathan $
 */
class Zamp_Cache_NoCache extends Zamp_Cache_Abstract {
	/**
	 *    construct method
	 *
	 * @access public
	 * @param array $cache_config
	 */
	public function __construct($cache_config) {
		parent::__construct($cache_config);
	}

	/**
	 *    fetch the cache data for given identifier. If cache not set the returns boolean false.
	 *
	 * @access public
	 * @param string $key
	 * @return false
	 */
	public function fetch($key) {
		return false;
	}

	/**
	 *    store the data into the Cache
	 *
	 * @access public
	 * @param string $key identifier name
	 * @param mixed $data
	 * @param integer $ttl Cache life time. If not given it taken from the application configuration. [default: 3600]
	 * @return false
	 */
	public function store($key, $data, $ttl = null) {
		return false;
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
		return false;
	}

	/**
	 *    clear all the Cache data
	 *
	 * @access public
	 * @return false
	 */
	public function clear() {
		return false;
	}
}
/** End of File **/