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
 * Database management class
 *
 * @package		Todoyu
 * @subpackage	Core
 * @exception	TodoyuDbException
 */
class TodoyuDatabase {

	/**
	 * Holds the only instance if available.
	 * Singleton Pattern
	 *
	 * @var	TodoyuDatabase
	 */
	private static $instance = null;

	/**
	 * Database connection link
	 *
	 * @var	Resource
	 */
	private $link = null;

	/**
	 * History if all executed queries
	 *
	 * @var	Array
	 */
	private $queryHistory = array();

	/**
	 * Database configuration
	 *
	 * @var	Array
	 */
	private $config;



	/**
	 * Get the only instance of the database object
	 * Singleton Pattern
	 *
	 * @param	Array		$config			Configuration
	 * @return	TodoyuDatabase
	 */
	public static function getInstance($config) {
		if( is_null(self::$instance) ) {
			self::$instance = new self($config);
		}

		return self::$instance;
	}



	/**
	 * Constructor is only available over getInstance()
	 *
	 * @param	Array		$config
	 */
	private function __construct($config) {
		$this->config = $config;

		if( $this->config['autoconnect'] !== false ) {
			$this->connect();
		}
	}



	/**
	 * Prevent object cloning
	 */
	private function __clone() {

	}



	/**
	 * Connect to the database, using the configuration array
	 */
	private function connect() {
			// Decide how to connect to mysql server
		$mysqlFunc	= $this->config['persistent'] ? 'mysql_pconnect' : 'mysql_connect';

			// Connect to MySQL server
		$this->link	= @call_user_func($mysqlFunc, $this->config['server'], $this->config['username'], $this->config['password']);

			// Check if connection to server has failed
		if( !$this->link ) {
			$this->printConnectionError(mysql_error(), mysql_errno());
			exit();
		}

			// Select database
		$selectedStatus = @mysql_select_db($this->config['database'], $this->link);

			// Check if database selection has failed
		if( !$selectedStatus ) {
			$this->printSelectDbError(mysql_error(), mysql_errno());
			exit();
		}

		$this->initConnection();
	}



	/**
	 * Initialize database connection
	 * Use UTF-8 names and clear sql_mode
	 */
	private function initConnection() {
		$this->query("SET sql_mode=''");
		$this->query('SET NAMES utf8;');
	}



	/**
	 * Check if todoyu is connected to the database
	 *
	 * @return	Boolean
	 */
	public function isConnected() {
		return $this->link ? true : false;
	}



	/**
	 * Add a query to the query history
	 *
	 * @param	String		$query
	 */
	private function addToHistory($query) {
		$this->queryHistory[] = $query;
	}



	/**
	 * Check whether the database is connected
	 *
	 * @return	Boolean
	 */
	public function hasLink() {
		return !is_null($this->link);
	}



	/**
	 * Get query history
	 *
	 * @return	Array
	 */
	public function getQueryHistory() {
		return $this->queryHistory;
	}



	/**
	 * Build and execute a select query
	 *
	 * @param	String		$fields
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$orderBy
	 * @param	String		$limit
	 * @return	Resource
	 */
	public function doSelect($fields, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '') {
		$query = TodoyuSql::buildSELECTquery($fields, $table, $where, $groupBy, $orderBy, $limit);

		return $this->query($query);
	}



	/**
	 * Select a row. Only one row will be selected.
	 * Limit parameter is only usefull to set an offset.
	 *
	 * @param	String		$fields
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$orderBy
	 * @param	String		$limit
	 * @return	Array
	 */
	public function doSelectRow($fields, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '1') {
		$query	= TodoyuSql::buildSELECTquery($fields, $table, $where, $groupBy, $orderBy, $limit);
		$result	= $this->query($query);

		if( $this->hasRows($result) ) {
			$row = $this->fetchAssoc($result);
			$this->freeResult($result);
			return $row;
		} else {
			return false;
		}
	}



