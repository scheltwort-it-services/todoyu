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
 * General helper functions
 *
 * @namespace	Todoyu.Helper
 */
Todoyu.Helper = {

	/**
	 * Convert value to Integer
	 *
	 * @param	{String|Boolean|Number}		mixedvar
	 */
	intval: function(mixedvar) {
		var type = typeof( mixedvar );
		var temp;

		switch(type) {
			case 'boolean':
				return mixedvar ? 1 : 0;

			case 'string':
				temp = parseInt(mixedvar, 10);
				return isNaN(temp) ? 0 : temp;

			case 'number':
				return Math.floor(mixedvar);

			default:
				return 0;
		}
	},



	/**
	 * Convert to 2-digit value (possibly add leading zero)
	 *
	 * @param	{String|Number}		number
	 * @return	{String}
	 */
	twoDigit: function(number) {
		number = parseInt(number, 10);

		if( number < 10 ) {
			number = '0' + number;
		}

		return number;
	},



	/**
	 * Toggle source of image
	 *
	 * @param	{String}		idImage
	 * @param	{String}		src1
	 * @param	{String}		src2
	 */
	toggleImage: function(idImage, src1, src2) {
		var image = $(idImage);

		if( image.src.indexOf(src1) === -1 ) {
			image.src = src1;
		} else {
			image.src = src2;
		}
	},



	/**
	 * Round with given precision
	 *
	 * @param	{Number}		value
	 * @param	{Number}	precision
	 * @return	{Number}
	 */
	round: function(value, precision) {
		value		= parseFloat(value);
		precision	= this.intval(precision);
		var factor	= Math.pow(10, precision);

		return Math.round((value*factor))/factor;
	},



	/**
	 * Uppercase the first character of every word in a string
	 *
	 * @param	{String}	str
	 * @return	{String}
	 */
	ucwords: function(str) {
		return (str + '').replace(/^(.)|\s(.)/g, function ($1) {
			return $1.toUpperCase();
		});
	},



	/**
	 * Returns the internal translation table used by htmlspecialchars and htmlentities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @param	{String}	table
	 * @param	{String}	quote_style
	 */
	get_html_translation_table:function(table, quote_style) {
			var entities = {}, hash_map = {}, decimal = 0, symbol = '';
			var constMappingTable = {}, constMappingQuoteStyle = {};
			var useTable = {}, useQuoteStyle = {};

			// Translate arguments
			constMappingTable[0]		= 'HTML_SPECIALCHARS';
			constMappingTable[1]		= 'HTML_ENTITIES';
			constMappingQuoteStyle[0]	= 'ENT_NOQUOTES';
			constMappingQuoteStyle[2]	= 'ENT_COMPAT';
			constMappingQuoteStyle[3]	= 'ENT_QUOTES';

			useTable		= !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
			useQuoteStyle	= !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

			if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
					throw new Error("Table: "+useTable+' not supported');
					// return false;
			}

			entities['38'] = '&amp;';
			if (useTable === 'HTML_ENTITIES') {
					entities['160'] = '&nbsp;';
					entities['161'] = '&iexcl;';
					entities['162'] = '&cent;';
					entities['163'] = '&pound;';
					entities['164'] = '&curren;';
					entities['165'] = '&yen;';
					entities['166'] = '&brvbar;';
					entities['167'] = '&sect;';
					entities['168'] = '&uml;';
					entities['169'] = '&copy;';
					entities['170'] = '&ordf;';
					entities['171'] = '&laquo;';
					entities['172'] = '&not;';
					entities['173'] = '&shy;';
					entities['174'] = '&reg;';
					entities['175'] = '&macr;';
					entities['176'] = '&deg;';
					entities['177'] = '&plusmn;';
					entities['178'] = '&sup2;';
					entities['179'] = '&sup3;';
					entities['180'] = '&acute;';
					entities['181'] = '&micro;';
					entities['182'] = '&para;';
					entities['183'] = '&middot;';
					entities['184'] = '&cedil;';
					entities['185'] = '&sup1;';
					entities['186'] = '&ordm;';
					entities['187'] = '&raquo;';
					entities['188'] = '&frac14;';
					entities['189'] = '&frac12;';
					entities['190'] = '&frac34;';
					entities['191'] = '&iquest;';
					entities['192'] = '&Agrave;';
					entities['193'] = '&Aacute;';
					entities['194'] = '&Acirc;';
					entities['195'] = '&Atilde;';
					entities['196'] = '&Auml;';
					entities['197'] = '&Aring;';
					entities['198'] = '&AElig;';
					entities['199'] = '&Ccedil;';
					entities['200'] = '&Egrave;';
					entities['201'] = '&Eacute;';
					entities['202'] = '&Ecirc;';
					entities['203'] = '&Euml;';
					entities['204'] = '&Igrave;';
					entities['205'] = '&Iacute;';
					entities['206'] = '&Icirc;';
					entities['207'] = '&Iuml;';
					entities['208'] = '&ETH;';
					entities['209'] = '&Ntilde;';
					entities['210'] = '&Ograve;';
					entities['211'] = '&Oacute;';
					entities['212'] = '&Ocirc;';
					entities['213'] = '&Otilde;';
					entities['214'] = '&Ouml;';
					entities['215'] = '&times;';
					entities['216'] = '&Oslash;';
					entities['217'] = '&Ugrave;';
					entities['218'] = '&Uacute;';
					entities['219'] = '&Ucirc;';
					entities['220'] = '&Uuml;';
					entities['221'] = '&Yacute;';
					entities['222'] = '&THORN;';
					entities['223'] = '&szlig;';
					entities['224'] = '&agrave;';
					entities['225'] = '&aacute;';
					entities['226'] = '&acirc;';
					entities['227'] = '&atilde;';
					entities['228'] = '&auml;';
					entities['229'] = '&aring;';
					entities['230'] = '&aelig;';
					entities['231'] = '&ccedil;';
					entities['232'] = '&egrave;';
					entities['233'] = '&eacute;';
					entities['234'] = '&ecirc;';
					entities['235'] = '&euml;';
					entities['236'] = '&igrave;';
					entities['237'] = '&iacute;';
					entities['238'] = '&icirc;';
					entities['239'] = '&iuml;';
					entities['240'] = '&eth;';
					entities['241'] = '&ntilde;';
					entities['242'] = '&ograve;';
					entities['243'] = '&oacute;';
					entities['244'] = '&ocirc;';
					entities['245'] = '&otilde;';
					entities['246'] = '&ouml;';
					entities['247'] = '&divide;';
					entities['248'] = '&oslash;';
					entities['249'] = '&ugrave;';
					entities['250'] = '&uacute;';
					entities['251'] = '&ucirc;';
					entities['252'] = '&uuml;';
					entities['253'] = '&yacute;';
					entities['254'] = '&thorn;';
					entities['255'] = '&yuml;';
			}

			if (useQuoteStyle !== 'ENT_NOQUOTES') {
					entities['34'] = '&quot;';
			}
			if (useQuoteStyle === 'ENT_QUOTES') {
					entities['39'] = '&#39;';
			}
			entities['60'] = '&lt;';
			entities['62'] = '&gt;';

			// ascii decimals to real symbols
			for (decimal in entities) {
					symbol = String.fromCharCode(decimal);
					hash_map[symbol] = entities[decimal];
			}

			return hash_map;
	},



	/**
	 * Convert all HTML entities to their applicable characters
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @param	{String}	string
	 * @param	{String}	quote_style
	 */
	html_entity_decode:function(string, quote_style) {
			var hash_map = {}, symbol = '', tmp_str = '', entity = '';
			tmp_str = string.toString();

			if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
					return false;
			}

			// fix &amp; problem
			// http://phpjs.org/functions/get_html_translation_table:416#comment_97660
			delete(hash_map['&']);
			hash_map['&'] = '&amp;';

			for (symbol in hash_map) {
					entity = hash_map[symbol];
					tmp_str = tmp_str.split(entity).join(symbol);
			}
			tmp_str = tmp_str.split('&#039;').join("'");

			return tmp_str;
	},



	/**
	 * Convert all applicable characters to HTML entities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/htmlentities
	 * version: 1009.2513
	 *
	 * @param	{String}		string
	 * @param	{String}		quote_style
	 */
	htmlentities:function(string, quote_style) {
			var hash_map = {}, symbol = '', tmp_str = '', entity = '';
			tmp_str = string.toString();

			if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
					return false;
			}
			hash_map["'"] = '&#039;';
			for (symbol in hash_map) {
					entity = hash_map[symbol];
					tmp_str = tmp_str.split(symbol).join(entity);
			}

			return tmp_str;
	},



	/**
	 * Wraps buffer to selected number of characters using string break char
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	wordwrap
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 */
	wordwrap: function(str, int_width, str_break, cut) {
			var m = ((arguments.length >= 2) ? arguments[1] : 75   );
			var b = ((arguments.length >= 3) ? arguments[2] : "\n" );
			var c = ((arguments.length >= 4) ? arguments[3] : false);

			var i, j, l, s, r;

			str += '';

			if (m < 1) {
					return str;
			}

			for (i = -1, l = (r = str.split(/\r\n|\n|\r/)).length; ++i < l; r[i] += s) {
					for (s = r[i], r[i] = ""; s.length > m; r[i] += s.slice(0, j) + ((s = s.slice(j)).length ? b : "")){
							j = c == 2 || (j = s.slice(0, m + 1).match(/\S*(\s)?$/))[1] ? m : j.input.length - j[0].length || c == 1 && m || j.input.length + (j = s.slice(m).match(/^\S*/)).input.length;
					}
			}

			return r.join("\n");
	},



	/**
	 * Wraps buffer to selected number of characters using string break char,
	 * while keeping HTML entities intact
	 *
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 */
	wordwrapEntities: function(str, int_width, str_break, cut) {
			str		= this.html_entity_decode(str);
			str		= this.wordwrap(str, int_width, str_break, cut);
			str		= this.htmlentities(str);

			return str;
	},



	/**
	 * Fire event
	 *
	 * @param	{Element}		element
	 * @param	{String}		event e.g. 'click'
	 * @return	{String|Object}
	 */
	fireEvent: function(element, event){
		var evt;

		if(document.createEventObject){
				// dispatch for IE
			evt = document.createEventObject();

			return element.fireEvent('on' + event, evt);
		} else {
				// dispatch for firefox + others
			evt = document.createEvent('HTMLEvents');
			evt.initEvent(event, true, true ); // event type, bubbling, cancelable

			return ! element.dispatchEvent(evt);
		}
	},



	/**
	 * Check whether client is given browser (e.g. 'chrome', 'safari')
	 *
	 * @param	{String}	browserName
	 */
	isNavigatorUserAgent: function(browserName) {
		browserName	= browserName.toLowerCase();

		return navigator.userAgent.toLowerCase().indexOf(browserName) > -1;
	},



	/**
	 * Check whether used client browser is google chrome
	 *
	 * @return	{Boolean}
	 */
	isChrome: function() {
		return this.isNavigatorUserAgent('chrome');
	},



	/**
	 * Check whether used client browser is apple safari
	 *
	 * @return	{Boolean}
	 */
	isSafari: function() {
		return this.isNavigatorUserAgent('safari');
	},



	/**
	 * Set element scrollTop, circumventing refresh bug in safari + chrome
	 *
	 * @param	{Element}	element
	 * @param	{Number}	position
	 */
	setScrollTop: function(element, position) {
		element.scrollTop = position;

		if( this.isChrome() || this.isSafari() ) {
			this.onUpdateChromeSafariScrollTop(element.id, 0);
		}
	},



	/**
	 * Safari + Chrome workaround: defered window refresh to update after modification of scrollTop
	 *
	 * @param	{String}	elementID
	 * @param	{Number}	step
	 */
	onUpdateChromeSafariScrollTop: function(elementID, step) {
		switch(step) {
			case 0: case 1:
				$(elementID).style.overflow = ( step == 0 ) ? 'scroll' : '';
				break;
			case 2: case 3:
				window.scrollBy(0,( step == 2 ) ? 1 : -1 );
				break;
		}

		step++;
		if( step < 4 ) {
			this.onUpdateChromeSafariScrollTop.defer(elementID, step)
		}
	}

};