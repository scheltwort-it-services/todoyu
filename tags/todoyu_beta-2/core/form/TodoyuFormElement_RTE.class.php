<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * FormElement: Textinput
 *
 * Single line textinput, <input type="text">
 *
 * @package		Todoyu
 * @subpackage	Form
 */

class TodoyuFormElement_RTE extends TodoyuFormElement_Textarea {

	/**
	 * Initialize RTE form element
	 *
	 * @param	String		$name
	 * @param	TodoyuFieldset	$fieldset
	 * @param	Array		$config
	 */
	public function __construct($name, TodoyuFieldset $fieldset, array $config = array()) {
		TodoyuFormElement::__construct('RTE', $name, $fieldset, $config);
	}



	/**
	 * Build RTE initialisation javascript code to convert the textarea into a RTE
	 * when displayed on the page
	 *
	 * @return	String
	 */
	private function buildRTEjs() {
		$options	= array(
			'mode'		=> 'exact',
			'elements'	=> $this->getHtmlID(),
			'theme'		=> 'simple');

			// Load config
		if( is_array($this->config['tinymce']) ) {
			foreach($this->config['tinymce'] as $name => $value) {
				$options[$name] = $value;
			}
		}

			// Generate config code
		$jsCode	= "tinyMCE.init({\n";
		$tmpOpt	= array();

		foreach($options as $name => $value) {
			$tmpOpt[] = $name . ' : "' . $value . '"';
		}

		$jsCode .= implode(",\n", $tmpOpt);

		$jsCode .= "\n});\n";

		return $jsCode;
	}



	/**
	 * Get rich text editor (tinyMCE) config
	 *
	 * @param	String	$name
	 * @return	Array
	 */
	private function getRTEconfig($name) {
		$defaults	= array('theme'	=> 'simple');
		$config		= false;

		if( isset($this->config['tinymce']['theme']) ) {
			$config = $this->config['tinymce']['theme'];
		} elseif( array_key_exists($name, $defaults) ) {
			$config = $defaults[$name];
		}

		return $config;
	}



	/**
	 * Get data to store in the database for this field
	 *
	 * @return	String
	 */
	public function getStorageData() {
		$value	= str_replace("\n", '', $this->getValue());

		$this->setValue($value);

		return parent::getStorageData();
	}



	/**
	 * Get field data
	 *
	 * @return	Array
	 */
	public function getData() {
		$this->config['rteJs'] = $this->buildRTEjs();

		return parent::getData();
	}



	/**
	 * Check if field is valid for required flag
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		return TodoyuValidator::isNotEmpty($this->getValue());
	}

}

?>