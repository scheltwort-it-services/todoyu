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
 * SQL manager. Functions to handle SQL queries and files
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSQLManager {

	/**
	 * Extract queries from a file. Get a list of the single queries
	 *
	 * @param	String		$file
	 * @return	Array		List of queries in file
	 */
	public static function getQueriesFromFile($file) {
		$content= TodoyuFileManager::getFileContent($file);
		$content= TodoyuSQLManager::cleanSQL($content);
		$queries= explode(';', $content);

		foreach($queries as $index => $query) {
			if( trim($query) === '' ) {
				unset($queries[$index]);
			} else {
				$queries[$index] = trim($query);
			}
		}

		return $queries;
	}



	/**
	 * Get table queries of code tables.sql
	 *
	 * @return	Array
	 */
	public static function getCoreTableQueries() {
		$file	= PATH . '/core/config/db/tables.sql';

		return self::getQueriesFromFile($file);
	}



	/**
	 * Get core table structure from 'tables.sql'
	 *
	 * @return	Array
	 */
	public static function getCoreTablesFromFile() {
			// Get 'table.sql' definitions from extensions having them
		$tableQueries	= self::getCoreTableQueries();
		$tableStructures= array();

		foreach($tableQueries as $tableQuery) {
			$tableStructure = TodoyuSQLParser::parseCreateQuery($tableQuery);
			$tableStructures[$tableStructure['table']]	= $tableStructure;
		}

		return $tableStructures;
	}



	/**
	 * Get extensions table structure
	 *
	 * @return	Array
	 */
	public static function getExtTablesFromFile() {
			// Get all extension queries
		$tableQueries	= self::getExtTableQueries();
		$tableStructures= array();

			// Merge queries of all extensions
		foreach($tableQueries as $tableQuery) {
			$tableStructure = TodoyuSQLParser::parseCreateQuery($tableQuery);

			if( array_key_exists($tableStructure['table'], $tableStructures) ) {
				$tableStructures[$tableStructure['table']]	= self::mergeTableStructure($tableStructures[$tableStructure['table']], $tableStructure);
			} else {
				$tableStructures[$tableStructure['table']]	= $tableStructure;
			}
		}

		return $tableStructures;
	}



	/**
	 * Merge two table structures.
	 * Only missing columns are added. An existing structure is no updated
	 * The Primary Key is only added once. Keys with the same name are ignored
	 *
	 * @param	Array		$structureOne
	 * @param	Array		$structureTwo
	 * @return	Array
	 */
	private static function mergeTableStructure(array $structureOne, array $structureTwo) {
		$colDiff	= array_diff_assoc($structureTwo['columns'], $structureOne['columns']);

			// Add missing columns
		foreach($colDiff as $colName => $colStructure) {
			$structureOne['columns'][$colName] = $colStructure;
		}

			// Get informations about existing keys
		$keyNames	= TodoyuArray::getColumn($structureOne['keys'], 'name');
		$keyTypes	= TodoyuArray::getColumn($structureOne['keys'], 'type');
		$hasPrimary	= in_array('PRIMARY', $keyTypes);

			// Add missing keys
		foreach($structureTwo['keys'] as $keyStructure) {
				// Don't add a primary key if already one exists
			if( $keyStructure['type'] === 'PRIMARY' && $hasPrimary ) {
				continue;
			}

				// Don't add key if already one with this name exists
			if( in_array($keyStructure['name'], $keyNames) ) {
				continue;
			}

				// Add key
			$structureOne['keys'][] = $keyStructure;
		}

		return $structureOne;
	}



	/**
	 * Merge core and extension table structure.
	 * This results in a full table structure for the system
	 *
	 * @param	Array		$coreTables
	 * @param	Array		$extTables
	 * @return	Arra
	 */
	public static function mergeCoreAndExtTables(array $coreTables, array $extTables) {
		$mergedTables	= $coreTables;

			// Add tables and columns of each extension
		foreach($extTables as $tableName => $tableStructure) {
			if( array_key_exists($tableName, $mergedTables) ) {
				$mergedTables[$tableName]	= self::mergeTableStructure($mergedTables[$tableName], $tableStructure);
			} else {
				$mergedTables[$tableName]	= $tableStructure;
			}
		}

		return $mergedTables;
	}



	/**
	 * Get queries of all extensions from their tables.sql
	 *
	 * @return	Array
	 */
	public static function getExtTableQueries() {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		$extQueries	= array();

		foreach($extKeys as $extKey) {
			$extPath	= TodoyuExtensions::getExtPath($extKey);
			$file		= $extPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'tables.sql';

			if( is_file($file) ) {
				$fileQueries= self::getQueriesFromFile($file);
				$extQueries	= array_merge($extQueries, $fileQueries);
			}
		}

		return $extQueries;
	}



	/**
	 * Get queries which are needed to update the database on the base of the structure differences
	 *
	 * @param	Array		$structureDifferences
	 * @return	Array
	 */
	private static function getStructureUpdateQueriesFromDifferences(array $structureDifferences) {
		$queries	= array(
			'create'=> array(),
			'add'	=> array(),
			'change'=> array()
		);

			// Build queries for missing tables
		foreach($structureDifferences['missingTables'] as $tableStructure) {
			$queries['create'][] = self::buildCreateTableQueriesFromStructure($tableStructure);
		}

			// Build queries for missing columns
		foreach($structureDifferences['missingColumns'] as $table => $columnStructures) {
			foreach($columnStructures as $columnName => $columnStructure) {
				$queries['add'][] = self::buildAddColumnQueriesFromStructure($table, $columnStructure);
			}
		}

			// Build queries for changed columns
		foreach($structureDifferences['changedColumns'] as $table => $columnStructure) {
			$queries['change'][] = self::buildChangeColumnQueriesFromStructure($table, $columnStructure);
		}

		foreach($structureDifferences['missingKeys'] as $table => $keyStructures) {
			foreach($keyStructures as $keyStructure) {
				$queries['keys'][] = self::buildAddKeyQueriesFromStructure($table, $keyStructure);
			}
		}

		return $queries;
	}



	/**
	 * Get queries which are necessary to update the database to the file structure
	 *
	 * @return	Array
	 */
	public static function getStructureUpdateQueries() {
		$structureDiff	= self::getStructureDifferences();

			// Generate modification queries based of the differences
		$updateQueries	= self::getStructureUpdateQueriesFromDifferences($structureDiff);

		return $updateQueries;
	}



	/**
	 * Get structure differences between file configuration and active database
	 *
	 * @return	Array
	 */
	public static function getStructureDifferences() {
			// Get table structure from files
		$fileTableStructure	= self::getFileTableStructures();
			// Get table structure from database
		$dbTableStructure	= TodoyuDbAnalyzer::getTableStructures();
			// Find differences between
		$structureDiff		= self::getDifferencesFromStructures($fileTableStructure, $dbTableStructure);

		return $structureDiff;
	}



	/**
	 * Build a query to add a new table with column definitions
	 *
	 * @param	Array		$tableStructure
	 * @return	String
	 */
	private static function buildCreateTableQueriesFromStructure(array $tableStructure) {
		$columnsSQL	= array();
		$keysSQL	= array();

			// Compile column definitions
		foreach($tableStructure['columns'] as $columnName => $columnStructure) {
			$columnsSQL[] = self::buildColumnSQL($columnStructure); // trim('`' . $columnStructure['field'] . '` ' . $columnStructure['type'] . ' ' . $columnStructure['null'] . ' ' . $columnStructure['default'] . ' ' . $columnStructure['extra']);
		}

			// Compile key definitions
		foreach($tableStructure['keys'] as $keyStructure) {
			$keysSQL[] = self::buildKeySQL($keyStructure);
		}

		$query	= '	CREATE TABLE `' . $tableStructure['table'] . '` (' . "\n";
		$query	.= implode(",\n", $columnsSQL);

			// If columns and keys exists, add seperating comma and keys
		if( sizeof($columnsSQL) > 0 && sizeof($keysSQL) > 0 ) {
			$query .= ",\n";
			$query .= implode(",\n", $keysSQL);
		}

			// Close column section and add table extra data
		$query	.= "\n) " . $tableStructure['extra'];

		return $query;
	}



	/**
	 * Build a query to add a column to a table
	 *
	 * @param	String		$table
	 * @param	Array		$columnStructures
	 * @return	String
	 */
	private static function buildAddColumnQueriesFromStructure($table, array $columnStructure) {
		$columnSQL		= self::buildColumnSQL($columnStructure);

		$query	= 'ALTER TABLE `' . $table . '` ADD ' . $columnSQL;

		return $query;
	}



	/**
	 * Build a query to change a column definition
	 *
	 * @param	String		$table
	 * @param	Array		$columnStructure
	 * @return	String
	 */
	private static function buildChangeColumnQueriesFromStructure($table, array $columnStructure) {
		$columnStructure= array_shift($columnStructure);
		$columnSQL		= self::buildColumnSQL($columnStructure);

		$query	= 'ALTER TABLE `' . $table . '` CHANGE `' . $columnStructure['field'] . '` ' . $columnSQL;

		return $query;
	}



	/**
	 * Build a query to add a new key to the table
	 *
	 * @param	String		$table
	 * @param	Array		$keyStructure
	 * @return	String
	 */
	private static function buildAddKeyQueriesFromStructure($table, array $keyStructure) {
		$type	= $keyStructure['type'] === 'PRIMARY' ? 'PRIMARY KEY' : $keyStructure['type'];
		$name	= $keyStructure['type'] === 'PRIMARY' ? '' : '`' . $keyStructure['name'] . '`';
		$fields	= '`' . implode('`,`', $keyStructure['fields']) . '`';

		return 'ALTER TABLE `' . $table . '` ADD ' . $type . ' ' . $name . ' (' . $fields . ')';

	}



	/**
	 * Build a column definition from stucture
	 *
	 * @param	Array		$columnStructure
	 * @return	String
	 */
	private static function buildColumnSQL(array $columnStructure) {
		return trim('`' . $columnStructure['field'] . '` ' . $columnStructure['type'] . ' ' . $columnStructure['null'] . ' ' . $columnStructure['default'] . ' ' . $columnStructure['extra']);
	}



	/**
	 * Build a key definiton from structure
	 *
	 * @param	Array		$keyStructure
	 * @return	String
	 */
	private static function buildKeySQL(array $keyStructure) {
		$sql	= '';

		return ($keyStructure['type'] === 'INDEX' ? '' : $keyStructure['type']) . ' KEY ' . ($keyStructure['name'] !== 'PRIMARY' ? ' `' . $keyStructure['name'] . '`' : '') . ' (`' . implode('`,`', $keyStructure['fields']) . '`)';
	}



	/**
	 * Get table structure from all files
	 *
	 * @return	Array
	 */
	public static function getFileTableStructures() {
			// Get table structure from core tables.sql
		$fileCoreStructure	= self::getCoreTablesFromFile();
			// Get table structure from all ext tables.sql
		$fileExtStructure	= self::getExtTablesFromFile();
			// Merge the core and all ext table structures
		$fileTableStructure	= self::mergeCoreAndExtTables($fileCoreStructure, $fileExtStructure);

		return $fileTableStructure;
	}



	/**
	 * Update database to table structure defined in core and extension files
	 *
	 */
	public static function updateDatabaseFromTableFiles() {
			// Get update queries
		$updateQueries	= self::getStructureUpdateQueries();

		foreach($updateQueries['create'] as $createQuery) {
			Todoyu::db()->query($createQuery);
		}
		foreach($updateQueries['add'] as $addQuery) {
			Todoyu::db()->query($addQuery);
		}
		foreach($updateQueries['change'] as $changeQuery) {
			Todoyu::db()->query($changeQuery);
		}
		foreach($updateQueries['keys'] as $keyQuery) {
			Todoyu::db()->query($keyQuery);
		}
	}



	/**
	 * Get differences between file and database structure
	 *
	 * @param	Array		$fileStructure
	 * @param	Array		$dbStructure
	 * @return	Array
	 */
	public static function getDifferencesFromStructures(array $fileStructure, array $dbStructure) {
		$missingTables	= array();
		$missingColumns	= array();
		$changedColumns	= array();
		$missingKeys	= array();

		foreach($fileStructure as $fileTableName => $fileTableConfig) {
			if( array_key_exists($fileTableName, $dbStructure) ) {
					// Compare columns
				foreach($fileTableConfig['columns'] as $fileColumnName => $fileColumnConfig) {
						// Column already exists in database
					if( array_key_exists($fileColumnName, $dbStructure[$fileTableName]['columns']) ) {
							// Check for differences in the column structure
						$diff	= array_diff($fileColumnConfig, $dbStructure[$fileTableName]['columns'][$fileColumnName]);
							// Found a difference?
						if( sizeof($diff) > 0 ) {
								// Column if different in the database
							$changedColumns[$fileTableName][$fileColumnName] = $fileColumnConfig;
						}
					} else {
							// Add missing column for a table
						$missingColumns[$fileTableName][$fileColumnName] = $fileColumnConfig;
					}
				}

					// Compare keys
				$fileKeyNames		= TodoyuArray::getColumn($fileTableConfig['keys'], 'name');
				$dbKeyNames			= TodoyuArray::getColumn($dbStructure[$fileTableName]['keys'], 'name');
				$missingTableKeys	= array_diff($fileKeyNames, $dbKeyNames);

				foreach($fileTableConfig['keys'] as $fileKey) {
					if( in_array($fileKey['name'], $missingTableKeys) ) {
						$missingKeys[$fileTableName][] = $fileKey;
					}
				}
			} else {
					// Table does not exist in the database
				$missingTables[] = $fileTableConfig;
			}
		}

		return array(
			'missingTables'	=> $missingTables,
			'missingColumns'=> $missingColumns,
			'changedColumns'=> $changedColumns,
			'missingKeys'	=> $missingKeys
		);
	}



	/**
	 * Execute the queries in the version update file
	 *
	 * @param	String		$updateFile			Path to update file
	 */
	public static function executeQueriesFromFile($updateFile) {
		$queries	= self::getQueriesFromFile($updateFile);

		foreach($queries as $query) {
			Todoyu::db()->query($query);
		}

		return sizeof($queries);
	}




	/**
	 * Cleans given SQL from whitespace, comments, etc.
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	public static function cleanSQL($SQLString) {
		$SQLString	= self::removeSQLComments($SQLString);

		return $SQLString;
	}



	/**
	 * Remove comments from within SQL
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	private static function removeSQLComments($SQLString) {
		$cleanSQL	= array();
		$lines		= explode("\n", $SQLString);

		foreach($lines as $line) {
			$line	= trim($line);
				// Line is not a comment?
			if ( substr($line, 0, 2) !== '--' && substr($line, 0, 1) !== '#' ) {
				$cleanSQL[]	= $line;
			}
		}

		return implode("\n", $cleanSQL);
	}



	/**
	 * Check DB for existence of given tables, return missing ones
	 *
	 *	@param	Array	$extTableNames
	 *	@return	Array
	 */
	public static function getMissingTables(array $tablesNames) {
		$dbTables	= Todoyu::db()->getTables();
		$missingTables	= array_diff($tablesNames, $dbTables);

		return array_flip($missingTables);
	}

}


?>