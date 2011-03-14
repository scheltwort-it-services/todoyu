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
 * Test class for TodoyuTime.
 * Generated by PHPUnit on 2010-05-10 at 18:08:25.
 */
class TodoyuTimeTest extends PHPUnit_Framework_TestCase {

	protected $timezone;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->timezone = Todoyu::getTimezone();

		Todoyu::setTimezone('UTC');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		Todoyu::setTimezone($this->timezone);
	}

	public function testGetStartOfDay() {
		$timeAfternoon	= gmmktime(14, 0, 0, 1, 1, 2010);
		$timeDaystart	= gmmktime(0, 0, 0, 1, 1, 2010);

		$result1	= TodoyuTime::getStartOfDay($timeAfternoon);
		$result2	= TodoyuTime::getStartOfDay($timeDaystart);

		$this->assertEquals($timeDaystart, $result1);
		$this->assertEquals($result1, $result2);
	}

	public function testGetEndOfDay() {
		$time		= gmmktime(14, 0, 0, 1, 1, 2010);
		$testDayend	= gmmktime(23, 59, 59, 1, 1, 2010);

		$timeEnd1	= TodoyuTime::getEndOfDay($time);
		$timeEnd2	= TodoyuTime::getEndOfDay($testDayend);

		$this->assertEquals($testDayend, $timeEnd1);
		$this->assertEquals($testDayend, $timeEnd2);
	}

	public function testGetDayRange() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testStart	= gmmktime(0, 0, 0, 8, 3, 2010);
		$testEnd	= gmmktime(23, 59, 59, 8, 3, 2010);
		$range		= TodoyuTime::getDayRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	public function testGetWeekRange() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testStart	= gmmktime(0, 0, 0, 8, 2, 2010);
		$testEnd	= gmmktime(23, 59, 59, 8, 8, 2010);
		$range		= TodoyuTime::getWeekRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	public function testGetMonthRange() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testStart	= gmmktime(0, 0, 0, 8, 1, 2010);
		$testEnd	= gmmktime(23, 59, 59, 8, 31, 2010);
		$range		= TodoyuTime::getMonthRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	public function testGetWeekStart() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testStart	= gmmktime(0, 0, 0, 8, 2, 2010);
		$weekStart	= TodoyuTime::getWeekStart($time);

		$this->assertEquals($testStart, $weekStart);
	}

	public function testGetMonthStart() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testStart	= gmmktime(0, 0, 0, 8, 1, 2010);
		$monthStart	= TodoyuTime::getMonthStart($time);

		$this->assertEquals($testStart, $monthStart);
	}

	public function testGetWeekday() {
		$time		= gmmktime(14, 33, 59, 8, 3, 2010);
		$testWeekday= 1;
		$weekday	= TodoyuTime::getWeekday($time);

		$this->assertEquals($testWeekday, $weekday);
	}

	public function testGetTimeParts() {
		$time		= (14*3600) + (33*60) + (59); // 14:33:59
		$testHours	= 14;
		$testMinutes= 33;
		$testSeconds= 59;

		$timeParts	= TodoyuTime::getTimeParts($time);

		$this->assertEquals($testHours, $timeParts['hours']);
		$this->assertEquals($testMinutes, $timeParts['minutes']);
		$this->assertEquals($testSeconds, $timeParts['seconds']);
	}

	public function testFirstHourLeftOver() {
		$testHours1	= 1.0;
		$testHours2	= 0.0;
		$testHours3	= 0.7;

		$hours1	= TodoyuTime::firstHourLeftOver(2.5);
		$hours2	= TodoyuTime::firstHourLeftOver(-0.5);
		$hours3	= TodoyuTime::firstHourLeftOver(0.7);

		$this->assertEquals($testHours1, $hours1);
		$this->assertEquals($testHours2, $hours2);
		$this->assertEquals($testHours3, $hours3);
	}

	public function testSec2hour() {
		$seconds1	= (14*3600) + (33*60) + (29); // 14:33:29
		$seconds2	= (14*3600) + (33*60) + (31); // 14:33:31
		$testString1= '14:33';
		$testString2= '14:34';

		$timeString1	= TodoyuTime::sec2hour($seconds1);
		$timeString2	= TodoyuTime::sec2hour($seconds2);

		$this->assertEquals($testString1, $timeString1);
		$this->assertEquals($testString2, $timeString2);
	}

