<?php
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
 * Add jQuery to page
 *
 * @package		Todoyu
 * @subpackage	Core
 * @deprecated
 * @todo		Remove as soon as prototype adapter for highcharts works
 */
class TodoyuJQuery {

	/**
	 * Add jQuery assets to page and
	 */
	public static function addJQuery() {
			// Add jQuery source file
		TodoyuPage::addJavascript('lib/js/jquery/jquery.min.js', 10, false, false, false);

			// Prevent prototype conflicts
		TodoyuPage::addAdditionalHeaderData('<script type="text/javascript">Todoyu.jQueryNoConflict();</script>');
	}

}

?>