	/**
	 * Build and execute an insert query
	 *
	 * @param	String		$table
	 * @param	Array		$fieldNameValues
	 * @param	Array		$noQuoteFields
	 * @return	Integer		Autogenerated ID
	 */
	public function doInsert($table, array $fieldNameValues, array $noQuoteFields = array()) {
		$query = TodoyuSql::buildINSERTquery($table, $fieldNameValues, $noQuoteFields);

		$this->query($query);

		return $this->getLastInsertID();
	}



	/**
	 * Build and execute a delete query
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$limit
	 * @return	Integer		Num affected (deleted) rows
	 */
	public function doDelete($table, $where, $limit = '') {
		$query	= TodoyuSql::buildDELETEquery($table, $where, $limit);

		$this->query($query);

		return $this->getAffectedRows();
	}



	/**
	 * Set record(s) deleted = 1
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @return	Integer		Num affected (updated) rows
	 */
	public function setDeleted($table, $where) {
		$fieldNameValues = array(
			'deleted'	=> 1
		);

		return $this->doUpdate($table, $where, $fieldNameValues);
	}






	/**
	 * Update database
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @param	Array		$fieldNameValues
	 * @param	Array		$noQuoteFields
	 * @return	Integer		Num affected (updated) rows
	 */
	public function doUpdate($table, $where, array $fieldNameValues, array $noQuoteFields = array()) {
		$query	= TodoyuSql::buildUPDATEquery($table, $where, $fieldNameValues, $noQuoteFields);

		$this->query($query);

		return $this->getAffectedRows();
	}



	/**
	 * Update a record
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @param	Array		$fieldNameValues
	 * @param	Array		$noQuoteFields
	 * @return	Boolean		Was record updated?
	 */
	public function doUpdateRecord($table, $idRecord, array $fieldNameValues, array $noQuoteFields = array()) {
		$table	= '`' . $table . '`';
		$idRecord	= (int)	$idRecord;

		$where	= 'id = ' . $idRecord;

		return $this->doUpdate($table, $where, $fieldNameValues, $noQuoteFields) === 1;
	}



	/**
	 * Execute a select on the database, but only return if there is a result for this query
	 *
	 * @param	String			$fields
	 * @param	String			$table
	 * @param	String			$where
	 * @param	String			$groupBy
	 * @param	String|Integer	$limit
	 * @return	Boolean
	 */
	public function hasResult($fields, $table, $where, $groupBy = '', $limit = 1) {
		$cacheID	= 'hasresult:' . sha1(serialize(func_get_args()));

		if( ! TodoyuCache::isIn($cacheID) ) {
			$result	= $this->doSelect($fields, $table, $where, $groupBy, '', $limit);
			$hasRes	= $this->getNumRows($result) > 0;

			TodoyuCache::set($cacheID, $hasRes);
		}

		return TodoyuCache::get($cacheID);
	}



	/**
	 * Switch a boolean value in database (0 or 1) to the opposite value
	 * 0 => 1, 1 => 0
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @param	String		$fieldName		Field to be toggled
	 * @return	Integer
	 */
	public function doBooleanInvert($table, $idRecord, $fieldName) {
		$idRecord	= (int) $idRecord;

		$where			= '`id` = ' . $idRecord;
		$toggleCommand	= TodoyuSql::buildBooleanInvertQueryPart($fieldName, $table);
		$data			= array(
			$fieldName => $toggleCommand
		);
		$noQuoteFields	= array($fieldName);

		return $this->doUpdate($table, $where, $data, $noQuoteFields) === 1;
	}



	/**
	 * Get error message of last executed query
	 *
	 * @return	String
	 */
	public function getError() {
		return mysql_error($this->link);
	}



	/**
	 * Get error number of last executed query
	 *
	 * @return	Integer
	 */
	public function getErrorNo() {
		return mysql_errno($this->link);
	}



