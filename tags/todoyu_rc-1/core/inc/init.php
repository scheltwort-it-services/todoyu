<?php
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

/**
 * Add extra assets for browsers below ie7
 * @todo	Use hook functions to allow others to hook in here
 */

	// Add form include path
Todoyu::addIncludePath( PATH_CORE . '/form' );

	// Set default timezone
date_default_timezone_set($CONFIG['LOCALE']['defaultTimezone']);

	// Init basic classes
Todoyu::init();

	// Register core localization file
TodoyuLanguage::register('core', PATH_CORE . '/locale/core.xml');
TodoyuLanguage::register('date', PATH_CORE . '/locale/date.xml');
TodoyuLanguage::register('file', PATH_CORE . '/locale/file.xml');
TodoyuLanguage::register('form', PATH_CORE . '/locale/form.xml');
TodoyuLanguage::register('locale', PATH_CORE . '/locale/locale.xml');
TodoyuLanguage::register('dateformat', PATH_CORE . '/config/dateformat.xml');

	// Register static_... tables' localization files
TodoyuLanguage::register('static_country', PATH_CORE . '/locale/static_country.xml');
TodoyuLanguage::register('static_country_zone', PATH_CORE . '/locale/static_country_zone.xml');
TodoyuLanguage::register('static_currency', PATH_CORE . '/locale/static_currency.xml');
TodoyuLanguage::register('static_territory', PATH_CORE . '/locale/static_territory.xml');
TodoyuLanguage::register('static_language', PATH_CORE . '/locale/static_language.xml');

	// Load extensions
require( PATH_CORE . '/inc/load_extensions.php' );

	// Custom config overrides
require_once( PATH_LOCALCONF . '/override.php');

?>