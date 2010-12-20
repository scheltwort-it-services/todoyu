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
 * Timerange object
 */
Todoyu.Timerange = Class.create({

	name: '',

	element: null,

	handles: [],

	slider: null,

	selectedDates: [],

	selectableDates: [],

	defaultOptions: {
		axis: 'horizontal',
		restricted: true
	},

	options: {
		dateFormat: '%d.%m.%Y'
	},



	/**
	 * Constructor
	 *
	 * @param	{String}			baseID
	 * @param	{Function|Array}	validDates
	 * @param	{Array}				selectedDates
	 * @param	{Object}			sliderOptions
	 * @param	{Object}			timerangeOptions
	 */
	initialize: function(baseID, validDates, selectedDates, sliderOptions, timerangeOptions) {
		this.name			= baseID;
		this.selectedDates	= selectedDates;
		this.options		= $H(this.options).update(timerangeOptions||{}).toObject();

		this.element	= $(baseID + '-slider');
		this.handles	= [
			$(this.name + '-handle-start'),
			$(this.name + '-handle-end')
		];

			// validDates can be a callback function, a range or a list of dates
		if( Object.isFunction(validDates) ) {
			this.selectableDates = validDates(this, selectableDates);
		} else if( Object.isArray(validDates) ) {
			if( validDates.size() === 2 ) {
				this.selectableDates = this.getDatesInRange(new Date(validDates[0]), new Date(validDates[1]));
			} else {
				this.selectableDates = validDates;
			}
		}

			// Merge default options with dynamic values and given config
		var options	= $H(this.defaultOptions).merge({
			range:  $R(0, this.selectableDates.length),
			values: $R(0, this.selectableDates.length),
			spans: [this.name + '-span']
		}).update(sliderOptions).toObject();

			// Replace given event handler with internals
		options.onChange= this.onChange.bind(this, options.onChange || Prototype.emptyFunction);
		options.onSlide	= this.onSlide.bind(this, options.onSlide || Prototype.emptyFunction);

			// Get indexes of selected values
		if( this.selectedDates ) {
			options.sliderValue	= [this.getIndex(this.selectedDates[0]), this.getIndex(this.selectedDates[1])];
		} else {
			options.sliderValue = [0, this.selectableDates.length-1];
		}

			// Create slider
		this.slider = new Control.Slider(this.handles, this.element, options);

		this.setFieldDate('start', this.getDate(options.sliderValue[0]));
		this.setFieldDate('end', this.getDate(options.sliderValue[1]));

			// Observe element for keyboard support
		this.observeElements();
	},



	/**
	 * Focus the key board dummy input
	 */
	focusForKeyboard: function() {
		$(this.name + '-focus').focus();
	},



	/**
	 * Observe track and key board field
	 */
	observeElements: function() {
			// On timerange click, set focus to dummy field
		this.element.observe('click', this.focusForKeyboard.bind(this));
			// Observe the dummy field to act own keypresses
		$(this.name + '-focus').observe('keydown', this.onKeyPress.bind(this));
		$(this.name + '-start').observe('keydown', this.onDateFieldKeyPress.bind(this));
		$(this.name + '-end').observe('keydown', this.onDateFieldKeyPress.bind(this));
		$(this.name + '-start').observe('change', this.onDateFieldChange.bind(this));
		$(this.name + '-end').observe('change', this.onDateFieldChange.bind(this));
	},



	/**
	 * Handler when key in fields are pressed
	 *
	 * @param	{Event}		event
	 */
	onDateFieldKeyPress: function(event) {
		var idParts	= event.element().id.split('-');
		var baseID	= idParts.slice(0, -1).join('-');
		var key		= idParts.last();

		this.setActiveHandle(baseID, key);

		this.onKeyPress(event, true);
	},



	/**
	 * Handler when date in fields is changed
	 *
	 * @param	{Event}		event
	 */
	onDateFieldChange: function(event) {
		var key			= event.element().id.split('-').last();
		var handleIndex	= this.getHandleIndex(key);
		var value		= $F(event.element());

		var date		= Date.parseDate($F(event.element()), this.options.dateFormat);
		var dateIndex	= this.getIndex(date.getTime());

		this.slider.setValue(dateIndex, handleIndex);
	},



	/**
	 * Callback for key presses to change date
	 *
	 * @param	{Event}		event
	 * @param	{Boolean}	dontStopEvent
	 */
	onKeyPress: function(event, dontStopEvent) {
		if( dontStopEvent !== true ) {
			event.stop();
		}

		var value	= this.getActiveHandleValue();
		var arrow	= false;

		switch(event.keyCode) {
			case Event.KEY_RIGHT:
				arrow = true;
				value++;
				break;

			case Event.KEY_LEFT:
				arrow = true;
				value--;
				break;

			case Event.KEY_UP:
				arrow = true;
				value = this.getIndexShiftedByMonth(value, true);
				break;

			case Event.KEY_DOWN:
				arrow = true;
				value = this.getIndexShiftedByMonth(value, false);
				break;
		}

			// Don't shift date if not an array key
		if( ! arrow ) {
			return ;
		}

		if( value < 0 ) {
			value = 0;
		}
		if( value >= this.selectableDates.length ) {
			value = this.selectableDates.length-1;
		}

		var handleIndex	= this.getHandleIndex(this.getSelectedHandleKey());

		this.slider.setValue(value, handleIndex);
	},



	/**
	 * Set active handle based on the given key
	 *
	 * @param	{String}	baseID		Base ID of the filter
	 * @param	{String}	key			Key of the field (start or end)
	 */
	setActiveHandle: function(baseID, key) {
		var handle	= $(baseID + '-handle-' + key);

		this.slider.activeHandle	= handle;
		this.slider.activeHandleIdy	= this.getHandleIndex(key);

		handle.up('.slider').select('.handle').invoke('removeClassName', 'selected');
		handle.addClassName('selected');
	},



	/**
	 * Get the index for the next or last month
	 *
	 * @param	{Number}	index		Index of the current date
	 * @param	{Boolean}	next		Next or last month
	 * @return	{Number}	Index of new date
	 */
	getIndexShiftedByMonth: function(index, next) {
		var currentDate	= this.getDate(index);
		var shiftedDate	= this.shiftMonth(currentDate, next);

		return this.getIndex(shiftedDate.getTime());
	},



	/**
	 * Shift date for next or last month
	 *
	 * @param	{Date}		date		Current date
	 * @param	{Boolean}	next		Next or last month
	 * @return	{Date}		New date
	 */
	shiftMonth: function(date, next) {
		var shift = next ? 1 : -1;

		return new Date(date.getFullYear(), date.getMonth()+shift, date.getDate(), 0, 0, 0);
	},



	/**
	 * Get value of the active drag handle
	 */
	getActiveHandleValue: function() {
			// Get key of handle
		var key		= this.getSelectedHandleKey();
			// Transform the key to array index
		var valueIndex = this.getHandleIndex(key);

		return this.slider.values[valueIndex];
	},

	getHandleIndex: function(key) {
		return {
			'start': 0,
			'end': 1
		}[key];
	},



	/**
	 * Get key of the selected handle
	 *
	 * @return	{String}
	 */
	getSelectedHandleKey: function() {
		return this.element.down('.selected').id.split('-').last();
	},



	/**
	 * Generate dates in a range
	 *
	 * @param	{Date}	dateStart
	 * @param	{Date}	dateEnd
	 * @return	{Array}	List of dates in the range
	 */
	getDatesInRange: function(dateStart, dateEnd) {
		var dates	= [];

		dateStart.setHours(0, 0, 0, 0);
		dateEnd.setHours(23, 59, 59, 0);
		var current = new Date(dateStart);

		dates.push(dateStart);

		while( current <= dateEnd ) {
			current.setDate(current.getDate()+1);

			dates.push(new Date(current));
		}

		return dates;
	},



	/**
	 * Get index if the
	 * @param time
	 */
	getIndex: function(time) {
		var i 		= 0;
		var dates 	= this.getDates();
		var length	= dates.length;
		var d		= new Date(time);
		d.setHours(0,0,0,0);

		for(i=0; i<length; i++) {
			if( dates[i].getTime() == d.getTime() ) {
				return i;
			}
		}

		return 0;
	},


	/**
	 * Get selectable dates
	 *
	 * @return	{Array}
	 */
	getDates: function() {
		return this.selectableDates;
	},



	/**
	 * Get date at a specific index
	 *
	 * @param	{Number}	index
	 * @return	{Date}
	 */
	getDate: function(index) {
		return this.selectableDates[index];
	},

	getDateFormatted: function(index) {
		return this.formatDate(this.getDate(index));
	},

	formatDate: function(date) {
		return date.print(this.options.dateFormat);
	},

	onChange: function(onChange, values) {
			// Enable keyboard
		this.focusForKeyboard();

		this.setFieldValues(values[0], values[1]);

		onChange(this, values);
	},

	onSlide: function(onSlide, values) {
		this.focusForKeyboard();

		this.setFieldValues(values[0], values[1]);

		onSlide(this, values);
	},


	setFieldValues: function(indexStart, indexEnd) {
		this.setFieldDate('start', this.getDate(indexStart));
		this.setFieldDate('end', this.getDate(indexEnd));
	},

	setFieldDate: function(key, date) {
		$(this.name + '-' + key).value = this.formatDate(date);
	}

});