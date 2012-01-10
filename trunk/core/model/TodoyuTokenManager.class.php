<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Token Manager
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuTokenManager {

	/**
	 * Database table
	 */
	const TABLE = 'system_token';



	/**
	 * Token Getter
	 *
	 * @param	Integer			$idToken
	 * @return	TodoyuToken
	 */
	public static function getToken($idToken) {
		$idToken	= (int) $idToken;

		return TodoyuRecordManager::getRecord('TodoyuToken', $idToken);
	}



	/**
	 * Get ID of token of given extension, type and owner person
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @return	Integer
	 */
	public static function getTokenIdByOwner($extID, $idTokenType, $idPersonOwner) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		ext				= ' . $extID
				. ' AND	token_type		= ' . $idTokenType
				. ' AND	id_person_owner	= ' . $idPersonOwner
				. ' AND deleted			= 0';

		return (int) Todoyu::db()->getFieldValue($field, $table, $where);
	}



	/**
	 * Get token of given extension and type by owner's person ID
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @return	TodoyuToken|Boolean
	 */
	public static function getTokenByOwner($extID, $idTokenType, $idPersonOwner = 0) {
		$idToken	= self::getTokenIdByOwner($extID, $idTokenType, $idPersonOwner);

		return $idToken > 0 ? self::getToken($idToken) : false;
	}



	/**
	 * Get token with given hash
	 *
	 * @param	String		$hash
	 * @return	TodoyuToken|Boolean
	 */
	public static function getTokenByHash($hash) {
		$hash	= mysql_real_escape_string($hash);

		$where	= '	hash = \'' . $hash . '\' AND deleted = 0';
		$idToken= Todoyu::db()->getFieldValue('id', self::TABLE, $where);

		return $idToken !== false ? self::getToken($idToken) : false;
	}



	/**
	 * Generate new token hash
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @param	Boolean		$storeInSession
	 * @return	String							Hash
	 */
	public static function generateHash($extID, $idTokenType, $idPersonOwner = 0, $storeInSession = false) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);
		$idPersonCreate	= Todoyu::personid();

			// Generate new hash
		$prefix	= $extID . $idTokenType . $idPersonCreate . $idPersonOwner;
		$salt	= uniqid($prefix, microtime(true));
		$hash	= md5($salt);

			// Ensure the hash not being used yet
		if( ! self::isUnusedHash($hash) ) {
			$hash	= self::generateHash($extID, $idTokenType, $idPersonOwner, $storeInSession);
		}

			// Cache the hash in the session
		if( $storeInSession ) {
			self::storeHashInSession($extID, $idTokenType, $hash);
		}

		return $hash;
	}



	/**
	 * Check given hash to (not) exist already
	 *
	 * @param	String	$hash
	 * @return	Boolean
	 */
	public static function isUnusedHash($hash) {
		$token	= self::getTokenByHash($hash);

		return $token == false || $token->getID() == 0;
	}



	/**
	 * Store hash of given extension and type in session
	 *
	 * @param	Integer		$idTokenType
	 * @param	Integer		$extID
	 * @param	String		$hash
	 * @param	Integer		$idPersonOwner
	 */
	public static function storeHashInSession($extID, $idTokenType, $hash, $idPersonOwner = 0) {
		$idTokenType	= (int) $idTokenType;
		$extID			= (int) $extID;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		$hashPath	= self::getTokenHashSessionPath($extID, $idTokenType, $idPersonOwner);

		TodoyuSession::set($hashPath, $hash);
	}



	/**
	 * Get path to token hash value in session
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @return	String
	 */
	public static function getTokenHashSessionPath($extID, $idTokenType, $idPersonOwner) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		return 'tokenHash/' . $extID . '/' . $idTokenType . '/' . $idPersonOwner;
	}



	/**
	 * Get hash of given extension, type and owner from session
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @return	String
	 */
	public static function getHashFromSession($extID, $idTokenType, $idPersonOwner = 0) {
		$idTokenType	= (int) $idTokenType;
		$extID			= (int) $extID;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		$hashPath	= self::getTokenHashSessionPath($extID, $idTokenType, $idPersonOwner);

		return TodoyuSession::get($hashPath);
	}



	/**
	 * Store token to database, hash value is taken from session
	 *
	 * @param	Integer		$idPersonOwner
	 * @param	Integer		$extID
	 * @param	$idTokenType
	 */
	public static function saveTokenFromSession($extID, $idTokenType, $idPersonOwner = 0) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		$idToken= self::getTokenIdByOwner($extID, $idTokenType, $idPersonOwner);
		$hash	= self::getHashFromSession($extID, $idTokenType, $idPersonOwner);

		$data	= array(
			'id'				=> $idToken,
			'ext'				=> $extID,
			'token_type'		=> $idTokenType,
			'id_person_owner'	=> $idPersonOwner,
			'hash'				=> $hash
		);

		self::saveToken($data);
	}



	/**
	 * Save token to DB (new or update)
	 *
	 * @param	Array	$data
	 * @return	Integer			ID of token record
	 */
	public static function saveToken(array $data) {
		$idToken	= (int) $data['id'];

			// Is a new token?
		if( $idToken === 0 ) {
			$idToken = self::addToken($data);
		} else {
				// Is update of existing token
			self::updateToken($idToken, $data);
			self::removeTokenFromCache($idToken);
		}

		return $idToken;
	}



	/**
	 * Add new token record to DB
	 *
	 * @param	Array		$data
	 * @return	Integer				Token record ID
	 */
	public static function addToken(array $data = array()) {
		$data['date_create']		= NOW;
		$data['id_person_create']	= Todoyu::personid();

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update token
	 *
	 * @param	Integer		$idToken
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateToken($idToken, array $data) {
		$idToken	= (int) $idToken;

		self::removeTokenFromCache($idToken);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idToken, $data);
	}



	/**
	 * Remove token from cache (only necessary if the token has been loaded from database
	 * and updated after in the same request and needs to be loaded again)
	 *
	 * @param	Integer		$idToken
	 */
	public static function removeTokenFromCache($idToken) {
		$idToken	= (int) $idToken;

		TodoyuRecordManager::removeRecordCache('TodoyuToken', $idToken);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idToken);
	}



	/**
	 * Get hash of token that was received with request
	 *
	 * @return	String
	 */
	public static function geTokenHashValueFromRequest() {
		return TodoyuRequest::getParam('token');
	}



	/**
	 * Check whether token has been received with request
	 *
	 * @return	Boolean
	 */
	public static function hasRequestToken() {
		$hash	= self::geTokenHashValueFromRequest();

		return ! empty($hash);
	}



	/**
	 * Get todoyu sharing URL for accessing public token of given specs
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Boolean		$download
	 * @return	String
	 */
	public static function getPublicTokenURL($extID, $idTokenType, $download = false) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;

		$token	= TodoyuTokenManager::getTokenByOwner($extID, $idTokenType);

		if( $token !== false ) {
			$urlParams = array(
				'token'	=> $token->getHash()
			);
			if( $download ) {
				$urlParams['download']	= 1;
			}

			return TodoyuString::buildUrl($urlParams, '', true);
		}

		return false;
	}



	/**
	 * Check whether a token of the given specs is stored already
	 *
	 * @param	Integer		$extID
	 * @param	Integer		$idTokenType
	 * @param	Integer		$idPersonOwner
	 * @return	String|Boolean
	 */
	public static function isTokenStored($extID, $idTokenType, $idPersonOwner = 0) {
		$extID			= (int) $extID;
		$idTokenType	= (int) $idTokenType;
		$idPersonOwner	= Todoyu::personid($idPersonOwner);

		$token	= TodoyuTokenManager::getTokenByOwner($extID, $idTokenType, $idPersonOwner);

		return $token ? true : false;
	}

}

?>