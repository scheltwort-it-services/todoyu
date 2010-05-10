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
 * Core Action Controller
 * Quickinfo
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreQuickinfoActionController extends TodoyuActionController {

	/**
	 * Render about window
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function getAction(array $params) {
		$name	= trim($params['quickinfo']);
		$element= trim($params['element']);

		$quickinfo	= new TodoyuQuickinfo($name, $element);

		$quickinfo->printJSON();
	}

}

?>
