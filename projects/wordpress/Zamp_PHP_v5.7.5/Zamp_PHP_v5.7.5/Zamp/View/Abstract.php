<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_View_Abstract
 */

/**
 *    Zamp view Abstract class
 *
 * @category Zamp_View
 * @package Zamp_View_Abstract
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Abstract.php 381 2018-12-25 13:09:18Z phpmathan $
 */
abstract class Zamp_View_Abstract {
	/**
	 * @access public
	 * @var string $_view_template_extension
	 */
	public $_view_template_extension = false;
	/**
	 * @access public
	 * @var string $templateDir ;
	 */
	public $templateDir;

	/**
	 *    construct method
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$config = isset(Zamp::$core->_config['viewTemplate']) ?Zamp::$core->_config['viewTemplate'] :[];
		
		if(isset($config['prefix']))
			$this->_view_template_extension = $config['prefix'];
		
		$this->templateDir = _APPLICATION_CORE_.'view/'.$config['template_name'];
		
		if(!is_dir($this->templateDir))
			throw new Zamp_Exception("View Template Folder <font color='blue'>{$this->templateDir}</font> not found!", 5);
	}

	/**
	 *    setting default view data
	 *
	 * @access public
	 * @param object $engine_instance
	 * @return void
	 */
	public function setDefaultViewData($engine_instance) {
		$Zampf = Zamp::$core;
		
		$displayFile = isset($Zampf->_config['viewMainTemplateFile']) ?$Zampf->_config['viewMainTemplateFile'] :'index'.$this->_view_template_extension;
		$mainTemplateFile = $this->templateDir.'/'.$displayFile;

		if(!isFileExists($mainTemplateFile))
			throw new Zamp_Exception("View main template file '<font color='blue'>$mainTemplateFile</font> not found!'", 6);

		$viewTemplate = $Zampf->_config['viewTemplate']['template_name'];

		$engine_instance->assign([
			'displayFile' => $displayFile,
			'current_url' => $Zampf->_current_url,
			'root_url' => $Zampf->_root_url,
			'application_url' => $Zampf->_application_url,
			'view_template_name' => $viewTemplate
		]);

		$assets = [
            'css' => 1,
            'images' => 1,
            'js' => 1,
            'libs' => 1,
        ];

		$directories = scandir(_PROJECT_PATH_);

		foreach($directories as $dir_name) {
			if(!isset($assets[$dir_name]))
				continue;

			if(is_dir($dir_name)) {
				if($dir_name <> 'libs')
					$engine_instance->assign("_$dir_name", $Zampf->_root_url.$dir_name.'/'.$viewTemplate);
				else
					$engine_instance->assign("_$dir_name", $Zampf->_root_url.$dir_name);
			}
		}

		if(SET_ZAMP_HEADER)
			header("X-Powered-By: Zamp PHP/".ZAMP_VERSION);
	}
}
/** End of File **/
