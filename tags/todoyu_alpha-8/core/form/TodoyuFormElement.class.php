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
 * Base class Todoyufor all form elements
 *
 * @package		Todoyu
 * @subpackage	Form
 */

abstract class TodoyuFormElement implements TodoyuFormElementInterface {

	/**
	 * Type of the element
	 *
	 * @var	String
	 */
	protected $type;

	/**
	 * Name of the form element
	 *
	 * @var	String
	 */
	protected $name;

	/**
	 * Parent fieldset
	 *
	 * @var	TodoyuFieldset
	 */
	protected $fieldset;

	/**
	 * Field configuration
	 *
	 * @var	Array
	 */
	public $config;


	/**
	 * Field error
	 *
	 * @var	Boolean
	 */
	protected $error = false;

	/**
	 * Field error message
	 *
	 * @var	String
	 */
	protected $errorMessage = '';



	/**
	 * Initialize form element
	 *
	 * @param	String		$type
	 * @param	String		$name
	 * @param	TodoyuFieldset	$fieldset
	 * @param	Array		$config
	 */
	public function __construct($type, $name, TodoyuFieldset $fieldset, array $config = array()) {
		$this->type 	= $type;
		$this->name 	= $name;
		$this->fieldset = $fieldset;
		$this->config	= $config;

		$this->setAttribute('nodeAttributes', $this->getAttribute('@attributes'));
		$this->setAttribute('htmlId', $this->getForm()->makeID($this->name));

		$this->init();
	}



	/**
	 * Init after constructor
	 * Can be overridden in extended types
	 *
	 */
	protected function init() {

	}



	/**
	 * Remove the field from the form
	 *
	 */
	public function remove() {
		$this->fieldset->removeField($this->getName(), true);
	}



	/**
	 * Get template for this form element
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return TodoyuFormFactory::getTemplate($this->type);
	}



	/**
	 * Get form instance
	 *
	 * @return	TodoyuForm
	 */
	public final function getForm() {
		return $this->fieldset->getForm();
	}



	/**
	 * Get fieldset
	 *
	 * @return	TodoyuFieldset
	 */
	public final function getFieldset() {
		return $this->fieldset;
	}



	/**
	 * Get data to render the element
	 * A lot of config fields have to be processed and transformed, before
	 * the element can be rendered with its template
	 *
	 * @return	Array
	 */
	protected function getData() {
		$this->config['htmlId']			= $this->getForm()->makeID($this->name);
		$this->config['htmlName']		= $this->getForm()->makeName($this->name, $this->config['multiple']);
		$this->config['label']			= TodoyuDiv::getLabel($this->config['label']);
		$this->config['containerClass']	= 'type' . ucfirst($this->type) . ' fieldname' . ucfirst(str_replace('_', '', $this->name));
		$this->config['inputClass']		= $this->type;
		$this->config['required']		= $this->hasAttribute('required');
		$this->config['hasErrorClass']	= $this->hasAttribute('hasError') ? 'fieldHasError':'';
		$this->config['hasIconClass']	= $this->hasAttribute('hasIcon') ? 'hasIcon icon' . ucfirst($this->name):'';
		$this->config['wizard']			= $this->getWizardConfiguration();

		return $this->config;
	}



	/**
	 * Get config value
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	public function __get($name) {
		return $this->config[$name];
	}



	/**
	 * Set config value
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function __set($name, $value) {
		$this->setAttribute($name, $value);
	}



	/**
	 * Set config value
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function setAttribute($name, $value) {
		$this->config[$name] = $value;
	}



	/**
	 * Get config value
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	public function getAttribute($name) {
		return $this->config[$name];
	}



	/**
	 * Check if an attribute is set
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function hasAttribute($name) {
		return isset($this->config[$name]);
	}



	/**
	 * Get form element field name
	 *
	 * @return 	String
	 */
	public function getName() {
		return $this->name;
	}



	/**
	 * Get form element label
	 *
	 * @return unknown
	 */
	public function getLabel() {
		return $this->getAttribute('label');
	}



	/**
	 * Get type of form element
	 *
	 * @return unknown
	 */
	public function getType()	{
		return $this->type;
	}



	/**
	 * Get field value ('attribute') of form element
	 *
	 * @return	Mixed
	 */
	public function getValue() {
		return $this->getAttribute('default');
	}



	/**
	 * Set field value ('attribute')
	 *
	 * @param	Mixed		$value
	 */
	public function setValue($value) {
		$this->setAttribute('default', $value);

		$this->updateFormData($value);
	}