//	public function testSec2time() {
//		$seconds	= (14*3600) + (33*60) + (29); // 14:33:29
//		$testString	= '14:33:29';
//
//		$timeString	= TodoyuTime::sec2time($seconds);
//
//		$this->assertEquals($testString, $timeString);
//	}

	public function testFormatTime() {
		$seconds	= 18*3600 + 24*60 + 35;
		$testString1= '18:24:35';
		$testString2= '18:25';
		$testString3= '18:24';

		$timeString1= TodoyuTime::formatTime($seconds, true);
		$timeString2= TodoyuTime::formatTime($seconds, false);
		$timeString3= TodoyuTime::formatTime($seconds, false, false);

		$this->assertEquals($testString1, $timeString1);
		$this->assertEquals($testString2, $timeString2);
		$this->assertEquals($testString3, $timeString3);
	}

	public function testFormat() {
		$currentLocale	= Todoyu::getLocale();

		Todoyu::setLocale('en_GB');

		$time	= gmmktime(14, 36, 5, 3, 9, 1984);

		$formattedEN= TodoyuTime::format($time, 'datetime');
		$expectedEN	= '03/09/84 14:36';

		$this->assertEquals($expectedEN, $formattedEN);


		Todoyu::setLocale('de_DE');

		$formattedDE= TodoyuTime::format($time, 'datetime');
		$expectedDE	= '09.03.1984 14:36';

		$this->assertEquals($expectedDE, $formattedDE);

		Todoyu::setLocale($currentLocale);
	}



	public function testGetFormat() {
		$currentLocale	= Todoyu::getLocale();

			// Test with german locale
		Todoyu::setLocale('de_DE');

		$format		= TodoyuTime::getFormat('DshortD2MlongY4');
		$expected	= '%a, %d. %B %Y';

		$this->assertEquals($expected, $format);


			// Test with debugging enabled
		$debug	= Todoyu::$CONFIG['DEBUG'];

		Todoyu::$CONFIG['DEBUG'] = true;
		$format		= TodoyuTime::getFormat('notavailableformatstring');
		$this->assertEquals('core.dateformat.notavailableformatstring', $format);


			// Test with debugging disabled
		Todoyu::$CONFIG['DEBUG'] = false;
		$format		= TodoyuTime::getFormat('notavailableformatstring');
		$this->assertEquals('', $format);


			// Restore settings
		Todoyu::$CONFIG['DEBUG'] = $debug;
		Todoyu::setLocale($currentLocale);
	}



	/**
	 * @todo	comment
	 * @return void
	 */
	public function testParseDateString() {
		$time	= gmmktime(13, 46, 22, 4, 19, 2016);
		$date1	= date('r', $time);
		$date2	= date('Y-m-d H:i:s', $time);

		$time1	= TodoyuTime::parseDateString($date1);
		$time2	= TodoyuTime::parseDateString($date2);

		$this->assertEquals($time, $time1);
		$this->assertEquals($time, $time2);
	}



	/**
	 * @todo	comment
	 * @return void
	 */
	public function testParseDate() {
		$dateCompare	= strtotime('2010-03-22');
		$dateString1	= '2010-03-22';
		$dateTime1		= TodoyuTime::parseDate($dateString1);

		$oldLocale		= TodoyuLabelManager::getLocale();

		TodoyuLabelManager::setLocale('en_GB');
		$dateString2	= '3/22/2010';
		$dateTime2		= TodoyuTime::parseDate($dateString2);

		TodoyuLabelManager::setLocale('de_DE');
		$dateString3	= '22.3.2010';
		$dateTime3		= TodoyuTime::parseDate($dateString3);

		TodoyuLabelManager::setLocale('pt_BR');
		$dateString4	= '22.3.2010';
		$dateTime4		= TodoyuTime::parseDate($dateString4);


		$this->assertEquals($dateCompare, $dateTime1);
		$this->assertEquals($dateCompare, $dateTime2);
		$this->assertEquals($dateCompare, $dateTime3);
		$this->assertEquals($dateCompare, $dateTime4);

		TodoyuLabelManager::setLocale($oldLocale);
	}

	public function testParseDateTime() {
		$dateCompare	= strtotime('2010-03-22 14:36');

		$oldLocale		= TodoyuLabelManager::getLocale();

		Todoyu::setLocale('de_DE');
		$dateStringDE	= '22.03.2010 14:36';
		$timeDE			= TodoyuTime::parseDateTime($dateStringDE);

		$this->assertEquals($dateCompare, $timeDE);

		TodoyuLabelManager::setLocale($oldLocale);
	}

	public function testParseTime() {
		$time_1	= '23:59';
		$sec_1	= 86340;
		$time_2	= '23:59:30';
		$sec_2	= 86370;
		$time_3	= '0:00:01';
		$sec_3	= 1;

		$res_1	= TodoyuTime::parseTime($time_1);
		$res_2	= TodoyuTime::parseTime($time_2);
		$res_3	= TodoyuTime::parseTime($time_3);

		$this->assertEquals($sec_1, $res_1);
		$this->assertEquals($sec_2, $res_2);
		$this->assertEquals($sec_3, $res_3);
	}

	public function testParseDuration() {
		$dur_1	= '3:00';
		$sec_1	= 10800;
		$dur_2	= '0:00';
		$sec_2	= 0;
		$dur_3	= '1:';
		$sec_3	= 3600;
		$dur_4	= '100:00';
		$sec_4	= 360000;
		$dur_5	= ':59';
		$sec_5	= 3540;
		$dur_6	= '0:67';
		$sec_6	= 4020; // 1:07

		$res_1	= TodoyuTime::parseDuration($dur_1);
		$res_2	= TodoyuTime::parseDuration($dur_2);
		$res_3	= TodoyuTime::parseDuration($dur_3);
		$res_4	= TodoyuTime::parseDuration($dur_4);
		$res_5	= TodoyuTime::parseDuration($dur_5);
		$res_6	= TodoyuTime::parseDuration($dur_6);

		$this->assertEquals($sec_1, $res_1);
		$this->assertEquals($sec_2, $res_2);
		$this->assertEquals($sec_3, $res_3);
		$this->assertEquals($sec_4, $res_4);
		$this->assertEquals($sec_5, $res_5);
		$this->assertEquals($sec_6, $res_6);
	}

	public function testGetRoundedTime() {
		$time	= 10*3600 + 33*60 + 31; // 10:33:31
		$test1	= 10*3600 + 35*60 + 0; // 10:35:00
		$test2	= 10*3600 + 30*60 + 0; // 10:30:00
		$test3	= 10*3600 + 40*60 + 0; // 10:40:00
		$test4	= 11*3600 + 0 + 0; // 11:00:00
		$test5	= 10*3600 + 33*60 + 0; // 10:33:00

		$rounded1	= TodoyuTime::getRoundedTime($time);
		$rounded2	= TodoyuTime::getRoundedTime($time, 5);
		$rounded3	= TodoyuTime::getRoundedTime($time, 10);
		$rounded4	= TodoyuTime::getRoundedTime($time, 15);
		$rounded5	= TodoyuTime::getRoundedTime($time, 20);
		$rounded6	= TodoyuTime::getRoundedTime($time, 30);
		$rounded7	= TodoyuTime::getRoundedTime($time, 60);
		$rounded8	= TodoyuTime::getRoundedTime($time, 1);

		$this->assertEquals($test1, $rounded1);
		$this->assertEquals($test1, $rounded2);
		$this->assertEquals($test2, $rounded3);
		$this->assertEquals($test2, $rounded4);
		$this->assertEquals($test3, $rounded5);
		$this->assertEquals($test2, $rounded6);
		$this->assertEquals($test4, $rounded7);
		$this->assertEquals($test5, $rounded8);
	}



	/**
	 * Check week timestamps
	 * Force UTC to test
	 */
	public function testGetTimestampsForWeekdays() {
		$expectedTimestamps = array(
			gmmktime(0,0,0,6,7,2010),	// 07.06.2010 00:00
			gmmktime(0,0,0,6,8,2010),	// 08.06.2010 00:00
			gmmktime(0,0,0,6,9,2010),	// 09.06.2010 00:00
			gmmktime(0,0,0,6,10,2010),	// 10.06.2010 00:00
			gmmktime(0,0,0,6,11,2010),	// 11.06.2010 00:00
			gmmktime(0,0,0,6,12,2010),	// 12.06.2010 00:00
			gmmktime(0,0,0,6,13,2010)	// 13.06.2010 00:00
		);

		$timestamps	= TodoyuTime::getTimestampsForWeekdays($expectedTimestamps[2]);

		$this->assertEquals($expectedTimestamps, $timestamps);
	}

	public function testGetDaysInMonth() {
		$timeJune2010	= gmmktime(0, 0, 0, 6, 1, 2010);
		$timeFeb2010	= gmmktime(0, 0, 0, 2, 1, 2010);

		$resDaysJune2010	= TodoyuTime::getDaysInMonth($timeJune2010);
		$resDaysFeb2010		= TodoyuTime::getDaysInMonth($timeFeb2010);
		$resDaysJan2010		= TodoyuTime::getDaysInMonth($timeFeb2010, -1);
		$resDaysMarch2010	= TodoyuTime::getDaysInMonth($timeFeb2010, 1);
		$resDaysApril2010	= TodoyuTime::getDaysInMonth($timeFeb2010, 2);
		$resDaysFeb2012		= TodoyuTime::getDaysInMonth($timeFeb2010, 24);

		$this->assertEquals($resDaysJune2010, 30);
		$this->assertEquals($resDaysFeb2010, 28);
		$this->assertEquals($resDaysJan2010, 31);
		$this->assertEquals($resDaysMarch2010, 31);
		$this->assertEquals($resDaysApril2010, 30);
		$this->assertEquals($resDaysFeb2012, 29);
	}

	public function testRangeOverlaps() {
		$date1	= strtotime('2010-01-01 08:00:00');
		$date2	= strtotime('2010-01-01 10:00:00');
		$date3	= strtotime('2010-03-05 08:00:00');
		$date4	= strtotime('2010-05-25 02:00:00');
		$date5	= strtotime('2010-08-13 14:00:00');
		$date6	= strtotime('2011-01-01 12:00:00');

		$overlaps1	= TodoyuTime::rangeOverlaps($date1, $date2, $date3, $date4);
		$overlaps2	= TodoyuTime::rangeOverlaps($date1, $date3, $date2, $date4);
		$overlaps3	= TodoyuTime::rangeOverlaps($date1, $date6, $date3, $date4);
		$overlaps4	= TodoyuTime::rangeOverlaps($date1, $date6, $date5, $date6);
		$overlaps5	= TodoyuTime::rangeOverlaps($date1, $date3, $date3, $date6);

		$this->assertFalse($overlaps1);
		$this->assertTrue($overlaps2);
		$this->assertTrue($overlaps3);
		$this->assertTrue($overlaps4);
		$this->assertFalse($overlaps5);
	}

}

?>