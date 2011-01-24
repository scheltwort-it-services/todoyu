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
 *	Todoyu popup
 *
 *	@namespace	Todoyu.Popup
 *	@see http://prototype-window.xilinus.com/documentation.html
 */
Todoyu.Popup = {

	/**
	 * Popup object reference
	 */
	popup:		{},
	
	last:		null,

	timeoutID:	null,



	/**
	 * Get popup reference
	 *
	 * @param	{String}			idPopup
	 * @return	Object
	 */
	getPopup: function(idPopup) {
		return this.popup[idPopup];
	},



	/**
	 * Get last opened popup handle
	 * 
	 * @return	Object
	 */
	getLastPopup: function() {
		return this.last;
	},



	/**
	 * Get amount of open popups
	 * 
	 * @return	{Number}
	 */
	getNumPopups: function() {
		return Object.keys(this.popup).size();
	},



	/**
	 * Open new popup window
	 *
	 * @param	{String}		idPopup
	 * @param	{String}		title
	 * @param	{Number}		winWidth
	 * @param	{String}		contentUrl
	 * @param	{Array}		requestOptions
	 * @return	{Window}
	 */
	openWindow: function(idPopup, title, winWidth, contentUrl, requestOptions) {
			// Construct
		this.popup[idPopup] = new Window({
			id:					idPopup,
			className:			"dialog",
			title:				title,

			parent:				document.getElementsByTagName("body").item(0),

			minWidth:			100,
			minHeight:			220,
			width:				winWidth,
			height:				240,

			resizable:			true,
			closable:			true,
			minimizable:		false,
			maximizable:		false,
			draggable:			false,

			zIndex:				2000,
			recenterAuto:		false,

			'hideEffect':		Element.hide,
			'showEffect':		Element.show,
			effectOptions:		null,

			destroyOnClose:		true
		});

			// Show popup and activate content overlay
		this.getPopup(idPopup).showCenter(true, 100);

			// Wrap onComplete with own onComplete to handle popup
		requestOptions = requestOptions || {};
		if( typeof requestOptions.onComplete !== 'function' ) {
			requestOptions.onComplete = Prototype.emptyFunction;
		}

		requestOptions.onComplete.wrap(function(idPopup, callOriginal, response){
			callOriginal(response);
		}.bind(this, idPopup));

		this.getPopup(idPopup).setAjaxContent(contentUrl, requestOptions, false, false);

			// Save last opened popup
		this.last = this.getPopup(idPopup);

			// Close all RTEs inside popup when closing it
		var closeObserver = {
			onDestroy: function(eventName, win) {
				Todoyu.Ui.closeRTE(win.content);
			}
		}
		Windows.addObserver(closeObserver);

		return this.getPopup(idPopup);
	},



	/**
	 * Enter Description here...
	 *
	 * @param	{String}	idPopup
	 */
	getContentElement: function(idPopup) {
		return $(idPopup + '_content');
	},



	/**
	 * Update size of popUp to fit its content without scroll bar
	 *
	 * @param	{String}	idPopup
	 * @param	{Boolean} clearTimeout
	 */
	updateHeight: function(idPopup, clearTimeout) {
		this.getPopup(idPopup).updateHeight();
	},



	/**
	 * Update popup content
	 *
	 * @param	{String}	contentUrl
	 * @param	{Object}	requestOptions
	 */
	updateContent: function(idPopup, contentUrl, requestOptions) {
		this.getPopup(idPopup).setAjaxContent(contentUrl, requestOptions, false, false);
	},



	/**
	 * Set content of given popup
	 *
	 * @param	{String}	idPopup
	 * @param	{String}	content
	 */
	setContent: function(idPopup, content) {
		this.getPopup(idPopup).setHTMLContent(content);
		content.evalScripts();
	},



	/**
	 * Refresh popup
	 *
	 * @param	{String}	idPopup
	 */
	refresh: function(idPopup) {
		this.getPopup(idPopup).refresh();
	},



	/**
	 * Close popUp
	 *
	 * @param	{String}	idPopup
	 */
	close: function(idPopup) {
		this.getPopup(idPopup).close();
	},



	/**
	 * Destroy popUp
	 * 
	 * @param	{String}	idPopup
	 */
	destroy: function(idPopup) {
		this.getPopup(idPopup).destroy();
	}

};