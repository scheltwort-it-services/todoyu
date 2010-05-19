<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 * Test for: TodoyuArray
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuArrayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->array = array(
			array(
				'id'		=> 32,
				'firstname'	=> 'Max',
				'lastname'	=> 'Miller',
				'street'	=> 'Franklin Street'
			),
			array(
				'id'		=> 45,
				'firstname'	=> 'John',
				'lastname'	=> 'Doe',
				'street'	=> 'Roosewelt Road'
			),
			array(
				'id'		=> 12,
				'firstname'	=> 'Michael',
				'lastname'	=> 'Schumacher',
				'street'	=> 'Fist In Your Face Blvd 123'
			),
			array(
				'id'		=> 284,
				'firstname'	=> 'Juck',
				'lastname'	=> 'Norris',
				'street'	=> 'Fist In Your Face Blvd 23'
			),
			array(
				'id'		=> 15,
				'firstname'	=> 'Luke',
				'lastname'	=> 'Skywalker',
				'street'	=> 'Star Freeway \" Sub Galaxy'
			)
		);

	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * Test TodoyuArray::getColumn
	 */
	public function testGetColumn() {
		$idColumn	= TodoyuArray::getColumn($this->array, 'id');

		$this->assertEquals(32, $idColumn[0]);
	}



	/**
	 * Test TodoyuArray::getFirstKey
	 */
	public function testGetFirstKey() {
		$firstKey	= TodoyuArray::getFirstKey($this->array[0]);

		$this->assertEquals('id', $firstKey);
	}



	/**
	 * Test TodoyuArray::getKeyOffset
	 */
	public function testGetKeyOffset() {
		$offset	= TodoyuArray::getKeyOffset($this->array[0], 'street');

		$this->assertEquals(3, $offset);
	}



	/**
	 * Test TodoyuArray::getLastKey
	 */
	public function testGetLastKey() {
		$last	= TodoyuArray::getLastKey($this->array[0]);

		$this->assertEquals('street', $last);
	}



	/**
	 * TodoyuArray::intval
	 */
	public function testIntval() {
		$ids	= TodoyuArray::getColumn($this->array, 'id');

		$ids	= TodoyuArray::intval($ids, true, true);
		$sum	= array_sum($ids);

		$this->assertEquals(388, $sum);

		$names	= TodoyuArray::getColumn($this->array, 'name');

		$names	= TodoyuArray::intval($names, true, true);
		$sum	= array_sum($names);

		$this->assertEquals(0, $sum);
	}



	/**
	 * Test TodoyuArray::intImplode
	 */
	public function testIntImplode() {
		$implode= TodoyuArray::intImplode($this->array[0], '.');
		$expect	= '32.0.0.0';

		$this->assertEquals($expect, $implode);

		$ids	= TodoyuArray::getColumn($this->array, 'id');
		$implode= TodoyuArray::intImplode($ids, ',');
		$expect	= '32,45,12,284,15';

		$this->assertEquals($expect, $implode);
	}



	/**
	 * Test TodoyuArray::reform
	 */
	public function testReform() {
		$reform	= array(
			'id'		=> 'value',
			'firstname'	=> 'label'
		);
		$new	= TodoyuArray::reform($this->array, $reform);

		$this->assertType('array', $new);
		$this->assertTrue(array_key_exists('value', $new[0]));
		$this->assertEquals('Max', $new[0]['label']);
	}



	/**
	 * Test TodoyuArray::stripslashes
	 */
	public function testStripslashes() {
		$streets	= TodoyuArray::getColumn($this->array, 'street');
		$streets	= TodoyuArray::stripslashes($streets);

		$this->assertEquals('Star Freeway " Sub Galaxy', $streets[4]);
	}



	/**
	 * Test TodoyuArray::sortByLabel
	 */
	public function testSortByLabel() {
		$sorted	= TodoyuArray::sortByLabel($this->array, 'firstname');
		$this->assertEquals('Luke', $sorted[2]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'id');
		$this->assertEquals('Michael', $sorted[0]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'lastname', true);
		$this->assertEquals('Luke', $sorted[0]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'street', false, true, false);
		$this->assertEquals('Schumacher', $sorted[0]['lastname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'street', false, true, true);
		$this->assertEquals('Norris', $sorted[0]['lastname']);
	}



	/**
	 * Test TodoyuArray::filter
	 */
	public function testFilter() {
		$filter	= array(
			'id'	=> array(12, 88)
		);
		$filtered = TodoyuArray::filter($this->array, $filter);
		$this->assertEquals(1, sizeof($filtered));

		$filter	= array(
			'id'		=> array(45, 15),
			'firstname'	=> array('John', 'Michael')
		);
		$filtered = TodoyuArray::filter($this->array, $filter);
		$this->assertEquals('Doe', $filtered[0]['lastname']);
	}



	/**
	 * Test TodoyuArray::prefix
	 */
	public function testPrefix() {
		$names		= TodoyuArray::getColumn($this->array, 'firstname');
		$prefixed	= TodoyuArray::prefix($names, 'xxx');

		$this->assertEquals('xxxJohn', $prefixed[1]);
	}



	/**
	 * Test TodoyuArray::insertElement
	 */
	public function testInsertElement() {
		$assoc	= array();
		foreach($this->array as $person) {
			$assoc[$person['lastname']] = $person;
		}
		$new	= array(
			'id'		=> 666,
			'firstname'	=> 'George',
			'lastname'	=> 'Bush',
			'street'	=> 'Ex Presents Alley'
		);

		$assoc = TodoyuArray::insertElement($assoc, 'georgy', $new, 'after', 'Doe');

		$pos	= TodoyuArray::getKeyOffset($assoc, 'georgy');

		$this->assertEquals(2, $pos);
	}



	/**
	 * Test TodoyuArray::fromSimpleXml
	 *
	 * @todo Implement testFromSimpleXml().
	 */
	public function testFromSimpleXml() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::removeKey
	 *
	 * @todo Implement testRemoveKeys().
	 */
	public function testRemoveKeys() {
		$array	= array(
			'switzerland'	=> 'berne',
			'england'		=> 'london',
			'france'		=> 'paris',
			'italy'			=> 'rome',
			'spain'			=> 'madrid'
		);

		$remove	= array(
			'france',
			'italy'
		);

		$result	= TodoyuArray::removeKeys($array, $remove);

		$this->assertTrue(array_key_exists('switzerland', $result));
		$this->assertTrue(array_key_exists('england', $result));
		$this->assertTrue(array_key_exists('spain', $result));
		$this->assertFalse(array_key_exists('france', $result));
		$this->assertFalse(array_key_exists('italy', $result));
	}



	/**
	 * Test TodoyuArray::implodeQuoted
	 *
	 * @todo Implement testImplodeQuoted().
	 */
	public function testImplodeQuoted() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::assure
	 *
	 * @todo Implement testAssure().
	 */
	public function testAssure() {
		$var_1	= array();
		$var_2	= array(1,2,3);
		$var_3	= 3;
		$var_4	= 'string';
		$var_5	= 4.234343;
		$var_6	= null;
		$var_7	= false;

		$this->assertTrue(is_array(TodoyuArray::assure($var_1)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_2)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_3)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_4)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_5)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_6)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_7)));

		$res_2	= TodoyuArray::assure($var_2);
		$res_5	= TodoyuArray::assure($var_5, true);

		$this->assertEquals($var_2, $res_2);
		$this->assertEquals($var_5, $res_5[0]);
	}



	/**
	 * Test TodoyuArray::unserEntryByValue
	 *
	 * @todo Implement testUnsetEntryByValue().
	 */
	public function testUnsetEntryByValue() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::intersectSubArrays
	 *
	 * @todo Implement testIntersectSubArrays().
	 */
	public function testIntersectSubArrays() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::mergeSubArrays
	 *
	 * @todo Implement testMergeSubArrays().
	 */
	public function testMergeSubArrays() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::mergeUnique
	 *
	 * @todo Implement testMergeUnique().
	 */
	public function testMergeUnique() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::flatten
	 *
	 * @todo Implement testFlatten().
	 */
	public function testFlatten() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::removeByValue
	 *
	 * @todo Implement testRemoveByValue().
	 */
	public function testRemoveByValue() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::removeDuplicates
	 *
	 * @todo Implement testRemoveDuplicates().
	 */
	public function testRemoveDuplicates() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::intExplode
	 *
	 * @todo Implement testIntExplode().
	 */
	public function testIntExplode() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::trimExplode
	 *
	 * @todo Implement testTrimExplode().
	 */
	public function testTrimExplode() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::trim
	 *
	 * @todo Implement testTrim().
	 */
	public function testTrim() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuArray::useFieldAsIndex
	 *
	 * @todo Implement testUseFieldAsIndex().
	 */
	public function testUseFieldAsIndex() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

}

?>