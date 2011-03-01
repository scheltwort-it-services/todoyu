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
 * Country
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCountry extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer		$idCountry
	 */
	public function __construct($idCountry) {
		parent::__construct($idCountry, 'static_country');
	}



	/**
	 * Get phone code of country
	 *
	 * @return	String
	 */
	public function getPhoneCode() {
		return $this->get('phone');
	}



	/**
	 * Get ISO alpha2 code of country
	 *
	 * @return	String
	 */
	public function getCode2() {
		return $this->get('iso_alpha2');
	}



	/**
	 * Get ISO alpha2 code of country
	 *
	 * @return	String
	 */
	public function getCode3() {
		return $this->get('iso_alpha3');
	}



	/**
	 * Get ISO number of country
	 *
	 * @return	String
	 */
	public function getIsoNum() {
		return $this->get('iso_num');
	}



	/**
	 * Get currency of country
	 *
	 * @return		TodoyuCurrencyCurrency
	 */
	public function getCurrency() {
		$field	= 'id';
		$table	= 'static_currency';
		$where	= 'iso_num = ' . $this->getIsoNum();

		$idCurrency	= Todoyu::db()->getFieldValue($field, $table, $where);

		return TodoyuCurrencyCurrencyManager::getCurrency($idCurrency);
	}



	/**
	 * Get country label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return $this->getCode3() ? TodoyuLabelManager::getLabel('static_country.' . $this->getCode3()) : '';
	}



	/**
	 * Load foreign data
	 *
	 */
	protected function loadForeignData() {
		$this->data['currency'] = $this->getCurrency()->getTemplateData();
	}



	/**
	 * Get country template data
	 * Foreign data: currency
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		$this->data['label'] = $this->getLabel();

		return parent::getTemplateData();
	}

}

?>