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
 * Select form element
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_SelectGrouped extends TodoyuFormElement_Select {

	/**
	 * Initialize
	 *
	 * @param	String				$name
	 * @param	TodoyuFieldset		$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFieldset $fieldset, array $config = array()) {
		TodoyuFormElement::__construct('selectgrouped', $name, $fieldset, $config);

		if( ! $this->isLazyInit() ) {
			$this->init();
			$this->initSource();
		}
	}



	/**
	 * Init select groups' options from function reference
	 *
	 * @param	Array	$source
	 */
	protected function initSourceFunction(array $source) {
		$groups		= TodoyuFunction::callUserFunction($source['function'], $this);

		foreach($groups as $group => $options) {
			foreach($options as $option) {
				$this->addOption($group, $option['value'], $option['label'], $option['selected'], $option['disabled'], $option['classname']);
			}
		}
	}



	/**
	 * Add new option at the end of the list
	 *
	 * @param	String		$group
	 * @param	String		$value
	 * @param	String		$label
	 * @param	Boolean		$selected
	 * @param	Boolean		$disabled
	 * @param	String		$classname
	 */
	public function addOption($group, $value, $label, $selected = false, $disabled = false, $classname='') {
		$this->config['options'][$group][] = array(
			'value'			=> $value,
			'label'			=> $label,
			'disabled'		=> $disabled,
			'classname'		=> $classname,
		);

		if( $selected === true ) {
			$this->addSelectedValue($value);
		}
	}

}

?>