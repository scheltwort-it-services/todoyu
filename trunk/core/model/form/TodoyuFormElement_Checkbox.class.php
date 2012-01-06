<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * FormElement: Checkbox
 *
 * Checkbox input
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Checkbox extends TodoyuFormElement {

	/**
	 * Constructs an new checkbox form element
	 *
	 * @param	String		$name
	 * @param	TodoyuFieldset	$fieldset
	 * @param	Array		$config
	 */
	public function __construct($name, TodoyuFieldset $fieldset, array $config = array()) {
		parent::__construct('checkbox', $name, $fieldset, $config);
	}



	/**
	 * Get checkbox form element data
	 *
	 * @return	Array
	 */
	protected function getData() {
		if( $this->hasAttribute('onchange') ) {
			$this->config['extraAttributes'] .= 'onchange="' . $this->getForm()->parseWithFormData($this->getAttribute('onchange')) . '"';
		}

		if( $this->getValue() == 1 ) {
			$this->config['extraAttributes'] .= 'checked="checked"';
		}

		return parent::getData();
	}



	/**
	 * Set checked status
	 *
	 * @param	Boolean		$checked
	 */
	public function setChecked($checked = true) {
		$value	= $checked ? 1 : 0;

		$this->setValue($value);
	}



	/**
	 * Set form element value as given
	 *
	 * @param	Mixed	$value
	 */
	public function setValue($value) {
		parent::setValue($value);
	}



	/**
	 * Check if checkbox is checked
	 *
	 * @return	Boolean
	 */
	public function isChecked() {
		return $this->getValue() == 1;
	}



	/**
	 * Empty for checkbox means no values available (new record)
	 *
	 * @return	Boolean
	 */
	public function isEmpty() {
		return !$this->hasAttribute('value');
	}

	

	/**
	 * Get storage data:
	 * 1: checked; 0: not checked
	 *
	 * @return	Integer
	 */
	protected function getStorageDataInternal() {
		return $this->isChecked() ? 1 : 0;
	}

}

?>