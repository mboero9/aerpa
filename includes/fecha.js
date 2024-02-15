function isArray(obj) {
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}

function checkValidYear(year)
{
	yr = new Number(year);
	if (isNaN(yr)) {
		alert("El año no es válido");
	} else if (yr < 1) {
		alert("El año no es válido");
	}
}

function checkValidMonth(month)
{
  mn = new Number(month);
	if (isNaN(mn)) {
		alert("El mes no es válido");
	} else if ((mn < 1) || (mn > 12)) {
		alert("El mes no es válido");
	}
}

//Validates date, considering leap years
function isValidDate(yr, mn, dy)
{
	if ((mn < 1) || (mn > 12)) {
		//invalid month
		return(false);
	} else if ((dy < 1) || (dy > 31)) {
		//invalid day
		return(false);
	} else if (dy <= 28) {
		//unconditional day
		return(true);
	} else if ((dy <= 30) && (mn != 2)) {
		//day ok, except February
		return(true);
	} else if ((dy == 31) && (
							 (mn == 1) || (mn == 3) || (mn == 5) ||
							 (mn == 7) || (mn == 8) || (mn == 10) ||
							 (mn ==12))) {
		//31 days months
		return(true);
	} else if ((dy == 29) &&
							((yr % 400 == 0) ||
							 ((yr % 100 != 0) && (yr % 4 == 0)))) {
		//February 29th on leap year
		return(true);
	} else {
		//error
		return(false);
	}
}

function checkDate(s) {
	ok = checkDateFormat(s);

	if (ok) {
		arr = s.split("/");
		if (arr.length != 3) {
			arr = s.split("-");
		}
		if (arr.length != 3) {
			arr = s.split(".");
	 	}
	 	ok = (arr.length == 3);
	}

	if (ok) {
		if (arr[0] > 31) {
			//yyyy-mm-dd || yy-mm-dd < 2000-01-01
			yr = arr[0]; mn = arr[1]; dy = arr[2];
		} else if (arr[2] > 31) {
			if (arr[1] > 12) {
				//mm-dd-yyyy
				yr = arr[2]; mn = arr[0]; dy = arr[1];
			} else {
				//dd-mm-yyyy || dd-mm-yy < 2000-01-01
				yr = arr[2]; mn = arr[1]; dy = arr[0];
			}
		} else if ((arr[2] > 12) && (arr[1] <= 12) && (arr[0] <= 12)) {
			//yy-mm-dd >= 2000-01-01
			yr = arr[0]; mn = arr[1]; dy = arr[2];
		} else {
			//other, assume dd-mm-yyyy
			yr = arr[2]; mn = arr[1]; dy = arr[0];
		}

		yr = new Number(yr);
		mn = new Number(mn);
		dy = new Number(dy);

		ok = ((!isNaN(yr)) && (!isNaN(mn)) && (!isNaN(dy)));
	}

	if (ok) {
		//Convert 2 digit years to 1950-2049 period
		if (yr < 50) {
			yr += 2000;
		} else if (yr < 100) {
			yr += 1900;
		}
		ok = (isValidDate(yr, mn, dy));
	}

	if (!ok) {
		return (false);
	} else {
		return (new Array(yr, mn, dy));
	}
}

function checkTime(s) {
	ok = checkTimeFormat(s);

	if (ok) {
		arr = s.split(":");
	 	ok = ((arr.length == 3) || (arr.length = 2));
	}

	if (ok) {
		hr = new Number(arr[0]);
		min = new Number(arr[1]);
		if (arr[2]) {
			sec = new Number(arr[2]);
		} else {
			sec = 0;
		}

		ok = (
			(hr >= 0) && (hr <= 23) &&
			(min >= 0) && (min <= 59) &&
			(sec >= 0) && (sec <= 59));
	}

	if (!ok) {
		return (false);
	} else {
		return (new Array(hr, min, sec));
	}
}

//Check date input format
function checkDateFormat(s) {
	rx = /^([0-9]{1,2}[\-\/\.][0-9]{1,2}[\-\/\.][0-9]{2}|[0-9]{1,2}[\-\/\.][0-9]{1,2}[\-\/\.][0-9]{4}|[0-9]{4}[\-\/\.][0-9]{1,2}[\-\/\.][0-9]{1,2})$/;
	return (rx.test(s));
}

function checkTimeFormat(s) {
	rx = /^[0-9]{1,2}:[0-9]{1,2}(|:[0-9]{1,2})$/;
	return (rx.test(s));
}

function formatDate(yr, mn, dy, fmt) {
	f = new String(fmt);

	buf = yr.toString();
	f = f.replace('%Y', buf);

	buf = yr.toString().substr(2,2);
	f = f.replace('%y', buf);

	buf = ((mn < 10) ? "0" + mn.toString() : mn.toString());
	f = f.replace('%m', buf);

	buf = mn.toString();
	f = f.replace('%n', buf);

	buf = ((dy < 10) ? "0" + dy.toString() : dy.toString());
	f = f.replace('%d', buf);

	buf = dy.toString();
	f = f.replace('%j', buf);

	return(f);
}