	/**
	 * Execute a query on the database
	 *
	 * @throws	TodoyuDbException
	 * @param	String		$query
	 * @return	Resource
	 */
	public function query($query) {
			// Add query to history if enabled
		if( $this->config['queryHistory'] ) {
			$this->addToHistory($query);
		}
			// If not connected, try again (will prompt an error if not possible)
		if( ! $this->isConnected() ) {
			$this->connect();

			if( ! $this->isConnected() ) {
				die("Cannot connect to the database. Unknown error.");
			}
		}

		$resource	= mysql_query($query, $this->link);

		try {
			if( !$resource ) {
				throw new TodoyuDbException($this->getError(), $this->getErrorNo(), $query);
			}
		} catch(TodoyuDbException $e) {
			TodoyuErrorHandler::handleTodoyuDbException($e);
		}

		return $resource;
	}



	/**
	 * Get amount of rows in the result set
	 *
	 * @param	Resource	$resource
	 * @return	Integer
	 */
	public function getNumRows($resource) {
		return mysql_num_rows($resource);
	}



	/**
	 * Check if a result contains result rows. Detect empty resultsets
	 *
	 * @param	Resource	$result
	 * @return	Boolean
	 */
	public function hasRows($result) {
		return $this->getNumRows($result) > 0;
	}



	/**
	 * Get amount of affected rows by the last query
	 *
	 * @return	Integer
	 */
	public function getAffectedRows() {
		return mysql_affected_rows($this->link);
	}



	/**
	 * Get ID which was generated for the last row inserted in the database
	 *
	 * @return	Integer
	 */
	public function getLastInsertID() {
		return mysql_insert_id($this->link);
	}



	/**
	 * Get last executed query from query history
	 *
	 * @return	String
	 */
	public function getLastQuery() {
		$index	= sizeof($this->queryHistory);

			// Inform about disabled history
		if( ! $this->config['queryHistory'] ) {
			TodoyuLogger::logNotice('Tried to get last query, but history is disabled. Change in db config');
		}

		return $this->queryHistory[$index-1];
	}



	/**
	 * Free a resource to save memory if the resource isn't needed anymore
	 *
	 * @param	Resource		$resource
	 * @return	Boolean
	 */
	public function freeResult($resource) {
		return mysql_free_result($resource);
	}



	/**
	 * Get record from table
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Array		Or false if row doesn't exist
	 */
	public function getRecord($table, $idRecord) {
			// Build cache ID
		$cacheKey	= TodoyuRecordManager::makeRecordQueryKey($table, $idRecord);

			// Check if row is already cached
		if( TodoyuCache::isIn($cacheKey) ) {
			return TodoyuCache::get($cacheKey);
		} else {
				// Fetch row from database, if not in cache
			$where		= 'id = ' . abs($idRecord);
			$resource	= $this->doSelect('*', $table, $where, '', '', 1);

				// Is a record was found, fetch it
			if( $this->hasRows($resource) ) {
				$recordData	= $this->fetchAssoc($resource);
					// Remove resource form memory
				$this->freeResult($resource);
					// Add row to cache
				TodoyuCache::set($cacheKey, $recordData);
			} else {
				$recordData	= false;
			}

			return $recordData;
		}
	}



	/**
	 * Get ID of MM record
	 *
	 * @param	String		$table
	 * @param	Integer		$fieldNameEntity1
	 * @param	Integer		$idEntity1
	 * @param	Integer		$fieldNameEntity2
	 * @param	Integer		$idEntity2
	 * @return	Integer
	 */
	public static function getMMid($table, $fieldNameEntity1, $idEntity1, $fieldNameEntity2, $idEntity2) {
		$idEntity1	= (int) $idEntity1;
		$idEntity2	= (int) $idEntity2;

		$field	= 'id';
		$where	= '		' . $fieldNameEntity1 . '	= ' . $idEntity1
				. ' AND	' . $fieldNameEntity2 . '	= ' . $idEntity2;
		$limit	= '1';

		$row	= Todoyu::db()->getColumn($field, $table, $where, '', '', $limit, $field);

		return $row[0];
	}



