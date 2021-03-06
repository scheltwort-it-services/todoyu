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
 * Installer
 *
 * @package		Todoyu
 * @subpackage	InstallerRenderer
 */
class TodoyuInstallerRenderer {

	/**
	 * Get version details for views' data array
	 *
	 * 	@return Array
	 */
	private static function getVersionData() {
		return	array(
			'versionnumber'	=> TODOYU_VERSION,
			'versiondate'	=> TODOYU_UPDATE
		);
	}



	/**
	 * Render welcome screen
	 *
	 * @param	String	$error
	 */
	public static function renderWelcome($error = '') {
		$data	= array(
			'title'		=> 'Welcome to the Todoyu installer',
			'textclass'	=> 'text textInfo',
			'version'	=> self::getVersionData()
		);
		$tmpl	= 'install/view/01_welcome.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render server check screen
	 *
	 * @param	String	$error
	 */
	public static function renderServercheck($error = '') {
		$next			= true;

		if( version_compare(PHP_VERSION, '5.2.0', '>=') ) {
			$versionStatus	= 'OK';
		} else {
			$versionStatus	= 'PROBLEM';
			$next			= false;
		}

		$writable		= array(
			'files',
			'config',
			'cache/tmpl/compile',
			'config/db.php',
			'config/extensions.php',
			'config/extconf.php'
		);
		$writableStatus	= array();

		foreach($writable as $path) {
			$absPath	= PATH . '/' . $path;

			TodoyuFileManager::setDefaultAccessRights($absPath);

			$writableStatus[$path]	= is_writable($absPath);

			if( $writableStatus[$path] === false ) {
				$next = false;
			}
		}

		$data	= array(
			'title'				=> 'Server check',
			'phpversionStatus'	=> $versionStatus,
			'phpversion'		=> '(Your version: ' . PHP_VERSION . ')',
			'writable'			=> $writableStatus,
			'next'				=> $next
		);
		$tmpl	= 'install/view/02_servercheck.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render database connection screen
	 *
	 * @param	String	$error
	 */
	public static function renderDbConnection($error = '') {
		$dbData	= $_SESSION['todoyuinstaller']['db'];

		$data	= array(
			'title'		=> 'Setup Database Server Connection',
			'fields'	=> $dbData,
			'version'	=> self::getVersionData()
		);

		if( is_array($dbData) ) {
			try {
				TodoyuDbAnalyzer::checkDbConnection($dbData);
			} catch(Exception $e) {
				$data['textclass']	= 'text textError';
				$data['text']		= 'Error: ' . $e->getMessage();
			}
		}

		$tmpl	= 'install/view/03_dbconnection.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render DB select screen
	 *
	 * @param	String	$error
	 */
	public static function renderDbSelect($error = '')	{
		$dbData	= $_SESSION['todoyuinstaller']['db'];

		$data	= array(
			'title'		=> 'Setup Database',
			'version'	=> self::getVersionData()
		);

		if( is_array($dbData) ) {
			try {
				$data['options'] = TodoyuDbAnalyzer::getAvailableDatabases($dbData);
			} catch(Exception $e) {
				$data['textclass'] = 'text textError';
				$data['text'] = 'Error: ' . $e->getMessage();
			}

			if( strlen($error) > 0 )	{
				$data['textclass'] = 'text textError';
				$data['text'] = 'Error: ' . $error;
			}
		}

		$tmpl	= 'install/view/04_dbselect.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render import static screen
	 *
	 * @param	String	$error
	 */
	public static function renderImportStatic($error = '') {
		$data	= array(
			'title'		=> 'Import static data',
			'version'	=> self::getVersionData(),
			'coreStructure'	=> TodoyuInstallerDbHelper::getCoreDBstructures(),
			'extStructure'	=> TodoyuInstallerDbHelper::getExtDBstructures()
		);
		$tmpl	= 'install/view/05_importstatic.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render config screen
	 *
	 * @param	String	$error
	 */
	public static function renderConfig($error = '') {
		$data	= array(
			'title'		=> 'System config',
			'text'		=> $error ? $error : '',
			'textClass'	=> $error ? 'text textError' : '',
			'version'	=> self::getVersionData()
		);

		$tmpl	= 'install/view/06_config.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render admin password change screen
	 *
	 * @param	String	$error
	 */
	public static function renderAdminPassword($error = '') {
		$data	= array(
			'title'		=> 'Change admin password',
			'text'		=> strlen($error) > 0 ? $error : 'Change admin password for your security!',
			'textClass'	=> strlen($error) > 0 ?  'text textError' : 'text textInfo',
			'version'	=> self::getVersionData()
		);

		$tmpl	= 'install/view/07_adminpassword.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render finishing screen
	 */
	public static function renderFinish() {
		$data	= array(
			'title'		=> 'Installation finished',
			'version'	=> self::getVersionData()
		);
		$tmpl	= 'install/view/08_finish.tmpl';

		return render($tmpl, $data);
	}




	public static function renderWelcomeToUpdate($error = '') {
		$data	= array(
			'title'		=> 'Welcome to todoyu datebase update',
			'version'	=> self::getVersionData()
		);

		$tmpl	= 'install/view/09_welcometoupdate.tmpl';

		return render($tmpl, $data);
	}



	/**
	 * Render welcome screen
	 *
	 * @param	String	$error
	 */
	public static function renderDBstructureCheck($error = '') {
		$dbDiff	= TodoyuInstallerDbHelper::getDBstructureDiff();

			// DB structure is NOT up-to-date! offer updating
		$data	= array(
			'title'		=> 'Welcome to the Todoyu installer',
			'textclass'	=> 'text textInfo',
			'version'	=> self::getVersionData(),
			'diffs'		=> $dbDiff
		);
		$tmpl	= 'install/view/10_dbchanges.tmpl';

		return render($tmpl, $data);
	}



   /**
 	* Render update finished screen
 	*
 	*	@param	String	$error
 	*/
	public static function renderUpdateFinished($error = '') {
		$data	= array(
			'title'		=> 'Update finished',
			'version'	=> self::getVersionData()
		);

		$tmpl	= 'install/view/11_finishupdate.tmpl';
		return render($tmpl, $data);
	}

}
?>