function formatTime(hr, min, sec, fmt) {
	f = new String(fmt);

	buf = ((hr < 10) ? "0" + hr.toString() : hr.toString());
	f = f.replace('%H', buf);

	buf = hr.toString();
	f = f.replace('%G', buf);

	buf = ((min < 10) ? "0" + min.toString() : min.toString());
	f = f.replace('%i', buf);

	buf = ((sec < 10) ? "0" + sec.toString() : sec.toString());
	f = f.replace('%s', buf);

	return(f);
}

//Parse date in textbox, and replace with formatted date
//(default format: ISO 8601 - yyyy-mm-dd)
function parseDate(txt, fmt, silent)
{
	var s, arr, yr, mn, dy;
	if (!fmt) {
		fmt = "%Y/%m/%d";
	}
	s = new String(txt.value);
	if (s.length == 0) {
		return (true);
	} else if (checkDateFormat(s)) {
		dtarr = checkDate(s);
		if (dtarr) {
			txt.value = formatDate(dtarr[0], dtarr[1], dtarr[2], fmt);
			return (true);
		} else {
			if (!silent) {
				alert("Fecha inválida");
			}
			return (false);
		}
	} else {
		if (!silent) {
			alert("El formato de fecha no es correcto");
		}
		return (false);
	}
}

function parseTime(txt, fmt, silent)
{
	var s, arr, hr, min, sec;
	if (!fmt) {
		fmt = "%H:%i:%s";
	}
	s = new String(txt.value);
	if (s.length == 0) {
		return (true);
	} else if (checkTimeFormat(s)) {
		hrarr = checkTime(s);
		if (hrarr) {
			txt.value = formatTime(hrarr[0], hrarr[1], hrarr[2], fmt);
			return (true);
		} else {
			if (!silent) {
				alert("Hora inválida");
			}
			return (false);
		}
	} else {
		if (!silent) {
			alert("El formato de hora no es correcto");
		}
		return (false);
	}
}

//Return values:
//1 - first is greater
//2 - second is greater
//3 - same date
//false - parameter error
function compareDate(dt1, dt2)
{
	if ((isArray(dt1)) && (isArray(dt2)) &&
			(dt1.length == 3) && (dt2.length == 3) &&
			(isValidDate(dt1[0], dt1[1], dt1[2])) && (isValidDate(dt2[0], dt2[1], dt2[2]))) {

		intdate1 = Number(dt1[0]) * 10000 + Number(dt1[1]) * 100 + Number(dt1[2]);
		intdate2 = Number(dt2[0]) * 10000 + Number(dt2[1]) * 100 + Number(dt2[2]);

		if (intdate1 == intdate2) {
			//same
			return (3);
		} else if (intdate1 > intdate2) {
			//first is greater
			return (1);
		} else {
			//second is greater
			return (2)
		}
	} else {
		return (false);
	}
}

function compareTime(tm1, tm2)
{
	if ((isArray(tm1)) && (isArray(tm2)) &&
			(tm1.length == 3) && (tm2.length == 3) &&
			(checkTime(tm1[0]+":"+tm1[1]+":"+tm1[2])) && (checkTime(tm2[0]+":"+tm2[1]+":"+tm2[2]))) {

		inttime1 = Number(tm1[0]) * 10000 + Number(tm1[1]) * 100 + Number(tm1[2]);
		inttime2 = Number(tm2[0]) * 10000 + Number(tm2[1]) * 100 + Number(tm2[2]);

		if (inttime1 == inttime2) {
			//same
			return (3);
		} else if (inttime1 > inttime2) {
			//first is greater
			return (1);
		} else {
			//second is greater
			return (2)
		}
	} else {
		return (false);
	}
}

function diffDate(dt1, dt2) {
	if ((isArray(dt1)) && (isArray(dt2)) &&
			(dt1.length == 3) && (dt2.length == 3) &&
			(isValidDate(dt1[0], dt1[1], dt1[2])) && (isValidDate(dt2[0], dt2[1], dt2[2]))) {
		do1 = new Date(dt1[0], dt1[1], dt1[2]);
		do2 = new Date(dt2[0], dt2[1], dt2[2]);
		return (do2 - do1);
	} else {
		return (false);
	}
}

function diffDateSec(dt1, dt2) {
	a = diffDate(dt1, dt2);
	if (a) {
		b = new String(a / 1000);
		pt = b.indexOf(".");
		if (pt >= 0) { b = b.substring(0,pt); }
		return (new Number(b));
	} else {
		return (false);
	}
}

function diffDateDay(dt1, dt2) {
	a = diffDate(dt1, dt2);
	if (a) {
		b = new String(a / 86400000);
		pt = b.indexOf(".");
		if (pt >= 0) { b = b.substring(0,pt); }
		return (new Number(b));
	} else {
		return (false);
	}
}