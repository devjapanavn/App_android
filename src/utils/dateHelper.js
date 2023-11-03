import moment from 'moment';

function convertTimeAgo(date, formatIn = 'YYYY-MM-DD HH:mm:ss') {
	if (date) {
		try {
			const momentFormat = moment(date, formatIn);
			return momentFormat.fromNow(false);
		} catch (error) {
			return '';
		}
	}
	return '';
}
function formatStringDate(date) {
	if (moment.isMoment(date)) {
		if (moment().startOf('date').isSame(date.startOf('date'))) {
			return 'Hôm nay';
		} else {
			return moment(date).format('DD/MM/YYYY');
		}
	}
}
function convertDateStringToString(
	date,
	formatIn = 'YYYY-MM-DD HH:mm:ss',
	formatOut = 'DD/MM/YYYY'
) {
	const formatStringToDate = moment(date, formatIn);
	if (date && moment.isMoment(formatStringToDate)) {
		if (moment().startOf('date').isSame(formatStringToDate.startOf('date'))) {
			return 'Hôm nay';
		} else {
			return moment(date).format(formatOut);
		}
	} else {
		return null;
	}
}

function formatStringToDate(date = moment().format('DD/MM/YYYY'), format = 'DD/MM/YYYY') {
	const dateMoment = moment(date, format);
	if (moment.isMoment(dateMoment)) {
		if (moment().startOf('date').isSame(dateMoment.startOf('date'))) {
			return 'Hôm nay';
		} else {
			return moment(dateMoment).format('DD/MM/YYYY');
		}
	}
}

function formatStringToDateApi(date, format = 'DD/MM/YYYY') {
	const dateMoment = moment(date, format);
	if (moment.isMoment(dateMoment)) {
		return moment(dateMoment).format('YYYY-MM-DD');
	}
}
function convertDateToEpgTime(date) {
	const dateMoment = moment(date * 1000);
	return dateMoment.format('HH:mm');
}

function formatDateApi(date) {
	if (moment.isMoment(date)) {
		return moment(date).format('YYYY-MM-DD');
	}
}

function convertToMoment(day) {
	return moment(day, 'YYYY-MM-DD');
}

function convertToString(day) {
	return moment(day).format('DD/MM/YYYY');
}

function generateDaysRange(fromDay, numOfDays = 7) {
	const dates = [];
	for (let i = numOfDays - 1; i >= 0; i--) {
		let date = moment(fromDay);
		date.subtract(i, 'day').format('DD-MM-YYYY');
		dates.push({
			dateString: date.format('DD/MM'),
			date: date,
			isLive: false
		});
	}
	dates.push({
		dateString: 'LIVE',
		date: moment(),
		isLive: true
	});

	return dates;
}

function convertStringToDate(date, format = 'YYYY-MM-DD HH:mm:ss') {
	try {
		if (date) {
			return moment(date, format);
		}
	} catch (error) {
		throw error;
	}
}

function convertStringToMminus(second) {
	try {
		if (second) {
			const secondNumber = parseInt(second);
			if (secondNumber && secondNumber > 60) {
				return parseInt(secondNumber / 60) + "'";
			}
		}
	} catch (error) {
		return second;
	}
}

function converCountDown(countdown = 0, start_time) {
	if (countdown <= 0) {
		return 'Đang diễn ra';
	} else {
		let minus = 0;
		let hours = 0;
		if (countdown < 60) {
			return `Khoảng ${countdown} giây`;
		} else {
			minus = parseInt(countdown / 60);
			if (minus < 60) {
				return `${start_time}, còn khoảng ${minus} phút`;
			} else {
				hours = parseInt(minus / 60);
				return `${start_time}, còn Khoảng ${hours} giờ`;
			}
		}
	}
}

function converDurationTime(time) {
	return ~~(time / 60) + ':' + (time % 60 < 10 ? '0' : '') + (time % 60);
}
export {
	convertDateStringToString,
	formatStringDate,
	formatDateApi,
	convertStringToDate,
	generateDaysRange,
	converCountDown,
	convertToMoment,
	convertStringToMminus,
	formatStringToDate,
	formatStringToDateApi,
	convertToString,
	convertDateToEpgTime,
	converDurationTime,
	convertTimeAgo
};