	/**
	 * Get a record by query. It hasn't to be a "record", its just a single row result
	 *
	 * @param	String		$fields
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$order
	 * @return	Array		Or false
	 */
	public function getRecordByQuery($fields, $table, $where = '', $groupBy = '', $order = '') {
		$rows	= $this->getArray($fields, $table, $where, $groupBy, $order, 1);

		return sizeof($rows) === 1 ? $rows[0] : false;
	}



	/**
	 * Delete a record by ID
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public function deleteRecord($table, $idRecord) {
		$where	= 'id = ' . (int) $idRecord;

		return $this->doDelete($table, $where, 1) === 1;
	}



	/**
	 * Add a new record to the database
	 *
	 * @param	String		$table			Table where the record is stored
	 * @param	Array		$fieldValues	Fieldname and value pairs
	 * @param	Array		$noQuoteFields	Fields which should not be quoted
	 * @return	Integer		New ID of the record
	 */
	public function addRecord($table, array $fieldValues, array $noQuoteFields = array()) {
		return $this->doInsert($table, $fieldValues, $noQuoteFields);
	}



	/**
	 * Check if a record exists in a table
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public function isRecord($table, $idRecord) {
		$idRecord	= (int) $idRecord;

		$where	= 'id = ' . $idRecord;

		return $this->hasResult('id', $table, $where);
	}



	/**
	 * Get a column from database
	 *
	 * @param	String		$field						Single field to select
	 * @param	String		$table						Table
	 * @param	String		$where						Where
	 * @param	String		$groupBy					Group
	 * @param	String		$orderBy					Order
	 * @param	String		$limit						Limit
	 * @param	String		$resultFieldName			Field name which will be in the SQL result. (ex: "id as idTask"). Not needed if identical with $field
	 * @param	String		$indexField					Field to use as index instead of automatically generated numeric indexes
	 * @return	Array
	 */
	public function getColumn($field, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '', $resultFieldName = '', $indexField = '') {
		$fields	= $field;

			// If an index field is used, it have to be selected too in the
//		if( $indexField !== '' ) {
//			$fields .= ',' . $indexField;
//		}

		$rows	= $this->getArray($fields, $table, $where, $groupBy, $orderBy, $limit);
		$key	= $resultFieldName === '' ? $field : $resultFieldName;
		$column	= array();

		foreach($rows as $row) {
			if( $indexField === '' ) {
				$column[] = $row[$key];
			} else {
				$column[$row[$indexField]] = $row[$key];
			}
		}

		return $column;
	}



	/**
	 * Get all selected rows as an array
	 *
	 * @param	String			$fields
	 * @param	String			$table
	 * @param	String			$where
	 * @param	String			$groupBy
	 * @param	String			$orderBy
	 * @param	String			$limit
	 * @param	String|Boolean	$indexField
	 * @return	Array
	 */
	public function getArray($fields, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '', $indexField = false) {
		$cacheID	= sha1(serialize(func_get_args()));

		if( TodoyuCache::isIn($cacheID) ) {
			return TodoyuCache::get($cacheID);
		} else {
			$resource	= $this->doSelect($fields, $table, $where, $groupBy, $orderBy, $limit);
			$array		= $this->resourceToArray($resource, $indexField);

			$this->freeResult($resource);

			TodoyuCache::set($cacheID, $array);

			return $array;
		}
	}



	/**
	 * Get an array with the $indexField value as array-key
	 * Alias for getArray()
	 *
	 * @param	String		$indexField
	 * @param	String		$fields
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$orderBy
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getIndexedArray($indexField, $fields, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '') {
		return $this->getArray($fields, $table, $where, $groupBy, $orderBy, $limit, $indexField);
	}



	/**
	 * The the value of a single field.
	 * The query should limit the result rows to 1 (all others are ignored anyway)
	 *
	 * @param	String		$field					Field the fetch
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$orderBy
	 * @param	String		$limit
	 * @param	String		$resultFieldName		If field isn't the field name in the resultset (possibly with table prefix...), set the name here
	 * @return	String|Boolean
	 */
	public function getFieldValue($field, $table, $where = null, $groupBy = null, $orderBy = null, $limit = null, $resultFieldName = null) {
		$cacheID	= sha1(serialize(func_get_args()));

		if( TodoyuCache::isIn($cacheID) ) {
			return TodoyuCache::get($cacheID);
		} else {
			$resource	= $this->doSelect($field, $table, $where, $groupBy, $orderBy, $limit);
			$key		= is_null($resultFieldName) ? $field : $resultFieldName;
			$value		= false;

			if( $this->getNumRows($resource) > 0 ) {
				$row	= $this->fetchAssoc($resource);
				$value	= $row[$key];
			}

			TodoyuCache::set($cacheID, $value);
		}

		return $value;
	}



