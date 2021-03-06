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
 * Helper class to configure rendering of a full page frontend
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuFrontend {

	/**
	 * Get active tab
	 *
	 * @return	String
	 */
	public static function getActiveTab() {
		$tab	= TodoyuPreferenceManager::getPreference(0, 'tab');

		if( $tab === false ) {
			$tab = self::getDefaultTab();
		}

		return $tab;
	}



	/**
	 * Get active tab submenu tab
	 *
	 * @param	String		$parentTab
	 * @return	String
	 */
	public static function getActiveSubmenuTab($parentTab) {
		$tab	= TodoyuPreferenceManager::getPreference(0, 'tabSubmenu_' . $parentTab);

		if( $tab === false ) {
			$tab = self::getDefaultTab();
		}

		return $tab;
	}



	/**
	 * Set active tab (and save in preferences)
	 *
	 * @param	String		$activeTab
	 */
	public static function setActiveTab($activeTab) {
		TodoyuPreferenceManager::savePreference(0, 'tab', $activeTab, 0, true);
	}



	/**
	 * Set active tab (and save in preferences)
	 *
	 * @param	String		$activeTab
	 * @param	String		$parentTab
	 */
	public static function setActiveSubmenuTab($parentTab, $activeTab) {
		$idPerson = personid();

		TodoyuPreferenceManager::savePreference(0, 'tabSubmenu_' . $parentTab, $activeTab, 0, true, $idPerson);
	}



	/**
	 * Get default active tab. Because we remember the last tab,
	 * this is only a fallback for new users
	 *
	 * @return	String
	 */
	public static function getDefaultTab() {
		return Todoyu::$CONFIG['FE']['TAB']['default'];
	}



	/**
	 * Set default tab
	 *
	 * @param	String		$defaultTab
	 */
	public static function setDefaultTab($defaultTab) {
		Todoyu::$CONFIG['FE']['TAB']['default'] = $defaultTab;
	}



	/**
	 * Add a new tab to the configuration
	 *
	 * @param	String		$key
	 * @param	String		$label
	 * @param	String		$href
	 * @param	Integer		$position
	 * @param	String		$target
	 */
	public static function addMenuEntry($key, $label, $href, $position = 50, $target = '') {
		if( ! is_array(Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]) ) {
			Todoyu::$CONFIG['FE']['NAVI']['entries'][$key] = array();
		}

		Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['key']		= $key;
		Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['label']		= $label;
		Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['href']		= $href;

		if( ! isset(Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['position']) ) {
			Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['position']	= $position;
		}

		if( $target !== '' ) {
			Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]['target']	= $target;
		}
	}



	/**
	 * Add multiple sub menu entries from given config array
	 *
	 * @param	String		$extKey
	 * @param	String		$parentKey
	 * @param	Array		$itemsConfig
	 * @param	String		$labelPrefix
	 */
	public static function addSubMenuEntriesFromTabsConf($extKey, $parentKey, array $itemsConfig, $labelPrefix = '') {
		foreach($itemsConfig as $itemKey => $itemConfig) {
				// Check for access rights
			if( array_key_exists('require', $itemConfig) ) {
				$required	= explode('.', $itemConfig['require']);
				$allowed	= allowed($required[0], $required[1]);
			} else {
				$allowed	= true;
			}

				// Add entry
			if( $allowed ) {
				$entryKey	= $extKey . ucfirst($itemKey);
				$href		= '?ext=' . $extKey . '&tab=' . $itemKey;
				$position	= intval($itemConfig['position']);
				$label		= Label($extKey . '.ext.subMenuEntry.' . $itemKey);

				self::addSubmenuEntry($parentKey, $entryKey, $labelPrefix . $label, $href, $position);
			}
		}
	}



	/**
	 * Add a sub menu tab
	 *
	 * @param	String		$parentKey
	 * @param	String		$key
	 * @param	String		$label
	 * @param	String		$href
	 * @param	Integer		$position
	 * @param	String		$type
	 */
	public static function addSubmenuEntry($parentKey, $key, $label, $href, $position = 50, $type = '') {
		Todoyu::$CONFIG['FE']['NAVI']['entries'][$parentKey]['submenu'][] = array(
			'key'		=> $key,
			'label'		=> Label($label),
			'href'		=> $href,
			'position'	=> $position,
			'type'		=> $type,
		);
	}



	/**
	 * Remove a menu entry
	 *
	 * @param	String		$key		Entry key
	 */
	public static function removeMenuEntry($key) {
		unset(Todoyu::$CONFIG['FE']['NAVI']['entries'][$key]);
	}



	/**
	 * Get sub menu tabs
	 *
	 * @param	String		$parentKey
	 * @return	Array
	 */
	public static function getSubmenuTabs($parentKey) {
		$subMenu	= Todoyu::$CONFIG['FE']['TAB']['tabs'][$parentKey]['submenu'];
		$active		= self::getActiveSubmenuTab($parentKey);

		if( is_array($subMenu) ) {
			foreach($subMenu as $key => $vals) {
				if($vals['key'] == $active) {
					$subMenu[$key]['active'] = true;
				}
			}
			$subMenu = TodoyuArray::sortByLabel($subMenu, 'position');
		} else {
			$subMenu = array();
		}

		return $subMenu;
	}



	/**
	 * Get configured tabs with parsed labels and sorted by position
	 *
	 * @return	Array
	 */
	public static function getMenuEntries() {
		$tabs	= Todoyu::$CONFIG['FE']['NAVI']['entries'];

		$active	= self::getActiveTab();

		if( array_key_exists($active, $tabs) ) {
			$tabs[$active]['active'] = true;
		}

			// Get label for menu entry and sort sub menus.
		foreach($tabs as $index => $tab) {
			$tabs[$index]['label'] = Label($tabs[$index]['label']);

			if( $tabs[$index]['submenu'] ) {
					// sort by 'position', remove duplicate entries
				$tabs[$index]['submenu'] = TodoyuArray::sortByLabel($tabs[$index]['submenu'], 'position', false, false, false, SORT_REGULAR, 'href');
			}
		}

		$tabs = TodoyuArray::sortByLabel($tabs, 'position');

		return $tabs;
	}



	/**
	 * Set default frontend view
	 *
	 * @param	String	$ext
	 * @param	String	$controller
	 */
	public static function setDefaultView($ext, $controller) {
		Todoyu::$CONFIG['FE']['DEFAULT'] = array(
			'ext'		=> $ext,
			'controller'=> $controller
		);
	}

}

?>