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
 * Superglobal object to access important data and objects
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class Todoyu {


	/**
	 * Database object instance
	 *
	 * @var	Database
	 */
	private static $db;


	/**
	 * Templating engine. Currenty Dwoo
	 *
	 * @var	Dwoo
	 */
	private static $template;

	/**
	 * Log object instance
	 *
	 * @var	Logger
	 */
	private static $logger;

	/**
	 * Currently logged in user
	 *
	 * @var	User
	 */
	private static $user;



	/**
	 * Initialize static Todoyu class
	 *
	 */
	public static function init() {
			// Init Locale for locallang files
		TodoyuLanguage::setLanguage(self::getLang());

			// Set system locale with setlocale
		self::setSystemLocale();
	}





	/**
	 * Set system locale with setlocale() based on the currently selected language
	 *
	 */
	public static function setSystemLocale() {
		$locale	= self::getLocale();


			// Check if locale exists
		if( ! TodoyuLocaleManager::hasLocale($locale) ) {
			$locale	= TodoyuLocaleManager::getDefaultLocale();
		}

			// Set locale
		$status	= TodoyuLocaleManager::setLocale($locale);

			// Log if operation fails
		if( $status === false ) {
			self::log('Can\'t set locale for language "' . $locale . '"', LOG_LEVEL_ERROR);
		}
	}



	/**
	 * Return database object
	 *
	 * @return	TodoyuDatabase
	 */
	public static function db() {
		if( is_null(self::$db) ) {
			self::$db = TodoyuDatabase::getInstance($GLOBALS['CONFIG']['DB']);
		}

		return self::$db;
	}



	/**
	 * Return templateing engine
	 *
	 * @return	Dwoo
	 */
	public static function tmpl() {
		if( is_null(self::$template) ) {
			$config	= TodoyuArray::assure($GLOBALS['CONFIG']['TEMPLATE']);

				// Make needed folders
			TodoyuFileManager::makeDirDeep($config['compile']);
			TodoyuFileManager::makeDirDeep($config['cache']);

				// Initialize Dwoo
			try {
				self::$template = new Dwoo($config['compile'], $config['cache']);
			} catch(Dwoo_Exception $e) {
				$msg	= 'Can\'t initialize tempalate engine: ' . $e->getMessage();
				Todoyu::log($msg, LOG_LEVEL_FATAL);
				die($msg);
			}
		}

		return self::$template;
	}



	/**
	 * Add directory for plugins to dwoo
	 *
	 * @param	String		$directory
	 */
	public static function addDwooPluginDir($directory) {
		$directory	= TodoyuFileManager::pathAbsolute($directory);

		self::$template->getLoader()->addDirectory($directory);
	}



	/**
	 * Save log message (can be stored in multiple systems)
	 *
	 * @param	String		$message		Log message
	 * @param	Integer		$level			Log level (use constants!)
	 * @param	Mixed		$data			Additional data to save with the log message
	 */
	public static function log($message, $level = 0, $data = null) {
		if( is_null(self::$logger) ) {
			self::$logger = TodoyuLogger::getInstance($GLOBALS['CONFIG']['LOG']['active'], $GLOBALS['CONFIG']['LOG']['level']);
		}

		self::$logger->log($message, $level, $data);
	}



	/**
	 * Return current user object
	 *
	 * @return	TodoyuUser
	 */
	public static function user() {
		if( is_null(self::$user) ) {
			self::$user = TodoyuAuth::getUser();
		}

		return self::$user;
	}



	/**
	 * Reset user object if a new user is logged in
	 *
	 */
	public static function resetUser() {
		self::$user = TodoyuAuth::getUser(true);
	}



	/**
	 * Get system language
	 * If user is logged in, its preference language
	 * If not logged in but the browser provides a language, use browser language
	 * Fallback is the language defined in config
	 *
	 * @return	String
	 */
	public static function getLang() {
		$lang	= $GLOBALS['CONFIG']['SYSTEM']['language'];

		if( TodoyuAuth::isLoggedIn() ) {
			$lang = self::user()->getLanguage();
		} else {
			$browserLang = TodoyuBrowserInfo::getBrowserLanguage();

			if( $browserLang !== false ) {
				$lang = $browserLang;
			}
		}

		return $lang;
	}



	/**
	 * Get system locale
	 *
	 * @return	String
	 */
	public static function getLocale() {
		return $GLOBALS['CONFIG']['SYSTEM']['locale'];
	}



	/**
	 * Get (EXTID value of) current ext area
	 *
	 * @param	String	$area
	 * @return	Integer
	 */
	public static function getArea($area = null) {
		if( is_null($area) ) {
			$area = EXT;
		}

		return TodoyuExtensions::getExtID($area);
	}



	/**
	 * Get area key (string version)
	 *
	 * @return	String
	 */
	public static function getAreaKey() {
		return TodoyuRequest::getArea();
	}



	/**
	 * Add a path to the global include path
	 * for autoloading classes
	 *
	 * @param	String		$includePath
	 */
	public static function addIncludePath($includePath) {
		$includePath	= TodoyuFileManager::pathAbsolute($includePath);

		if( ! in_array($includePath, $GLOBALS['CONFIG']['AUTOLOAD']) ) {
			$GLOBALS['CONFIG']['AUTOLOAD'][] = $includePath;
		}
	}



	/**
	 * Autoload classes. Check all configured directories
	 *
	 * @param	String		$className
	 */
	public static function autoloader($className) {
		$classFile = $className . '.class.php';

		foreach($GLOBALS['CONFIG']['AUTOLOAD'] as $includePath) {
			if( is_file($includePath . DIRECTORY_SEPARATOR . $classFile) ) {
				include_once($includePath . DIRECTORY_SEPARATOR . $classFile);
				break;
			}
		}
	}

}

?>