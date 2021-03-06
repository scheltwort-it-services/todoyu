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
 * Exception for database errors
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDbException extends Exception {

	/**
	 * Query which caused the error
	 *
	 * @var	String
	 */
	private $query;




	/**
	 * Init TodoyuDbException
	 *
	 * @param	String		$message
	 * @param	Integer		$code
	 * @param	String		$query
	 */
	public function  __construct($message, $code, $query) {
		parent::__construct($message, $code);

		$this->query = $query;
	}



	/**
	 * Get query
	 *
	 * @return	String
	 */
	public function getQuery() {
		return $this->query;
	}


	/**
	 * Get file without site path
	 *
	 * @return	String
	 */
	public function getFileShort() {
		return str_replace(PATH, '', $this->getFile());
	}



	/**
	 * Render database error as html
	 *
	 * @param 	Boolean		$fullDoc		Render full HTML document with (<html><body, etc)
	 * @return	String		HTML view of error
	 */
	public function getErrorAsHtml($fullDoc = false) {
			// Remove full site path
		$trace	= $this->getTrace();
		foreach($trace as $index => $step) {
			$trace[$index]['file'] = TodoyuDiv::removeSitePath($trace[$index]['file']);
		}

		$data	= array('message'	=> $this->getMessage(),
						'code'		=> $this->getCode(),
						'file'		=> $this->getFileShort(),
						'line'		=> $this->getLine(),
						'query'		=> $this->getQuery(),
						'trace'		=> $trace);

		$tmpl	= 'core/view/dberror_html.tmpl';

		$content= render($tmpl, $data);

		if( $fullDoc ) {
			$data	= array('content'	=> $content,
							'title'		=> 'Database Error!');
			$tmpl	= 'core/view/htmldoc.tmpl';
			$content= render($tmpl, $data);
		}

		return $content;
	}



	/**
	 * Render database error as JSON
	 *
	 * @todo	Implement a useful format
	 * @return	String
	 */
	public function getErrorAsJson() {
		return json_encode(array('error'=>$this->getMessage()));
	}



	/**
	 * Render database error in plain text
	 *
	 */
	public function getErrorAsPlain() {
			// Remove full site path
		$trace	= $this->getTrace();
		foreach($trace as $index => $step) {
			$trace[$index]['file'] = TodoyuDiv::removeSitePath($trace[$index]['file']);
		}

		$data	= array('message'	=> $this->getMessage(),
						'code'		=> $this->getCode(),
						'file'		=> $this->getFileShort(),
						'line'		=> $this->getLine(),
						'query'		=> $this->getQuery(),
						'trace'		=> $trace);

		//$content	= 'D'
		$tmpl	= 'core/view/dberror_plain.tmpl';

		$content= render($tmpl, $data);

		return $content;
	}
}


?>