	/**
	 * Update a row in a table defined by its ID
	 *
	 * @param	String		$table				Table where the record is stored
	 * @param	Integer		$idRecord			ID of the record
	 * @param	Array		$fieldValues		Field names with values
	 * @param	Array		$noQuoteFields		Fields which should not be quoted (because they are functions or something)
	 * @return	Boolean
	 */
	public function updateRecord($table, $idRecord, array $fieldValues, array $noQuoteFields = array()) {
		$where	= '`id` = ' . (int) $idRecord;

		return $this->doUpdate($table, $where, $fieldValues, $noQuoteFields) === 1;
	}



	/**
	 * Check if a query would have a result
	 *
	 * @param	String		$query
	 * @return	Boolean
	 */
	public function queryHasResult($query) {
		$resource = $this->query($query);

		return $this->getNumRows($resource) > 0;
	}



	/**
	 * Fetch a row (array) out of a MySQL result
	 *
	 * @param	Resource	$result
	 * @return	Array					Numeric indexes
	 */
	public static function fetchRow($result) {
		return mysql_fetch_row($result);
	}



	/**
	 * Fetch a row (array) out of a MySQL result
	 *
	 * @param	Resource	$result
	 * @return	Array					Associative array with field names
	 */
	public static function fetchAssoc($result) {
		return mysql_fetch_assoc($result);
	}



	/**
	 * Fetch a row (object) out of a MySQL result
	 * StdObject with fields as public member variables
	 *
	 * @param	Resource	$result
	 * @param	String		$className
	 * @param	Array		$classParams
	 * @return	Object
	 */
	public static function fetchObject($result, $className = null, array $classParams = null) {
		return mysql_fetch_object($result, $className, $classParams);
	}



	/**
	 * Check if last executed query has caused an error
	 *
	 * @return	Boolean
	 */
	public function hasErrorState() {
		return $this->getErrorNo() !== 0;
	}



	/**
	 * Fetch all rows in a result set into an array
	 * Use getArray() if you need all rows of a result
	 *
	 * @param	Resource			$resource
	 * @param	String|Boolean		$indexField
	 * @return	Array
	 */
	public static function resourceToArray($resource, $indexField = false) {
		$array	= array();

		while( $row = self::fetchAssoc($resource) ) {
			if( $indexField !== false ) {
				$array[$row[$indexField]] = $row;
			} else {
				$array[] = $row;
			}
		}

		return $array;
	}



	/**
	 * Get total found rows of last result
	 * The last query had to include SQL_CALC_FOUND_ROWS
	 *
	 * @return	Integer
	 */
	public function getTotalFoundRows() {
		$query	= 'SELECT FOUND_ROWS() as rows';

		$result	= $this->query($query);
		$row	= $this->fetchAssoc($result);

		return (int) $row['rows'];
	}



	/**
	 * Get tables of the database
	 *
	 * @return	Array
	 */
	public function getTables() {
		$dbName	= $this->config['database'];

		$query	= 'SHOW TABLES FROM ' . TodoyuSql::quoteTablename($dbName);
		$result	= $this->query($query);

		$rows	= $this->resourceToArray($result);

		$tables	= TodoyuArray::getColumn($rows, 'Tables_in_' . $dbName);

		return $tables;
	}



	/**
	 * Check whether a table exists in the database
	 *
	 * @param	String		$table
	 * @return	Boolean
	 */
	public function hasTable($table) {
		return in_array($table, $this->getTables());
	}



