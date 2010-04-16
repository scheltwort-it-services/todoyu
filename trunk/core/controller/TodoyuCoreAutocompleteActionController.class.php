<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 * Superglobal object to access important data and objects
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreAutocompleteActionController extends TodoyuActionController {

	public function updateAction(array $params) {
		$name		= trim($params['autocompleter']);
		$input		= trim($params['input']);
		$form		= trim($params['form']);
		$formData	= $params[$form];

		return TodoyuAutocompleter::renderAutocompleteList($name, $input, $formData);
	}

}

?>