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
 * Autocompleter
 */
Todoyu.Autocomplete = {
	/**
	 * Configuration for autocompleter object
	 */
	config: {
		paramName: 'input',
		minChars: 2
	},

	/**
	 * Autocompleter references
	 */
	acRefs: {},

	/**
	 * Flag. True if a valid option just was selected
	 * Prevents to cleanup field
	 */
	selectedFromList: false,



	/**
	 * Initialize autocompleter ('inputAC')
	 */

	/**
	 * Initialize autocompleter
	 *
	 * @param	{Number}		idElement		ID of the element whichs value will be set by autocomplete
	 * @param	{Object}		config			Custom config
	 */
	install: function(idElement, config)	{
		var inputField		= idElement + '-fulltext';
		var suggestDiv		= idElement + '-suggest';

			// setup request
		var url		= Todoyu.getUrl('core', 'autocomplete');
		var options = {
			paramName:	config.paramName || this.config.paramName,
			minChars:	config.minChars || this.config.minChars,
			callback:	this.beforeRequestCallback.bind(this),
			parameters:	'&action=update&autocompleter=' + config.acName + '&element=' + idElement,
			afterUpdateElement:	this.onElementSelected.bind(this)
		};

		if( config.options ) {
			options = $H(options).update(config.options).toObject();
		}

			// Create autocompleter
		this.acRefs[idElement] = new Todoyu.Autocompleter(inputField, suggestDiv, url, options);

			// Observe input
		$(inputField).observe('change', this.onInputChange.bindAsEventListener(this));
			// Observe input for key down to clean up invalid input
		$(inputField).observe('keydown', this.onKeydown.bindAsEventListener(this));
	},



	/**
	 * Callback which builds the request url
	 *
	 * @param	{Number}		idElement
	 * @param	{String}		acParam
	 */
	beforeRequestCallback: function(idElement, acParam) {
		var form	= $(idElement).up('form');
		var name	= form.readAttribute('name');
		var data	= form.serialize();

		return acParam + '&form=' + name + '&' + data;
	},



	/**
	 * Called if input field has changed (blur)
	 *
	 * @param	{Event}		event
	 */
	onInputChange: function(event) {
			// If the change was called by a valid select, revert flag and do nothing
		if( this.selectedFromList ) {
			this.selectedFromList = false;
			return;
		}
			// Extract field id
		var idElement = event.element().id.split('-').without('fulltext').join('-');
			// Clear fields
		this.clear(idElement);
	},



	/**
	 * On keypress. If its not the return key, the current value is invalid (until autocompleted)
	 *
	 * @param	{Event}	event
	 */
	onKeydown: function(event) {
		if( event.keyCode !== Event.KEY_RETURN && event.keyCode !== Event.KEY_TAB ) {
			this.selectedFromList = false;
		}
	},



	/**
	 * When autocomplete value is selected
	 *
	 * @param	{Element}	inputField
	 * @param	{Element}	selectedListElement
	 */
	onElementSelected: function(inputField, selectedListElement) {
		var baseID			= inputField.id.split('-').without('fulltext').join('-');
		var selectedValue	= selectedListElement.id;

		this.selectedFromList = true;

		if(this.acRefs[baseID].options.onElementSelectedCustom)	{
			 Todoyu.callUserFunction(this.acRefs[baseID].options.onElementSelectedCustom, inputField, selectedListElement, baseID, selectedValue, this.selectedFromList, this);
		}

		$(baseID).setValue(selectedValue);
	},



	/**
	 * Clear fields because of invalid input
	 *
	 * @param	{Element}		element
	 */
	clear: function(element) {
		var idElement = $(element).id;
		$(idElement).setValue('0');
		$(idElement + '-fulltext').setValue('');
	}

};