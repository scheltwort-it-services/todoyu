<?php

/**
 * Test class for TodoyuTime.
 * Generated by PHPUnit on 2010-05-10 at 18:08:25.
 */
class TodoyuTimeTest extends PHPUnit_Framework_TestCase {

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {

	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @todo Implement testGetStartOfDay().
	 */
	public function testGetStartOfDay() {
		$timeAfternoon	= mktime(14, 0, 0, 1, 1, 2010);
		$timeDaystart	= mktime(0, 0, 0, 1, 1, 2010);

		$result1	= TodoyuTime::getStartOfDay($timeAfternoon);
		$result2	= TodoyuTime::getStartOfDay($timeDaystart);

		$this->assertEquals($timeDaystart, $result1);
		$this->assertEquals($result1, $result2);
	}

	/**
	 * @todo Implement testGetEndOfDay().
	 */
	public function testGetEndOfDay() {
		$time		= mktime(14, 0, 0, 1, 1, 2010);
		$testDayend	= mktime(23, 59, 59, 1, 1, 2010);

		$timeEnd1	= TodoyuTime::getEndOfDay($time);
		$timeEnd2	= TodoyuTime::getEndOfDay($testDayend);

		$this->assertEquals($testDayend, $timeEnd1);
		$this->assertEquals($testDayend, $timeEnd2);
	}

	/**
	 * @todo Implement testGetDayRange().
	 */
	public function testGetDayRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 3, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 3, 2010);
		$range		= TodoyuTime::getDayRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	/**
	 * @todo Implement testGetWeekRange().
	 */
	public function testGetWeekRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 2, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 8, 2010);
		$range		= TodoyuTime::getWeekRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	/**
	 * @todo Implement testGetMonthRange().
	 */
	public function testGetMonthRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 1, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 31, 2010);
		$range		= TodoyuTime::getMonthRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}

	/**
	 * @todo Implement testGetWeekStart().
	 */
	public function testGetWeekStart() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 2, 2010);
		$weekStart	= TodoyuTime::getWeekStart($time);

		$this->assertEquals($testStart, $weekStart);		
	}

	/**
	 * @todo Implement testGetMonthStart().
	 */
	public function testGetMonthStart() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 1, 2010);
		$monthStart	= TodoyuTime::getMonthStart($time);

		$this->assertEquals($testStart, $monthStart);
	}

	/**
	 * @todo Implement testGetWeekday().
	 */
	public function testGetWeekday() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testWeekday= 1;
		$weekday	= TodoyuTime::getWeekday($time);

		$this->assertEquals($testWeekday, $weekday);
	}

	/**
	 * @todo Implement testGetTimeParts().
	 */
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

	/**
	 * @todo Implement testFirstHourLeftOver().
	 */
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

	/**
	 * @todo Implement testSec2hour().
	 */
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

	/**
	 * @todo Implement testSec2time().
	 */
	public function testSec2time() {
		$seconds	= (14*3600) + (33*60) + (29); // 14:33:29
		$testString	= '14:33:29';

		$timeString	= TodoyuTime::sec2time($seconds);

		$this->assertEquals($testString, $timeString);
	}

	/**
	 * @todo Implement testFormatTime().
	 */
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

	/**
	 * @todo Implement testFormat().
	 */
	public function testFormat() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetFormat().
	 */
	public function testGetFormat() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testParseDateString().
	 */
	public function testParseDateString() {
		$time	= mktime(13, 46, 22, 4, 19, 2016);
		$date1	= date('r', $time);
		$date2	= date('Y-m-d H:i:s', $time);

		$time1	= TodoyuTime::parseDateString($date1);
		$time2	= TodoyuTime::parseDateString($date2);

		$this->assertEquals($time, $time1);
		$this->assertEquals($time, $time2);
	}

	/**
	 * @todo Implement testParseDate().
	 */
	public function testParseDate() {
		$dateCompare	= strtotime('2010-03-22');
		$dateString1	= '2010-03-22';
		$dateTime1		= TodoyuTime::parseDate($dateString1);

		$oldLocale		= TodoyuLanguage::getLocale();

		TodoyuLanguage::setLocale('en_GB');
		$dateString2	= '3/22/2010';
		$dateTime2		= TodoyuTime::parseDate($dateString2);

		TodoyuLanguage::setLocale('de_DE');
		$dateString3	= '22.3.2010';
		$dateTime3		= TodoyuTime::parseDate($dateString3);

		TodoyuLanguage::setLocale('pt_BR');
		$dateString4	= '22.3.2010';
		$dateTime4		= TodoyuTime::parseDate($dateString4);


		$this->assertEquals($dateCompare, $dateTime1);
		$this->assertEquals($dateCompare, $dateTime2);
		$this->assertEquals($dateCompare, $dateTime3);
		$this->assertEquals($dateCompare, $dateTime4);

		TodoyuLanguage::setLocale($oldLocale);
	}

	/**
	 * @todo Implement testParseDateTime().
	 */
	public function testParseDateTime() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testParseTime().
	 */
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

	/**
	 * @todo Implement testParseDuration().
	 */
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

	/**
	 * @todo Implement testGetRoundedTime().
	 */
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
	 * @todo Implement testGetDayTimesOfWeek().
	 */
	public function testGetDayTimesOfWeek() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testGetAmountOfDaysInMonth().
	 */
	public function testGetAmountOfDaysInMonth() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}


	/**
	 * @todo Implement testRangeOverlaps().
	 */
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

	/**
	 * @todo Implement testGetCycleBorderDates().
	 */
	public function testGetCycleBorderDates() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}

?>