	/**
	 * Update form data for field
	 *
	 * @param	Mixed		$value
	 */
	protected function updateFormdata($value) {
		$this->getForm()->setFieldFormData($this->name, $value);
	}



	/**
	 * Get HTML ID of field
	 *
	 * @return	String
	 */
	public function getHtmlID() {
		return $this->getForm()->makeID($this->getName());
	}



	/**
	 * Get data of field to store in the database
	 *
	 * @return	String
	 */
	public function getStorageData() {
		if( $this->isNoStorageField() ) {
			return false;
		} else {
			return $this->getValue();
		}
	}



	/**
	 * Check if field is hidden (not displayed when fieldset is rendered)
	 *
	 * @return	String
	 */
	public function isHidden() {
		return $this->hasAttribute('hide');
	}



	/**
	 * Check if field is marked as no storage. If true,
	 * the field will not be stored in the database
	 *
	 * @return	Boolean
	 */
	public function isNoStorageField() {
		return $this->hasAttribute('noStorage');
	}



	/**
	 * Check if field is valid
	 *
	 * @return	Boolean
	 */
	public final function isValid() {
		$validations 	= $this->getValidations();
		$formData		= $this->getForm()->getFormData();

		foreach($validations as $validatorName => $validatorConfig) {
			$isValid = TodoyuFormValidator::validate($validatorName, $this->getStorageData(), $validatorConfig, $this, $formData);

			if( $isValid === false ) {
				$this->setErrorTrue();

					// If error message not already set by function, check config or use default
				if( $this->errorMessage === '' ) {
					if( isset($validatorConfig['@attributes']['msg']) ) {
						$this->setErrorMessage( TodoyuDiv::getLabel($validatorConfig['@attributes']['msg']) );
					} else {
						$this->setErrorMessage( Label('form.field.hasError') );
					}
				}

				return false;
			}
		}

			// If there are no validations registered, but a required flag is set, check
			// if there is a required checking function and call it
		if( sizeof($validations) === 0 && $this->isRequired() ) {
			if( ! $this->validateRequired() ) {
				$this->setErrorTrue();
				$this->setErrorMessage( Label('form.field.isrequired') );

				return false;
			}
		}

		return true;
	}



	/**
	 * Set error message of form element
	 *
	 * @param	String	$message
	 */
	public function setErrorMessage($message) {
		$this->errorMessage = $message;
	}


	public function getErrorMessage() {
		return $this->errorMessage;
	}



	/**
	 * Set error status of form element true
	 *
	 */
	protected function setErrorTrue() {
		$this->error = true;
	}



	/**
	 * Check whether form element has error status ($this->error == true)
	 *
	 * @return	Boolean
	 */
	protected function hasErrorStatus() {
		return $this->error === true;
	}



	/**
	 * Check whether form element has validations assigned
	 *
	 * @return	Boolean
	 */
	public function hasValidations() {
		return sizeof($this->getValidations()) > 0 ;
	}



	/**
	 * Get field validations
	 *
	 * @return	Array
	 */
	public function getValidations() {
		return is_array($this->config['validate']) ? $this->config['validate'] : array();
	}




	/**
	 * Check if field is required
	 *
	 * @return	Boolean
	 */
	public function isRequired() {
		return $this->hasAttribute('required');
	}



	/**
	 * Validate required field value being given
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		return ($this->getValue()) ? true : false;
	}


	/**
	 * Render form element
	 *
	 * @param	Boolean	$odd		Odd or even?
	 * @return	String
	 */
	public function render($odd = false) {
		$tmpl	= $this->getTemplate();
		$data	= $this->getData();

		$data['odd'] 			= $odd ? true : false;
		$data['error'] 			= $this->error;
		$data['errorMessage'] 	= $this->errorMessage;

		return render($tmpl, $data);
	}



	/**
	 * Returns Form field wizard configurations
	 *
	 * @return	Array
	 */
	public function getWizardConfiguration()	{
		if($this->hasAttribute('wizard'))	{
			$wizardConf = array(
				'hasWizard'		=> true,
				'wizardConf'	=> $this->getAttribute('wizard')
			);

			$wizardConf['wizardConf']['idRecord']	= intval($this->getForm()->getRecordID());

			if($wizardConf['wizardConf']['displayCondition'])	{
				$wizardConf['hasWizard'] = TodoyuDiv::callUserFunctionArray($wizardConf['wizardConf']['displayCondition'], $wizardConf);
			}
		}

		return $wizardConf;
	}
}

?>