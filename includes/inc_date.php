<?php
/**********************************************\
*** Clase Date, utilizada para validar fechas **
***                                           **
*** Diego Gutierrez (diego@minter.com.ar)     **
\**********************************************/

class Date
{
	var $year, $month, $day, $hour, $minute, $second;
	
	// formatted output
	function format($format) {
		$s = $format;
		$s = str_replace('Y', sprintf('%04d', $this->year), $s);
		$s = str_replace('y', sprintf('%02d', $this->year % 100), $s);
		$s = str_replace('m', sprintf('%02d', $this->month), $s);
		$s = str_replace('n', sprintf('%d', $this->month), $s);
		$s = str_replace('d', sprintf('%02d', $this->day), $s);
		$s = str_replace('m', sprintf('%02d', $this->month), $s);
		$s = str_replace('h', sprintf('%02d', $this->hour % 12), $s);
		$s = str_replace('H', sprintf('%02d', $this->hour), $s);
		$s = str_replace('i', sprintf('%02d', $this->minute), $s);
		$s = str_replace('s', sprintf('%02d', $this->second), $s);
		$s = str_replace('a', ($this->hour < 12 ? 'am' : 'pm'), $s);
		$s = str_replace('A', ($this->hour < 12 ? 'AM' : 'PM'), $s);
		return($s);
	}
	
	// parse date into components
	function parse_date($textdate) {
		$textdate = str_replace("'", "", $textdate);
		$textdate = str_replace('"', "", $textdate);
		//get hour if any
		if (!(($hridx = strrpos($textdate, " ")) === false)) {
			$hr = substr($textdate, $hridx + 1);
			$hridx = strpos($textdate, " ");
			$textdate = substr($textdate, 0, $hridx);
		} else {
			$hr = "";
		}
		//use - or / to get date parts
		$arr = explode('-', $textdate);
		if (sizeof($arr) != 3) {
			$arr = explode('/', $textdate);
		}
		if (sizeof($arr) == 3) {
			if ($arr[0] > 31) {
				//yyyy-mm-dd || 1932-01-01 <= yy-mm-dd < 2000-01-01
				$bf_year = $arr[0]; $bf_month = $arr[1]; $bf_day = $arr[2];
			} else if ($arr[2] > 31) {
				if ($arr[1] > 12) {
					//mm-dd-yyyy
					$bf_year = $arr[2]; $bf_month = $arr[0]; $bf_day = $arr[1];
				} else {
					//dd-mm-yyyy || dd-mm-yy < 2000-01-01
					$bf_year = $arr[2]; $bf_month = $arr[1]; $bf_day = $arr[0];
				}
			} else if (($arr[2] > 12) && ($arr[1] <= 12) && ($arr[0] <= 12)) {
				//yy-mm-dd >= 2000-01-01
				$bf_year = $arr[0]; $bf_month = $arr[1]; $bf_day = $arr[2];
			} else {
				//other, assume dd-mm-yyyy
				$bf_year = $arr[2]; $bf_month = $arr[1]; $bf_day = $arr[0];
			}
		//Get date formatted as yyyymmdd
		} else if (strlen($textdate) == 8) {
			$bf_year = substr($textdate, 0, 4);
			$bf_month = substr($textdate, 4, 2);
			$bf_day = substr($textdate, 6);
		}
		if ($bf_year < 50) {
			$bf_year += 2000;
		} else if ($bf_year < 100) {
			$bf_year += 1900;
		}
		//Use : . or - to split hour
		$bf_hour = $bf_minute = $bf_second = 0;
		$arr = explode(':', $hr);
		if (sizeof($arr) < 2) {
			$arr = explode('.', $hr);
		}
		if (sizeof($arr) < 2) {
			$arr = explode('-', $hr);
		}
		//Parse hour
		if (sizeof($arr) >= 2) {
			$bf_hour = $arr[0];
			$bf_minute = $arr[1];
			if (sizeof($arr) == 3) {
				$bf_second = $arr[2];
			}
		}
		//End
		$this->year = (int) $bf_year;
		$this->month = (int) $bf_month;
		$this->day = (int) $bf_day;
		$this->hour = $bf_hour;
		$this->minute = $bf_minute;
		$this->second = $bf_second;
	}
	
	// date validity verification
	function is_valid_date() {
		if (($this->month < 1) || ($this->month > 12)) {
			//invalid month
			return(false);
		} else if (($this->day < 1) || ($this->day > 31)) {
			//invalid day
			return(false);
		} else if ($this->day <= 28) {
			//unconditional day
			return(true);
		} else if (($this->day <= 30) && ($this->month != 2)) {
			//day ok, except February
			return(true);
		} else if (($this->day == 31) && (
								 ($this->month == 1) || ($this->month == 3) || ($this->month == 5) ||
								 ($this->month == 7) || ($this->month == 8) || ($this->month == 10) ||
								 ($this->month ==12))) {
			//31 days months
			return(true);
		} else if (($this->day == 29) && 
								(($this->year % 400 == 0) ||
								 (($this->year % 100 != 0) && ($this->year % 4 == 0)))) {
			//February 29th on leap year
			return(true);
		} else {
			//error
			return(false);
		}
	}
	
	// constructor
	function Date($textdate = '') {
		if ($textdate == '') {
			$today = getdate();
			$this->year = $today['year'];
			$this->month = $today['month'];
			$this->day = $today['mday'];
			$this->hour = $today['hours'];
			$this->minute = $today['minutes'];
			$this->second = $today['seconds'];
		} else {
			$this->parse_date($textdate);
			if (!$this->is_valid_date()) {
				$this->year = 0;
				$this->month = 0;
				$this->second = 0;
			}
		}
	}
}
?>
