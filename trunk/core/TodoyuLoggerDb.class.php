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
 * Database logger
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLoggerDb {

	/**
	 * Write log message in database
	 *
	 * @param	String		$message
	 * @param	Integer		$level
	 * @param	Mixed		$data
	 * @param	Array		$info
	 * @param	String		$requestKey
	 */
	public static function log($message, $level, $data, $info, $requestKey) {
		$table	= Todoyu::$CONFIG['LOG']['MODES']['DB']['table'];

		$data 	= array(
			'date_create'	=> NOW,
			'id_person'		=> personid(),
			'requestkey'	=> $requestKey,
			'level'			=> intval($level),
			'file'			=> $info['fileshort'],
			'line'			=> $info['line'],
			'message'		=> $message,
			'data'			=> serialize($data)
		);

		return Todoyu::db()->doInsert($table, $data);
	}

}

?>