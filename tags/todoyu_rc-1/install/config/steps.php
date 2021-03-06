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
 * Setup installer steps (installation, updating) order
 *
 * @package		Todoyu
 * @subpackage	Installer
 */


$CONFIG['INSTALLER']['install'] = array(
	'install',
	'servercheck',
	'dbconnection',
	'dbselect',
	'importtables',
	'systemconfig',
	'adminpassword',
	'finish'
);

$CONFIG['INSTALLER']['update'] = array(
	'update',
	'updatetocurrentversion',
	'finishupdate'
);


$CONFIG['INSTALLER']['steps'] = array(
		// Installation steps
	'install' => array(
		'process'	=> 'TodoyuInstallerManager::processInstall',
		'render'	=> 'TodoyuInstallerRenderer::renderInstall',
		'tmpl'		=> '01_install.tmpl'
	),
	'servercheck' => array(
			// Check server compatibility
		'process'	=> 'TodoyuInstallerManager::processServercheck',
		'render'	=> 'TodoyuInstallerRenderer::renderServercheck',
		'tmpl'		=> '02_servercheck.tmpl',
		'fileCheck'	=> array(
			'files',
			'config',
			'cache/tmpl/compile',
			'config/db.php',
			'config/extensions.php',
			'config/extconf.php'
		)
	),
	'dbconnection' => array(
			// Configure DB connection details
		'process'	=> 'TodoyuInstallerManager::processDbconnection',
		'render'	=> 'TodoyuInstallerRenderer::renderDbConnection',
		'tmpl'		=> '03_dbconnection.tmpl'
	),
	'dbselect' => array(
			// Configure to select existing or create new DB. Save DB connection data
		'process'	=> 'TodoyuInstallerManager::processDbSelect',
		'render'	=> 'TodoyuInstallerRenderer::renderDbSelect',
		'tmpl'		=> '04_dbselect.tmpl'
	),
	'importtables' => array(
			// Preview static data, than import it
		'process'	=> 'TodoyuInstallerManager::proccessImportTables',
		'render'	=> 'TodoyuInstallerRenderer::renderImportTables',
		'tmpl'		=> '05_importtables.tmpl'
	),
	'systemconfig' => array(
			// Update system config file (/config/system.php)
		'process'	=> 'TodoyuInstallerManager::procesSystemConfig',
		'render'	=> 'TodoyuInstallerRenderer::renderSytemConfig',
		'tmpl'		=> '06_systemconfig.tmpl'
	),
	'adminpassword' => array(
		'process'	=> 'TodoyuInstallerManager::processAdminPassword',
		'render'	=> 'TodoyuInstallerRenderer::renderAdminPassword',
		'tmpl'		=> '07_adminpassword.tmpl'
	),
	'finish' => array(
		'process'	=> 'TodoyuInstallerManager::processFinish',
		'render'	=> 'TodoyuInstallerRenderer::renderFinish',
		'tmpl'		=> '08_finish.tmpl'
	),



		// ------------------------ Update steps ---------------------
	'update' => array(
		'process'	=> 'TodoyuInstallerManager::processUpdate',
		'render'	=> 'TodoyuInstallerRenderer::renderUpdate',
		'tmpl'		=> '09_update.tmpl'
	),
	'updatetocurrentversion' => array(
			// Mandatory version updates
		'process'	=> 'TodoyuInstallerManager::processUpdateToCurrentVersion',
		'render'	=> 'TodoyuInstallerRenderer::renderUpdateToCurrentVersion',
		'tmpl'		=> '10_updatetocurrentversion.tmpl',
	),
	'finishupdate' => array(
		'process'	=> 'TodoyuInstallerManager::processFinishUpdate',
		'render'	=> 'TodoyuInstallerRenderer::renderFinishUpdate',
		'tmpl'		=> '12_finishupdate.tmpl'
	)
);

?>