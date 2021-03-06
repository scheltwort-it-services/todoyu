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

Todoyu.Headlet = {

	/**
	 * Current headlet name which is in over status
	 */
	current: null,

	/**
	 * List of headlet js objects (to call the handlers)
	 */
	headlets: {},



	/**
	 * Get the headlet element from an event
	 * 
	 * @param	Event		event
	 * @return	Element
	 */
	_getHeadletFromEvent: function(event) {
		return event.findElement('li.headlet');
	},



	/**
	 * Get the headlets name from an event
	 * 
	 * @param	Event		event
	 * @return	String
	 */
	_getNameFromEvent: function(event) {
		var h = this._getHeadletFromEvent(event);
		return h.id.split('-').last().toLowerCase();
	},



	/**
	 * Check if event happend in the content div of an overlay headlet
	 * 
	 * @param	Event		event
	 * @return	Bool
	 */
	_isContentEvent: function(event) {
		return event.element().up('ul.content') !== undefined;
	},



	/**
	 * Call the handler of a headlet if it has the specific function
	 * Possible handlers: onButtonClick, onContentClick, onMouseOver, onMouseOut
	 * 
	 * @param	String		name		Name of the headlet
	 * @param	String		type		Event type (handler name)
	 * @param	Event		event		Event object
	 */
	_callHandler: function(name, eventType, event) {
		var headlet	= this.headlets[name];

		if( typeof(headlet[eventType]) === 'function' ) {
			headlet[eventType].call(headlet, event);
		}
	},



	/**
	 * Initialize headlet management (observation)
	 */
	init: function() {
		var headlets	= $('headlets').select('li.headlet');
			// Observe headlet clicks
		headlets.invoke('observe', 'click', this.onClick.bindAsEventListener(this));
			// Observe all headlet elements
		headlets.invoke('observe', 'mouseover', this.onOverHeadlet.bindAsEventListener(this));
			// Observe headlet container
		$('headlets').observe('mouseover', this.onOverContainer.bindAsEventListener(this));
			// Close headlets when clicked outside of the headlets (on body)
		Todoyu.Ui.addBodyClickObserver(this.onBodyClick.bind(this));
	},



	/**
	 * Add a headlet object
	 * 
	 * @param	String		name
	 * @param	Object		headletObject
	 */
	add: function(name, headletObject) {
		name = name.toLowerCase();

			// Add to internal list
		this.headlets[name] = headletObject;

			// Add back reference to headlet object
		headletObject.headlet = this;
		
			// Add functions to headlet
		headletObject.hideOthers		= this.hideAllContent.bind(this, name);
		headletObject.isContentVisible	= this.isContentVisible.bind(this, name);
		headletObject.toggleContent		= this.toggleContent.bind(this, name);
		headletObject.showContent		= this.showContent.bind(this, name);
		headletObject.hideContent		= this.hideContent.bind(this, name);

			// Call headlet init function if exists
		Todoyu.callIfExists(headletObject.init, headletObject);
	},



	/**
	 * Handler for hovering a headlet
	 * 
	 * @param	Event		event
	 */
	onOverHeadlet: function(event) {
			// Over headlet, stop event bubbling
		event.stop();

			// Get headlet elements
		var headlet = this._getHeadletFromEvent(event);

			// If overstatus for headlet not already set
		if( headlet.overStatus !== true ) {
				// Set headlet over status
			headlet.overStatus = true;

				// Find name of current headlet				
			this.current = this._getNameFromEvent(event);
				// Call over handler for element
			this._callHandler(this.current, 'onMouseOver', event);			
		}
	},



	/**
	 * Handler for hovering the headlet container
	 * 
	 * @param	Event		event
	 */
	onOverContainer: function(event) {
			// Hover container, set over status of all elements false
		$('headlets').select('li.headlet').each(function(item){
				// Disable over status for each headlet
			item.overStatus = false;
		}.bind(this));

			// If there was a headlet in over status, call out handler
		if( this.current !== null ) {
				// Call out handler
			this._callHandler(this.current, 'onMouseOut', event);
				// Remove current element link
			this.current = null;	
		}
	},



	/**
	 * On click handler
	 * Calls one of the click handlers when on a headlet
	 * 
	 * @param	Event		event
	 */
	onClick: function(event) {
		var headlet = this._getHeadletFromEvent(event);

		if( headlet !== undefined ) {
			var name	= this._getNameFromEvent(event);
			var type	= '';

			this.hideAllContent(name);
			this.setActive(name);

			if( this._isContentEvent(event) ) {
				type	= 'onContentClick';

					// Check for menu click
				if( headlet.down('a.button').hasClassName('headletTypeMenu') ) {
					this._callHandler(name, 'onMenuClick', event);
				}
			} else {
				type	= 'onButtonClick';
			}

			this._callHandler(name, type, event);
		}

		event.stop();
	},



	/**
	 * On menu click handler
	 * Calls the onMenuClick handler on the headlet object
	 * 
	 * @param	Event		event
	 */
	onMenuClick: function(event) {
		var li	= event.findElement('li');
		if( li ) {
			var name	= li.id.split('-')[1];

			this._callHandler(name, 'onMenuClick', event);
		}

		event.stop();
	},



	/**
	 * Check if a headlet exists
	 * 
	 * @param	String		name
	 */
	exists: function(name) {
		return Todoyu.exists('headlet-' + name);
	},



	/**
	 * Set a headlet active
	 * 
	 * @param	String		name
	 */
	setActive: function(name) {
		$('headlets').select('li.headlet').invoke('removeClassName', 'active');
		$('headlet-' + name).addClassName('active');
	},	



	toggleContent: function(name, keepOthers) {
		this.getContent(name).toggle();
	},



	/**
	 * Check if content of a headlet is visible
	 * 
	 * @param	String		name
	 */
	isContentVisible: function(name) {
		return this.getContent(name).visible();
	},



	/**
	 * Show content of a headlet
	 * 
	 * @param	String		name
	 * @param	Boolean		keepOthers
	 */
	showContent: function(name, keepOthers) {		
		if( keepOthers === true ) {
			this.hideAllContent(name);
		}

		if( this.hasContent(name) ) {
			$('headlet-' + name + '-content').show();
		}
	},



	/**
	 * Hide content of a headlet
	 * 
	 * @param	String		name
	 */
	hideContent: function(name) {
		if( this.hasContent(name) ) {
			$('headlet-' + name + '-content').hide();
		}
	},



	/**
	 * Check if headlet has a content element
	 * 
	 * @param	String		name
	 */
	hasContent: function(name) {
		return Todoyu.exists('headlet-' + name + '-content');
	},



	/**
	 * Hide all content except
	 * 
	 * @param	String	exceptName
	 */
	hideAllContent: function(exceptName) {
			// Call hide function for all headlets
		$H(this.headlets).each(function(exceptName, pair){
			if( pair.key !== exceptName ) {
				this._callHandler(pair.key, 'hide');
			}
		}.bind(this, exceptName));
		
			// Hide content
		$('headlets').select('ul.content').each(function(name, headlet){
			if( headlet.id.split('-')[1] !== name ) {
				headlet.hide();
			}
		}.bind(this, exceptName));
	},



	/**
	 * Get headlet element
	 * 
	 * @param	String		name
	 * @return	Element
	 */
	getHeadlet: function(name) {
		return $('headlet-' + name);
	},



	/**
	 * Get button element of a headlet
	 * 
	 * @param	String		name
	 */
	getButton: function(name) {
		return $('headlet-' + name + '-button');
	},



	/**
	 * Get content element of a headlet
	 * 
	 * @param	String		name
	 */
	getContent: function(name) {
		return $('headlet-' + name.toLowerCase() + '-content');
	},



	/**
	 * Handler when clicked on body, fired by Todoyu.Ui.onBodyClick()
	 * If clicked outside the headlets, hide all content boxes
	 * 
	 * @param	Event		event
	 */
	onBodyClick: function(event) {		
		if( ! event.findElement('ul#headlets') ) {
			this.hideAllContent();
		}
	}

};