<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Timer
 */

/**
 *    Timer Class. This package used for benchmarking
 *
 * @category Zamp
 * @package Zamp_Timer
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Timer.php 367 2016-02-22 09:48:41Z phpmathan $
 */
class Zamp_Timer {
	/**
	 * @access public
	 * @var array $timer
	 */
	public $timer;

	/**
	 *    Set the start timer name
	 *
	 * @param string $name (optional)
	 * @access public
	 */
	public function startTimer($name = "") {
		$this->resetTimer($name);
		$this->timer[$name]['start'] = $this->setTimer();
	}

	/**
	 *    Set the stop timer name
	 *
	 * @param string $name (optional)
	 * @access public
	 */
	public function stopTimer($name = "") {
		if(!isset($this->timer[$name]['end']))
			$this->timer[$name]['end'] = $this->setTimer();

		$this->timer[$name]['total_time'] = $this->getEndTime($name) - $this->getStartTime($name);
	}

	/**
	 *    Set the middle processing timer name
	 *
	 * @param string $name (optional)
	 * @access public
	 */
	public function stretchTimer($name = "") {
		$this->timer[$name]['end'] = $this->setTimer();
		$this->timer[$name]['total_time'] = $this->getEndTime($name) - $this->getStartTime($name);
	}

	/**
	 *    Setting the timer
	 *
	 * @access public
	 * @return float
	 */
	public function setTimer() {
		return microtime(true);
	}

	/**
	 *    Get the processing start time
	 *
	 * @param string $name (optional)
	 * @access public
	 * @return float
	 */
	public function getStartTime($name = "") {
		if(!isset($this->timer[$name]['start']))
			$this->startTimer($name);

		return $this->timer[$name]['start'];
	}

	/**
	 *    Get the processing stop time
	 *
	 * @param string $name (optional)
	 * @access public
	 * @return float
	 */
	public function getEndTime($name = "") {
		if(!isset($this->timer[$name]['end']))
			$this->stopTimer($name);

		return $this->timer[$name]['end'];
	}

	/**
	 *    Get the processing elapsed time
	 *
	 * @param string $name (optional)
	 * @access public
	 * @return float
	 */
	public function getElapsedTime($name = "") {
		if(!isset($this->timer[$name]['total_time']))
			$this->stopTimer($name);

		return $this->timer[$name]['total_time'];
	}

	/**
	 *    Get total processed time
	 *
	 * @param string $name (optional)
	 * @param integer $total_length (optional)
	 * @access public
	 * @return float
	 */
	public function getProcessedTime($name = '', $total_length = '') {
		$total_length = $total_length ?$total_length :7;
		$total_length = "0.$total_length";
		$total_length = '%'.$total_length.'f';
		
		$fin_time = $this->getElapsedTime($name);
		
		return sprintf($total_length, $fin_time);
	}

	/**
	 *    Reset the Timer
	 *
	 * @param string $name (optional)
	 * @access public
	 */
	public function resetTimer($name = "") {
		unset($this->timer[$name]);
	}

	/**
	 *    Function used to reset all Timer
	 *
	 * @access public
	 */
	public function resetAllTimers() {
		unset($this->timer);
	}
}
/** End of File **/