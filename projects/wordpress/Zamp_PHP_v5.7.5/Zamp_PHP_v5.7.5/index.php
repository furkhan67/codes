<?php
/***
 * @version       $Revision: 367 $
 * @modifiedby    $LastChangedBy: phpmathan $
 * @lastmodified  $Date: 2016-02-22 15:18:41 +0530 (Mon, 22 Feb 2016) $
 */

require_once 'bootstrap.php';

// Initializing the Zamp SystemCore class.
require_once _PROJECT_PATH_.'Zamp/Zamp.php';
$Zampf = Zamp::getInstance('Zamp_SystemCore', [$config]);
$Zampf->initialize();

// getting the benchmark information and parsing it to the view.
$benchmark = $Zampf->getBenchmark();
//print"<PRE>";print_r($benchmark);

$processing_time = sprintf('%.4f', $benchmark['Total_Runtime']['total_time']).' <i>seconds</i>';

/*
// counting total number of runned queries.
$total_queries = count($Zampf->db->_runned_sql_queries);
if($total_queries > 0)
	$processing_time .= ' with '.$total_queries.(($total_queries > 1) ?' queries' :' query');
*/

$Zampf->_view->processing_time = $processing_time;
$Zampf->_view->memory_used = Zamp_General::byteCalculate(memory_get_usage());

// Display the view file.
$Zampf->_view->display($Zampf->_view->displayFile);

//print"Total Time Take :$processing_time<br/><PRE>";print_r($Zampf->db->_runned_sql_queries);
?>