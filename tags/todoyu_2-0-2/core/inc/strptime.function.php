<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

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
	if( !($date = strptime_strToDate($date, $format)) ) {
		return false;
	}

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
				// day
			case 'd':
			case 'e': $dateTime['tm_mday'] = intval($val); break;

				// month
			case 'm': $dateTime['tm_mon'] = intval($val); break;

			case 'Y': $dateTime['tm_year'] = intval($val); break;
			case 'y': $dateTime['tm_year'] = intval($val)+2000; break;

			case 'H':
			case 'I': $dateTime['tm_hour'] = intval($val); break;

			case 'M': $dateTime['tm_min'] = intval($val); break;

			case 'S': $dateTime['tm_sec'] = intval($val); break;
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
		// Remove AM, not necessary
	$date	= str_ireplace('am', '', $date);
		// Remove AM/PM marker
	$format	= trim(str_replace('%p', '', $format));
	
		// Check for PM, remove it and add 12 to the hour
	if( stripos($date, 'pm') !== false ) {
		if( preg_match('/.* ((\d{2}):(\d{2}) ?pm).*/i', $date, $matchesPM) === 1 ) {
			$replace= (intval($matchesPM[2])+12) . ':' . $matchesPM[3];
			$date	= str_replace($matchesPM[1], $replace, $date);
		}
	}

		// Define replacements for strftime markers with regex patterns
	$search = array('%d', '%e', // day
					'%m', // month
					'%Y', '%y', // year
					'%H', '%I', // hour
					'%M', // minutes
					'%S'); // seconds
	$replace = array('(\d{1,2})', '(\d{1,2})', //day
					 '(\d{1,2})', // month
					 '(\d{4})', '(\d{2})', // year
					 '(\d{1,2})', '(\d{1,2})', // hour
					 '(\d{1,2})', // minutes
					 '(\d{2})'); // seconds					 

		// Replace markers
	$pattern = str_replace($search, $replace, $format);

		// The to find a marker
	if(!preg_match("#$pattern#", $date, $matches)) {
		return false;
	}
	$dp = $matches;

		// Find all markers
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