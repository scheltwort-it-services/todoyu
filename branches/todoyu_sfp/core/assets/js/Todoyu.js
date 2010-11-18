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
 * @module	Core
 */

/**
 * Todoyu main container. All other JS containers are (sub-)nodes of this container
 *
 * @class	Todoyu
 * @static
 */
var Todoyu = {

	/**
	 * System name
	 * @property	name
	 * @type		String
	 */
	name: 		'Todoyu',

	/**
	 * Copyright owner
	 * @property	copyright
	 * @type		String
	 */
	copyright: 	'snowflake productions GmbH, Zurich/Switzerland',

	/**
	 * Log level
	 * @property	logLevel
	 * @type		Number
	 */
	logLevel:	0,

	/**
	 * Store the format of all available jsCalendars
	 * @property	JsCalFormat
	 * @type		Array
	 */
	JsCalFormat: {},


	/**
	 * Initialize todoyu object
	 *
	 * @method	init
	 */
	init: function() {
		this.Ajax.Responders.init();
		this.Ui.fixAnchorPosition();
		this.Ui.observeBody();
		this.initExtensions();
	},



	/**
	 * Initialize all extensions
	 * Call the init() function of all extensions in their main container if it exists
	 *
	 * @method	initExtensions
	 */
	initExtensions: function() {
		$H(this.Ext).each(function(pair){
			if( typeof(pair.value.init) === 'function' ) {
				pair.value.init();
			}
		});
	},



	/**
	 * Build request url with extension and controller
	 *
	 * @method	getUrl
	 * @param	{String}		ext
	 * @param	{String}		controller
	 * @param	{String}		hash
	 * @return	{String}
	 */
	getUrl: function(ext, controller, params, hash) {
		var url = 'index.php?ext=' + ext;

		if( controller ) {
			url = url + '&controller=' + controller;
		}

		if( typeof params === 'object' ) {
			url += '&' + Object.toQueryString(params);
		}

		if( hash ) {
			url += '#hash';
		}

		return url;
	},



	/**
	 * Redirect to another page
	 *
	 * @method	goTo
	 * @param	{String}	ext
	 * @param	{String}	controller
	 * @param	{Hash}		params
	 * @param	{String}	hash
	 * @param	{Boolean}	newWindow
	 * @param	{String}	windowName
	 */
	goTo: function(ext, controller, params, hash, newWindow, windowName) {
		newWindow	= newWindow ? newWindow : false;
		var url		=  this.getUrl(ext, controller, params);

		if( Object.isString(hash) ) {
			this.goToHashURL(url, hash, newWindow, windowName);
		} else {
			if( newWindow === false ) {
				location.href = url;
			} else {
				window.open(url, windowName);
			}
		}
	},



	/**
	 * Go to an URL with a hash
	 * If the URL itself is identical, just scroll to the element
	 *
	 * @method	goToHashURL
	 * @param	{String}	url
	 * @param	{String}	hash
	 * @param	{Boolean}	newWindow
	 * @param	{String}	windowName
	 */
	goToHashURL: function(url, hash, newWindow, windowName) {
		newWindow		= newWindow ? newWindow : false;
		var searchPart	= url.substr(url.indexOf('?'));

		if( location.search === searchPart && Todoyu.exists(hash) ) {
			if( $(hash).getHeight() > 0 ) {
				$(hash).scrollToElement();
				return;
			}
		}

			// Fallback
		if( newWindow === false ) {
			location.href =  url + '#' + hash;
		} else {
			windowName	= windowName ? windowName : '';

			window.open(url + '#' + hash, windowName);
		}
	},



	/**
	 * Send AJAX request
	 *
	 * @method	send
	 * @param	{String}		url
	 * @param	{Hash}			options
	 */
	send: function(url, options) {
		options = Todoyu.Ajax.getDefaultOptions(options);

		return new Ajax.Request(url, options);
	},



	/**
	 * Check whether an element exists
	 *
	 * @method	exists
	 * @param	{Element|String}		element		Element or its ID
	 */
	exists: function(element) {
		if( Object.isElement(element) ) {
			return true;
		}

		return document.getElementById(element) !== null;
	},



	/**
	 * Get current area
	 *
	 * @method	getArea
	 * @return	{String}
	 */
	getArea: function() {
		return document.body.id.split('-').last();
	},



	/**
	 * Show error notification
	 *
	 * @method	notifyError
	 * @param	{String}		message
	 * @param	{Number}		countdown
	 */
	notifyError: function(message, countdown) {
		Todoyu.Notification.notifyError(message, countdown);
	},



	/**
	 * Show info notification
	 *
	 * @method	notifyInfo
	 * @param	{String}		message
	 * @param	{Number}		countdown
	 */
	notifyInfo: function(message, countdown) {
		Todoyu.Notification.notifyInfo(message, countdown);
	},



	/**
	 * Show success notification
	 *
	 * @method	notifySuccess
	 * @param	{String}		message
	 * @param	{Number}		countdown
	 */
	notifySuccess: function(message, countdown) {
		Todoyu.Notification.notifySuccess(message, countdown);
	},



	/**
	 * Call a user function in string format with given arguments
	 * @example	Todoyu.calluserFunction('Todoyu.notifySuccess', 'This is a message', 5);
	 *
	 * @method	callUserFunction
	 * @param	{String}		functionName
	 * @return	{String|Number|Array|Object}
	 */
	callUserFunction: function(functionName /*, args */) {
		var args 	= $A(arguments).slice(1);
		var func	= this.getFunctionFromString(functionName);
		var context	= this.getFunctionFromString(functionName.split('.').slice(0,-1).join('.'));

		return func.apply(context, args);
	},



	/**
	 * Call a function reference, if it's one. Otherwise just ignore the call
	 * The first argument is the function, all other arguments will be handed down to this function
	 * The debug output is just for development
	 *
	 * @method	callIfExists
	 * @param	{Function}	functionReference	Function
	 * @param	{Object}		context				Context which is this in function
	 */
	callIfExists: function(functionReference, context /*, args */) {
		var args = $A(arguments).slice(2);

		if( typeof functionReference === 'function' ) {
			functionReference.apply(context, args);
		} else {
			//Todoyu.log('Todoyu.callIfExists() was executed with a non-function. This can be an error (not sure). Params: ' + Object.inspect(args), 1);
		}
	},



	/**
	 * Get a function reference from a function string
	 * Ex: 'Todoyu.Ext.project.edit'
	 *
	 * @method	getFunctionFromString
	 * @param	{String}		functionName
	 */
	getFunctionFromString: function(functionName, bind) {
		var namespaces 	= functionName.split(".");
		var func 		= namespaces.pop();
		var context		= window;

		for(var i = 0; i < namespaces.length; i++) {
			context = context[namespaces[i]];

			if( context === undefined ) {
				alert("Function: " + functionName + " not found!");
			}
		}

		var funcRef = context[func];

		if( bind ) {
			funcRef = funcRef.bind(context);
		}

		return funcRef;
	},



	/**
	 * Todoyu log. Check level and if console exists
	 *
	 * @method	log
	 * @param	{Object}		element
	 * @param	{Number}		level
	 * @param	{String}		title
	 */
	log: function(element, level, title) {
		if( level === undefined || (Object.isNumber(level) && level >= this.logLevel) ) {
			if( window.console !== undefined ) {
				if( title !== undefined ) {
					window.console.log('Log: ' + title);
				}
				window.console.log(element);
			}
		}
	}

};