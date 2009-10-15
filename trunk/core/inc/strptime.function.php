<?php

/**
 * strptime() for windows. The original has been modified because of several bugs
 * @package		Todoyu
 * @subpackage	Core
 */


/**
 * Implementation of strptime() for PHP on Windows.
 * Modified from http://au.php.net/manual/en/function.strptime.php#82004
 *
 * @param	String		$date
 * @param	String		$format
 * @return	Array		Parsed date
 */
function strptime($date, $format) {
	if( !($date = strptime_strToDate($date, $format)) )
		return false;

	$dateTime = array(
		'tm_sec'	=> 0,
		'tm_min'	=> 0,
		'tm_hour'	=> 0,
		'tm_mday'	=> 1,
		'tm_mon'	=> 1,
		'tm_year'	=> date('Y')
	); //array('sec' => 0, 'min' => 0, 'hour' => 0, 'day' => 0, 'mon' => 0, 'year' => 0, 'timestamp' => 0);
	foreach($date as $key => $val) {
		switch($key) {
			case 'd':
			case 'j': $dateTime['tm_mday'] = intval($val); break;
			case 'D': $dateTime['tm_mday'] = intval(date('j', $val)); break;

			case 'm':
			case 'n': $dateTime['tm_mon'] = intval($val); break;


			case 'Y': $dateTime['tm_year'] = intval($val); break;
			case 'y': $dateTime['tm_year'] = intval($val)+2000; break;

			case 'G':
			case 'g':
			case 'H':
			case 'h': $dateTime['tm_hour'] = intval($val); break;

			case 'M':
			case 'i': $dateTime['tm_min'] = intval($val); break;

			case 's': $dateTime['tm_sec'] = intval($val); break;
		}
	}
	$dateTime['timestamp'] = mktime($dateTime['tm_hour'], $dateTime['tm_min'], $dateTime['tm_sec'], $dateTime['tm_mon'], $dateTime['tm_mday'], $dateTime['tm_year']);

	/*echo '<pre>';
	print_r($dateTime);
	echo '</pre>';*/

	return $dateTime;
};


/**
 * Called by strptime().
 * Modified from http://au.php.net/manual/en/function.strptime.php#81611
 *
 * @param	String		$date
 * @param	String		$format
 * @return	Array
 */
function strptime_strToDate($date, $format) {
	$search = array('%d', '%D', '%j', // day
					'%m', '%M', '%n', // month
					'%Y', '%y', // year
					'%G', '%g', '%H', '%h', // hour
					'%i', '%s');
	$replace = array('(\d{2})', '(\w{3})', '(\d{1,2})', //day
					 '(\d{2})', '(\d{2})', '(\d{1,2})', // month
					 '(\d{4})', '(\d{2})', // year
					 '(\d{1,2})', '(\d{1,2})', '(\d{1,2})', '\d{2}', // hour
					 '(\d{2})', '(\d{2})');

	$pattern = str_replace($search, $replace, $format);

	if(!preg_match("#$pattern#", $date, $matches)) {
		return false;
	}
	$dp = $matches;

	if(!preg_match_all('#%(\w)#', $format, $matches)) {
		return false;
	}
	$id = $matches['1'];

	if(count($dp) != count($id)+1) {
		return false;
	}

	$ret = array();
	for($i=0, $j=count($id); $i<$j; $i++) {
		$ret[$id[$i]] = $dp[$i+1];
	}

	//echo '<pre>';
	//print_r($ret);
	//echo '</pre>';
	return $ret;
}

?>