<?php
/**
 * 	@version       $Revision: 372 $
 * 	@modifiedby    $LastChangedBy: phpmathan $
 * 	@lastmodified  $Date: 2016-11-30 01:33:42 +0530 (Wed, 30 Nov 2016) $
 */

class Processing extends Zamp_Object {
	public function preProcessing() {
		
	}
	
	public function postProcessing() {
		
	}
}

function debug($data, $exit=true, $dump=false) {
	echo "<PRE>";
	
	if($dump)
		var_dump($data);
	else
		print_r($data);
	
	echo "</PRE>";
	
	if($exit)
		cleanExit();
}

function jsonExit($data, $params=null) {
	if(!headers_sent())
		header('Content-Type: application/json');
	
	if(isset($params))
		$data = json_encode($data, $params);
	else
		$data = json_encode($data);
	
	echo($data);
	
	cleanExit();
}
/** End of File **/