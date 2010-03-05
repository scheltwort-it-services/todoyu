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

xxx = {
	init: function() {
		console.log('dummy init');
		
	}
};

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
	 * Initialize headlet management (observation)
	 */
	init: function() {
			// Observe all headlet elements
		$('headlets').select('li.headlet').invoke('observe', 'mouseover', this.onOverHeadlet.bindAsEventListener(this));
			// Observe headlet container
		$('headlets').observe('mouseover', this.onOverContainer.bindAsEventListener(this));
	},
	
		
	/**
	 * Add a headlet object
	 * 
	 * @param	String		name
	 * @param	Object		headletObject
	 */
	add: function(name, headletObject) {
		this.headlets[name.toLowerCase()] = headletObject;
		
		headletObject.init();
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
		return event.element().up('div.content') !== undefined;
	},
	
	
	
	/**
	 * Call the handler of a headlet if it has the specific function
	 * Possible handlers: onButtonClick, onContentClick, onMouseOver, onMouseOut
	 * 
	 * @param	String		name		Name of the headlet
	 * @param	String		type		Event type (handler name)
	 * @param	Event		event		Event object
	 */
	_callHandler: function(name, type, event) {
		var func	= this.headlets[name][type];
		var args	= [func, event];
				
		Todoyu.callIfExists.apply(Todoyu, args);
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
			
			if( this._isContentEvent(event) ) {
				type	= 'onContentClick';
			} else {
				type	= 'onButtonClick';
			}
			
			this._callHandler(name, type, event);
		}
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
	 * @param	Bool		keepOthers
	 */
	showContent: function(name, keepOthers) {		
		if( keepOthers === true ) {
			this.hideAllContent();
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
		if (this.hasContent(name)) {
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
	 * Hide all content elements
	 */
	hideAllContent: function() {
		$('headlets').select('div.content').invoke('hide');
	},
	
	
	
	/**
	 * 
	 * @param {Object} headlet
	 */
	setContentPosition: function(headlet) {
		alert("Implement 'setContentPosition' of Todoyu.Headlet");
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
	 * Get headlet button element
	 * @param {Object} name
	 */
	getButton: function(name) {
		return $('headlet-' + name + '-button');
	},
	
	getContent: function(name) {
		return $('headlet-' + name + '-content');
	}
	
};