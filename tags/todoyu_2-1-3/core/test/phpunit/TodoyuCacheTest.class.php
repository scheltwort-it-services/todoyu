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
 * Test for: TodoyuCache
 *
 * @package		Todoyu
 * @subpackage	Core
 */

/**
 * Test class for TodoyuCache.
 * Generated by PHPUnit on 2010-02-04 at 17:53:08.
 */
class TodoyuCacheTest extends PHPUnit_Framework_TestCase {

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		TodoyuCache::set('star1', 'The Beatles');
		TodoyuCache::set('star2', 'Elvis Presley');
		TodoyuCache::set('star3', 'Michael Jackson');
		TodoyuCache::set('star4', 'ABBA');
		TodoyuCache::set('star5', 'Queen');

		TodoyuCache::set('array', array(1,2,3,4,5,6));
	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * @todo Implement testGet().
	 */
	public function testGet() {
		$star1	= TodoyuCache::get('star1');
		$star4	= TodoyuCache::get('star4');
		$array	= TodoyuCache::get('array');

		$this->assertEquals('The Beatles', $star1);
		$this->assertEquals('ABBA', $star4);
		$this->assertTrue(is_array($array));
		$this->assertEquals(6, sizeof($array));
	}



	/**
	 * @todo Implement testSet().
	 */
	public function testSet() {
		$string	= 'This is a little test';
		$array	= array('a', 'b', 'c');
		$person	= new TodoyuContactPerson(0);

		TodoyuCache::set('string', $string);
		TodoyuCache::set('array2', $array);
		TodoyuCache::set('person', $person);

		$this->assertEquals($string, TodoyuCache::get('string'));
		$this->assertEquals($array, TodoyuCache::get('array2'));
		$this->assertEquals($person, TodoyuCache::get('person'));
	}



	/**
	 * @todo Implement testRemove().
	 */
	public function testRemove() {
		TodoyuCache::remove('star2');

		$this->assertFalse(TodoyuCache::isIn('star2'));
	}



	/**
	 * @todo Implement testIsIn().
	 */
	public function testIsIn() {
		TodoyuCache::set('isin1', 'yes');
		TodoyuCache::set('isin2', 'no');

		$this->assertTrue(TodoyuCache::isIn('isin1'));
		$this->assertTrue(TodoyuCache::isIn('isin2'));
		$this->assertFalse(TodoyuCache::isIn('isin3'));

		TodoyuCache::remove('isin1');

		$this->assertFalse(TodoyuCache::isIn('isin1'));
	}



	/**
	 * @todo Implement testDisable().
	 */
	public function testDisable() {
		TodoyuCache::disable();

		TodoyuCache::set('dummy', 'should not get in the cache');

		$this->assertFalse(TodoyuCache::isIn('dummy'));

		TodoyuCache::enable();
	}



	/**
	 * @todo Implement testEnable().
	 */
	public function testEnable() {
		TodoyuCache::disable();

		TodoyuCache::enable();

		TodoyuCache::set('dummy', 'should get in the cache');

		$this->assertTrue(TodoyuCache::isIn('dummy'));
	}



	/**
	 * @todo Implement testFlush().
	 */
	public function testFlush() {
		TodoyuCache::set('tester', 'Mr. Spock');

		$this->assertTrue(TodoyuCache::isIn('tester'));

		TodoyuCache::flush();

		$this->assertFalse(TodoyuCache::isIn('tester'));
	}
}
?>