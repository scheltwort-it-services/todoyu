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
 * Core Action Controller
 * About
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreAboutActionController extends TodoyuActionController {

	/**
	 * Render about window
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		$data	= array(
			'names'	=> array(
				'Zimmermann'		=> 'Adrian',
				'Ledergerber'		=> 'Andr&eacute;',
				'Oechslin'			=> 'Andr&eacute;',
				'Steiner'			=> 'Andri',
				'Schenker'			=> 'Astrid',
				'Boppart'			=> 'Cornel',
				'Brander'			=> 'Dominic',
				'Erni'				=> 'Fabian',
				'Orlow'				=> 'Joel B.',
				'Stenschke'			=> 'Kay',
				'Rossi'				=> 'Mario',
				'Rohner'			=> 'Markus',
				'Wiederkehr'		=> 'Martin',
				'Karrer'			=> 'Nicolas',
				'Fuchser'			=> 'Pascal',
				'Imboden'			=> 'Thomas',
				'Schl&auml;pfer'	=> 'Thomas'
			),
			'thirdpartycredits'	=> array(
				'cssmin'	=> array(
					'title'		=> 'CssMin',
					'url'		=> 'http://code.google.com/p/cssmin/',
					'copyright'	=> '2008, Joe Scylla',
					'license'	=> 'MIT License',
					'licenseUrl'=> 'http://opensource.org/licenses/mit-license.php'
				),
				'dwoo'	=> array(
					'title'		=> 'Dwoo template engine',
					'url'		=> 'http://dwoo.org/',
					'copyright'	=> '2008, Dwoo / Jordi Boggiano',
					'license'	=> 'Modified BSD License',
					'licenseUrl'=> ''
				),
				'famfamfam'	=> array(
					'title'		=> 'famfamfam Silk Icons',
					'url'		=> 'http://www.famfamfam.com/lab/icons/silk/',
					'copyright'	=> 'Mark James',
					'license'	=> 'Creative Commons Attribution 2.5 License',
					'licenseUrl'=> 'http://creativecommons.org/licenses/by/2.5/'
				),
				'firephp'	=> array(
					'title'		=> 'FirePHP',
					'url'		=> 'http://www.firephp.org/',
					'copyright'	=> '2007-2009, Christoph Dorn',
					'license'	=> 'New BSD License',
					'licenseUrl'=> 'http://www.opensource.org/licenses/bsd-license.php'
				),
				'firebug_lite'	=> array(
					'title'		=> 'Firebug Lite',
					'url'		=> 'http://getfirebug.com/firebuglite',
					'copyright'	=> 'JoeHewitt',
					'license'	=> 'New BSD License',
					'licenseUrl'=> 'http://www.opensource.org/licenses/bsd-license.php'
				),
				'highcharts'	=> array(
					'title'		=> 'Highcharts',
					'url'		=> 'http://www.highcharts.com/',
					'copyright'	=> 'Highslide Software',
					'license'	=> 'Creative Commons Attributiuon-NonCommercial 3.0 License',
					'licenseUrl'=> 'http://creativecommons.org/licenses/by-nc/3.0/'
				),
				'jsCalendar'	=> array(
					'title'		=> 'jsCalendar',
					'url'		=> 'http://dynarch.com/mishoo/',
					'copyright'	=> '2002-2005 Mihai Bazon, dynarch.com',
					'license'	=> 'GNU LGPL',
					'licenseUrl'=> 'http://www.gnu.org/licenses/lgpl.html'
				),
				'jsMin'	=> array(
					'title'		=> 'jSMin',
					'url'		=> 'http://www.crockford.com/javascript/jsmin.html',
					'copyright'	=> '2002 Douglas Crockford, 2008 Ryan Grove',
					'license'	=> 'MIT License',
					'licenseUrl'=> 'http://opensource.org/licenses/mit-license.php'
				),
				'phpMailer'	=> array(
					'title'		=> 'PHPMailer Lite',
					'url'		=> 'http://phpmailer.worxware.com/',
					'copyright'	=> '2001-2003, Brent R. Matzelle, 2004-2009, Andy Prevost.',
					'license'	=> 'GNU LGPL',
					'licenseUrl'=> 'http://www.gnu.org/copyleft/lesser.html'
				),
				'pclzip'	=> array(
					'title'		=> 'PclZip',
					'url'		=> 'http://www.phpconcept.net/pclzip',
					'copyright'	=> 'Vincent Blavet',
					'license'	=> 'GNU/LGPL - August 2009',
					'licenseUrl'=> 'http://www.gnu.org/copyleft/lesser.html'
				),
				'phamlp'	=> array(
					'title'		=> 'phamlp - PHP port of Haml and Sass',
					'url'		=> 'http://code.google.com/p/phamlp/',
					'copyright'	=> 'Copyright (c) 2010, PBM Web Development',
					'license'	=> 'All Rights Reserved',
					'licenseUrl'=> 'http://code.google.com/p/phamlp/downloads/detail?name=license.txt'
				),
				'prototype'	=> array(
					'title'		=> 'prototype JavaScript framework',
					'url'		=> 'http://www.prototypejs.org/',
					'copyright'	=> '2006-2007 Prototype Core Team',
					'license'	=> 'MIT License',
					'licenseUrl'=> 'http://opensource.org/licenses/mit-license.php'
				),
				'prototype.innerSVG' => array(
					'title'		=> 'innerSVG for prototype',
					'url'		=> 'http://code.google.com/p/innersvg',
					'copyright'	=> '2010, Jeff Schiller',
					'license'	=> 'Apache License 2.0',
					'licenseUrl'=> 'http://www.apache.org/licenses/LICENSE-2.0'
				),
				'scal'	=> array(
					'title'		=> 'sCal - Javascript Calendar Based on Prototype',
					'url'		=> 'http://scal.fieldguidetoprogrammers.com',
					'copyright'	=> 'Jamie Grove, Ian Tyndall',
					'license'	=> 'MIT License',
					'licenseUrl'=> 'http://opensource.org/licenses/mit-license.php'
				),
				'scriptacolous'	=> array(
					'title'		=> 'script.aculo.us',
					'url'		=> 'http://script.aculo.us/',
					'copyright'	=> 'Thomas Fuchs',
					'license'	=> 'MIT License',
					'licenseUrl'=> 'http://opensource.org/licenses/mit-license.php'
				),
				'tinyMCE'	=> array(
					'title'		=> 'TinyMCE - JavaScript WYSIWYG Editor',
					'url'		=> 'http://tinymce.moxiecode.com/',
					'copyright'	=> '2003-2011 Moxiecode Systems AB.',
					'license'	=> 'GNU LGPL',
					'licenseUrl'=> 'http://tinymce.moxiecode.com/license.php'
				),
			)
		);
		ksort($data['names']);

		$tmpl	= 'core/view/about-window.tmpl';

		return Todoyu::render($tmpl, $data);
	}

}

?>