	/**
	 * Returns the number of rows in the given table. You can specify an
	 * optional where clause to return a subset of the table.
	 *
	 * @param	String			$table
	 * @param	String		$where
	 * @return	Integer
	 */
	public function getRowCount($table, $where = '') {
		$query = 'SELECT COUNT(*) as total FROM ' . TodoyuSql::quoteTablename($table);

		if( $where ) {
			$query .= ' WHERE ' . $where;
		}

		$result	= $this->query($query);
		$rows	= $this->resourceToArray($result);

		return intval($rows[0]['total']);
	}



	/**
	 * Truncate a table
	 *
	 * @param	String		$table
	 * @return	Boolean
	 */
	public function truncateTable($table) {
		if( $this->hasTable($table) ) {
			$this->query('TRUNCATE TABLE ' . TodoyuSql::quoteTablename($table));

			return $this->getAffectedRows() > 0;
		} else {
			return false;
		}
	}



	/**
	 * Drop a table
	 *
	 * @param	String		$table
	 */
	public function dropTable($table) {
		$this->query('DROP TABLE IF EXISTS ' . TodoyuSql::quoteTablename($table));
	}



	/**
	 * Get fields of a table
	 *
	 * @param	String		$table
	 * @param	Boolean		$onlyFieldNames
	 * @return	Array
	 */
	public function getTableFields($table, $onlyFieldNames = false) {
		$query		= 'SHOW COLUMNS FROM ' . $table;
		$resource	= $this->query($query);

		$fields		= $this->resourceToArray($resource);

		if( $onlyFieldNames ) {
			$fieldNames = array();
			foreach($fields as $fieldInfo) {
				$fieldNames[] = $fieldInfo['Field'];
			}
			$fields = $fieldNames;
		}

		return $fields;
	}



	/**
	 * Get all keys (indexes) of a table
	 *
	 * @param	String		$table
	 * @param	Boolean		$onlyKeyNames
	 * @return	Array
	 */
	public function getTableKeys($table, $onlyKeyNames = false) {
		$query		= 'SHOW KEYS FROM ' . $table;
		$resource	= $this->query($query);

		$keys		= $this->resourceToArray($resource);

		if( $onlyKeyNames ) {
			$keyNames = array();
			foreach($keys as $keyInfo) {
				$keyNames[] = $keyInfo['Key_name'];
			}
			$keys = $keyNames;
		}

		return $keys;
	}



	/**
	 * Get database connection config
	 *
	 * @param	String		$key		Config key name
	 * @return	Mixed		Array or String
	 */
	public function getConfig($key = null) {
		return is_null($key) ? $this->config : $this->config[$key];
	}



	/**
	 * Get version of database server
	 *
	 * @return	String
	 */
	public function getVersion() {
		$q = 'SELECT VERSION() as version';

		$result	= $this->query($q);
		$info	= $this->resourceToArray($result);

		return trim($info[0]['version']);
	}



	/**
	 * Print database connection error message
	 *
	 * @param	String		$error
	 * @param	Integer		$errorNo
	 */
	private function printConnectionError($error, $errorNo) {
		ob_end_clean();

		$title	= 'Cannot connect to the server "' . htmlentities($this->config['server'], ENT_QUOTES, 'UTF-8',false) . '"';
		$message= $error . '<br /><br />Check server or change in config/db.php';

		include('core/view/error.html');
	}



	/**
	 * Print database selection error message
	 *
	 * @param	String		$error
	 * @param	Integer		$errorNo
	 */
	private function printSelectDbError($error, $errorNo) {
		ob_end_clean();

		$title	= 'Failed selecting database';
		$message= 'Cannot select database "' . htmlentities($this->config['database'], ENT_QUOTES, 'UTF-8', false) . '" on server ' . htmlentities($this->config['server'], ENT_QUOTES, 'UTF-8') . '<br />Check server or change in config/db.php<br />' . $error;

		include('core/view/error.html');
	}

}

?>