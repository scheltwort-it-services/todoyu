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
 * Quickcreate headlet
 * 
 * @package		Todoyu
 * @subpackage	Core
 */
Todoyu.Headlet.QuickCreate = {

	/**
	 * Popup reference
	 */
	popup:	null,



	/**
	 * Initialize quick create headlet
	 */
	init: function() {

	},
	
	
	
	/**
	 * Handler: When clicked on button
	 * 
	 * @param	{Event}		event
	 */
	onButtonClick: function(event) {
		if( this.isContentVisible() ) {
			this.hide();
		} else {
			this.hideOthers();
			this.showContent();
		}
	},
	
	
	/**
	 * Handler: When clicked on menu entry
	 * 
	 * @param	{Event}		event
	 */
	onMenuClick: function(event) {
		var idParts	= Event.findElement(event, 'a').className.split('-');
		var ext		= idParts[3];
		var type	= idParts[4];
		
		this.openTypePopup(ext, type);
		this.hide();
	},


	onBodyClick: function(event) {
		this.hide();

		if( this.isEventInOwnContent(event) ) {			
			event.stop();
		}
	},
	
	hide: function() {
		this.hideContent();
		this.headlet.setInactive('quickcreate');
	},



	/**
	 * Open creator wizard popup
	 * 
	 * @param	{String}		ext
	 * @param	{String}		type
	 */
	openTypePopup: function(ext, type) {
		if ( ! $('quickcreate') ) {
			var ctrl 	= 'Quickcreate' + type.toLowerCase();
			var url		= Todoyu.getUrl(ext, ctrl);
			var options	= {
				'parameters': {
					'action':	'popup'
				},
				'onComplete': this.onPopupOpened.bind(this, ext, type)
			};
			var idPopup	= 'quickcreate';
			var title	= '[LLL:core.create]' + ': ' + this.getTypeLabel(ext, type);
			var width	= 700;

			this.popup = Todoyu.Popup.openWindow(idPopup, title, width, url, options);
		}
	},



	/**
	 * Handler after popup opened: call mode's onPopupOpened-handler
	 * 
	 * @param	{String}	ext
	 */
	onPopupOpened: function(ext, type) {
		$('quickcreate').addClassName(type);

		var quickCreateObject	= 'Todoyu.Ext.' + ext + '.QuickCreate' + Todoyu.Helper.ucwords(type);
		
		Todoyu.callUserFunction(quickCreateObject + '.onPopupOpened');
	},
	
	
	
	/**
	 * Get label of a type from menu entry
	 * 
	 * @param	{String}		ext
	 * @param	{String}		type
	 */
	getTypeLabel: function(ext, type) {
		return $$('#headlet-quickcreate-content a.headlet-quickcreate-item-' + ext + '-' + type)[0].innerHTML;
	},



	/**
	 * Close wizard popup
	 */
	closePopup: function() {
		Todoyu.Popup.close('quickcreate');
	},



	/**
	 * Update quick create popup content
	 *
	 * @param	{String}		content
	 */
	updatePopupContent: function(content) {
		$('quickcreate_content').update(content);
	}

};