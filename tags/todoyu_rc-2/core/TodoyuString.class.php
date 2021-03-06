<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 * String helper functions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuString {

	/**
	 * Check if a string is utf-8 encoded
	 *
	 * @param	String		$stringToCheck
	 * @return	Boolean
	 */
	public static function isUTF8($stringToCheck) {
		if( function_exists('mb_detect_encoding') ) {
			return mb_detect_encoding($stringToCheck, 'UTF-8, ISO-8859-15, ISO-8859-1') === 'UTF-8';
		} else {
			return true; // Assume it's already utf8 as it should be. We cannot tell it anyway without this function
		}
	}



	/**
	 * Convert a string to UTF-8 if necessary
	 *
	 * @param	String		$stringToConvert
	 * @return	String
	 */
	public static function convertToUTF8($stringToConvert) {
		return self::isUTF8($stringToConvert) ? $stringToConvert : utf8_encode($stringToConvert);
	}



	/**
	 * Checking syntax of input email address
	 *
	 * @todo	improve, validation isnt failsafe!
	 *
	 * @param	String		Input string to evaluate
	 * @return	Boolean		Returns true if the $email address (input string) is valid; Has a "@", domain name with at least one period and only allowed a-z characters.
	 */
	public static function isValidEmail($email)	{
		$email = trim ($email);
		if( strstr($email,' ') ) {
			return false;
		}

//		@note	ereg is deprecated!
//		$regexp	= '^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$';
//		return ereg($regexp, $email) ? true : false;

		return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) ? true : false;
	}



	/**
	 * Crop a text to a specific length. If text is cropped, a postfix will be added (default: ...)
	 * Per default, words will not be split and the text will mostly be a little bit shorter
	 *
	 * @param	String		$text
	 * @param	Integer		$length
	 * @param	String		$postfix
	 * @param	Boolean		$dontSplitWords
	 * @return	String
	 */
	public static function crop($text, $length, $postfix = '...', $dontSplitWords = true) {
		$length	= intval($length);

		if( strlen($text) > $length ) {
			$text	= utf8_decode($text);

			$cropped	= substr($text, 0, $length);
			$nextChar	= substr($text, $length, 1);

			if( $dontSplitWords === true && $nextChar !== ' ' ) {
				$spacePos	= strpos($cropped, ' ');
				$cropped	= substr($cropped, 0, $spacePos);
			}
			$cropped .= $postfix;

			$cropped = utf8_encode($cropped);
		} else {
			$cropped = $text;
		}

		return $cropped;
	}



	/**
	 * Wrap string with given pipe-separated wrapper string, e.g. HTML tags
	 *
	 * @param	String	$string
	 * @param	String	$wrap			<tag>|</tag>
	 * @return	String
	 */
	public static function wrap($string, $wrap)	{
		return str_replace('|', $string, $wrap);
	}



	/**
	 * Split a camel case formated string into its words
	 *
	 * @param	String		$string
	 * @return	Array
	 */
	public static function splitCamelCase($string) {
		return preg_split('/([A-Z][^A-Z]*)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	}



	/**
	 * Convert an HTML snippet into plain text. Keep as much format information as possible
	 *
	 * @param	String		$html		HTML snippet
	 * @return	String		Text version
	 */
	public static function html2text($html) {
		return strip_tags($html);
		/*
		require_once( PATH_LIB . '/php/html2text/class.html2text.php' );

		$html2text = new html2text($html);
		$html2text->set_base_url(TODOYU_URL);

		return $html2text->get_text();
		*/
	}



	/**
	 * Get a substring around a keyword
	 *
	 * @param	String		$string			The whole text
	 * @param	String		$keyword		Keyword to find in the text
	 * @param	Integer		$charsBefore	Characters included before the keyword
	 * @param	Integer		$charsAfter		Characters included after the keyword
	 * @return	String		Substring with keyword surrounded by the original text
	 */
	public static function getSubstring($string, $keyword, $charsBefore = 20, $charsAfter = 20, $htmlEntities = true) {
		$charsBefore= intval($charsBefore);
		$charsAfter	= intval($charsAfter);
		$keyLen		= strlen(trim($keyword));
		$pos		= stripos($string, $keyword);
		$start		= TodoyuNumeric::intInRange($pos-$charsBefore, 0);
		$subLen		= $charsBefore + $keyLen + $charsAfter;

		if( $htmlEntities ) {
			$string = htmlentities(substr(html_entity_decode($string), $start, $subLen));
		} else {
			$string = substr($string, $start, $subLen);
		}

		return $string;
	}



	/**
	 * Add an element to a separated list (ex: coma separated)
	 *
	 * @param	String		$list
	 * @param	String		$value
	 * @param	String		$separator
	 * @param	Boolean		$unique
	 * @return	String
	 */
	public static function addToList($list, $value, $separator = ',', $unique = false) {
		$items	= explode($separator, $list);
		$items[]= $value;

		if( $unique ) {
			$items = array_unique($items);
		}

		return implode($separator, $items);
	}



	/**
	 * Check if an element is in a seperated list string (ex: comma seperated)
	 *
	 * @param	String		$item				Element to check for
	 * @param	String		$listString			List with concatinated elements
	 * @param	String		$listSeperator		List element seperating character
	 * @return	Boolean
	 */
	public static function isInList($item, $listString, $listSeperator = ',')	{
		$list	= explode($listSeperator, $listString);

		return in_array($item, $list);
	}



	/**
	 * Generate a random password. Customizeable
	 *
	 * @param	Integer		$length
	 * @param	Boolean		$useLowerCase
	 * @param	Boolean		$useNumbers
	 * @param	Boolean		$useSpecialChars
	 * @param	Boolean		$useDoubleChars
	 * @return	String
	 */
	public static function generatePassword($length = 8, $useLowerCase = false, $useNumbers = true, $useSpecialChars = false, $useDoubleChars = true) {
		$length		= intval($length);
		$characters	= array_merge(range('a', 'z'), range('A', 'Z'));

		if( $useNumbers ) {
			$characters = array_merge($characters, range('0', '9'));
		}
		if( $useSpecialChars ) {
			$characters = array_merge($characters, array('#','&','@','$','_','%','?','+','-'));
		}
		if( $useDoubleChars ) {
			shuffle($characters);
			$characters = array_merge($characters, $characters);
		}

		// Shuffle array
		shuffle($characters);
		$password = substr(implode('', $characters), 0, $length);

		if( $useLowerCase ) {
			$password = strtolower($password);
		}

		return $password;
	}



	/**
	 * Format a filesize in the gb/mb/kb/b and add label
	 *
	 * @param	Integer		$filesize
	 * @param	Array		$labels			Custom label array (overrides the default labels
	 * @param	Boolean		$noLabel		Don't append label
	 * @return	String
	 */
	public static function formatSize($filesize, array $labels = null, $noLabel = false) {
		$filesize	= intval($filesize);

		if( is_null($labels) ) {
			if( $noLabel === false ) {
				$labels = array(
					'gb'	=> Label('file.size.gb'),
					'mb'	=> Label('file.size.mb'),
					'kb'	=> Label('file.size.kb'),
					'b'		=> Label('file.size.b')
				);
			} else {
				$labels	= array();
			}
		}

		if( $filesize > 1000000000 ) { 		// GB
			$size	= $filesize / (1024 * 1024 * 1024);
			$label	= $labels['gb'];
		} elseif( $filesize > 1000000 ) {
			$size	= $filesize / (1024 * 1024);
			$label	= $labels['mb'];
		} elseif( $filesize > 1000 ) {
			$size	= $filesize / 1024;
			$label	= $labels['kb'];
		} else {
			$size	= $filesize;
			$label	= $labels['b'];
		}

		$dez	= $size >= 10 ? 0 : 1;

		return number_format($size, $dez, '.', '') . ( $noLabel ? '' : ' ' . $label);
	}



	/**
	 * Parse label if there is a label reference (LLL:)
	 * Else, just return the label
	 *
	 * @param	String		$label		Label or label reference
	 * @param	String		$locale		For output for this locale
	 * @return	String		Real label
	 */
	public static function getLabel($label, $locale = null) {
		if( ! is_string($label) ) {
			return '';
		} elseif( strncmp('LLL:', $label, 4) === 0 ) {
			$labelKey = substr($label, 4);
			return TodoyuLanguage::getLabel($labelKey, $locale);
		} else {
			return $label;
		}
	}



	/**
	 * Wrap into JS tag
	 *
	 * @param	String	$jsCode
	 * @return	String
	 */
	public static function wrapScript($jsCode) {
		return '<script language="javascript" type="text/javascript">' . $jsCode . '</script>';
	}



	/**
	 * Build an URL with given parameters prefixed with todoyu path
	 *
	 * @param	Array		$params		Parameters as key=>value
	 * @param	String		$hash		Hash (#hash)
	 * @param	Boolean		$absolute	Absolute URL with host server
	 * @return	String
	 */
	public static function buildUrl(array $params = array(), $hash = '', $absolute = false) {
		$query		= rtrim(PATH_WEB, '/\\') . '/index.php';
		$queryParts	= array();

			// Add question mark if there are query parameters
		if( sizeof($params) > 0 ) {
			$query .= '?';
		}

			// Add all parameters encoded
		foreach($params as $name => $value) {
			$queryParts[] = $name . '=' . urlencode($value);
		}

			// Concatinate
		$query .= implode('&', $queryParts);

			// Add hash
		if( ! empty($hash) ) {
			$query .= '#' . $hash;
		}

			// Add absolute server url
		if( $absolute ) {
			$query = SERVER_URL . $query;
		}

		return $query;
	}



	/**
	 * Get short md5 hash of a string
	 *
	 * @param	String		$string
	 * @return	String		10 characters md5 hash value of the string
	 */
	public static function md5short($string) {
		return substr(md5($string), 0, 10);
	}



	/**
	 * Analyze version string and return array of contained sub versions and attributes
	 *
	 * @param	String		$versionString
	 * @return	Array		[major,minor,revision,status]
	 */
	public static function getVersionInfo($versionString) {
		$info			= array();

		if( strpos($versionString, '-') !== false ) {
			$temp	= explode('-', $versionString);
			$version= explode('.', $temp[0]);
			$status	= $temp[1];
		} else {
			$version= explode('.', $versionString);
			$status	= 'stable';
		}

		$info['major']		= intval($version[0]);
		$info['minor']		= intval($version[1]);
		$info['revision']	= intval($version[2]);
		$info['status']		= $status;

		return $info;
	}

}

?>