<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * Ajax loader headlet
 * Shows AJAX loading icon (spinner animation) if a request is active
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHeadletAjaxLoader extends TodoyuHeadletTypeButton {

	/**
	 * Initialize headlets
	 */
	protected function init() {
			// Set JavaScript object which handles events
		$this->setJsHeadlet('Todoyu.Headlet.AjaxLoader');

		$this->addButtonAttribute('style', 'display:none;');
	}



	/**
	 * Get headlet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Label('core.headlet.ajaxloader.label');
	}

}

?>