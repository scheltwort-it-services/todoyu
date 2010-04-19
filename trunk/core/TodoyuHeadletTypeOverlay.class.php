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
 * Abstract headlet overlay class
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuHeadletTypeOverlay extends TodoyuHeadlet {

	/**
	 * Type
	 *
	 * @var	String
	 */
	protected $type = 'overlay';



	/**
	 * Type initialization
	 */
	protected function initType() {
		$this->addButtonClass('headletTypeOverlay');
	}



	/**
	 * Abstract function which must be implementented by headlets of type overlay
	 */
	abstract protected function renderOverlayContent();



	/**
	 * Render headlet content
	 *
	 * @return	String
	 */
	public function render() {
		$this->data['content'] = $this->renderOverlayContent();

		return parent::render();
	}

}

?>