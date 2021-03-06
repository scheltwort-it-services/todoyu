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
 *	Todoyu autocompleter (extended prototype autocompleter)
 */

Todoyu.Autocompleter = Class.create(Ajax.Autocompleter, {

	/**
	 * Hanlde completion of autocompleter suggestion retrieval
	 *
	 * @param	{Ajax.Response}		response
	 */
	onComplete: function(response) {
			// If a custom onComplete defined
		if( this.options.onCompleteCustom ) {
			var funResult = Todoyu.callUserFunction(this.options.onCompleteCustom, response, this);

				// If the custom function returns an object, override response
			if( typeof(funResult) === 'object' ) {
				response = funResult;
			}
		}

		if( response.getTodoyuHeader('acElements') == 0 ) {
			this.onEmptyResult(response);
		}

			// Call default ac handler
		this.updateChoices(response.responseText);
	},



	/**
	 * Hanlde receival of empty result (no suggestion found)
	 *
	 * @param	{Ajax.Response}		response
	 */
	onEmptyResult: function(response) {
		new Effect.Highlight(this.element, {
			'startcolor':	'#ff0000',
			'endcolor':		'#ffffff',
			'duration':		2.0
		});

		if( ! this.options.onCompleteCustom ) {
			Todoyu.notifyInfo('[LLL:form.ac.noResults]');
		}
	}
});