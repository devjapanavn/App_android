import React, { useEffect, useState } from 'react';
import { InteractionManager, View, StyleSheet } from 'react-native';
import _ from 'lodash';
import { appDimensions, colors } from '@app/assets';
import { Calendar, LocaleConfig } from 'react-native-calendars';
import Modal from 'react-native-modal';

LocaleConfig.locales.vn = {
	monthNames: [
		'Tháng 1',
		'Tháng 2',
		'Tháng 3',
		'Tháng 4',
		'Tháng 5',
		'Tháng 6',
		'Tháng 7',
		'Tháng 8',
		'Tháng 9',
		'Tháng 10',
		'Tháng 11',
		'Tháng 12'
	],
	monthNamesShort: [
		'Tháng 1',
		'Tháng 2',
		'Tháng 3',
		'Tháng 4',
		'Tháng 5',
		'Tháng 6',
		'Tháng 7',
		'Tháng 8',
		'Tháng 9',
		'Tháng 10',
		'Tháng 11',
		'Tháng 12'
	],
	dayNames: ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'],
	dayNamesShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
};
LocaleConfig.defaultLocale = 'vn';

export const CalendarComponent = ({ visible, onClose, current, onSelectDay }) => {
	const [isModalReady, setIsModalReady] = useState(false);

	useEffect(() => {
		InteractionManager.runAfterInteractions(() => {
			if (visible) {
				setIsModalReady(true);
			} else {
				setIsModalReady(false);
			}
		});
	}, [visible]);

	return (
		<Modal
			style={[styles.modal]}
			isVisible={visible}
			onBackButtonPress={onClose}
			onBackdropPress={onClose}>
			<View style={styles.container}>
				{isModalReady ? (
					<Calendar
						style={styles.calendarContainer}
						animation="fadeIn"
						markedDates={{
							[current]: {
								selected: true,
								marked: true,
								selectedColor: colors.primary
							}
						}}
						current={current}
						onDayPress={day => onSelectDay(day)}
						monthFormat={'MMMM - yyyy'}
						// onMonthChange={(month) => { console.log('month changed', month) }}
						hideExtraDays={false}
						firstDay={1}
						hideDayNames={false}
						showWeekNumbers={true}
						enableSwipeMonths={true}
					/>
				) : null}
			</View>
		</Modal>
	);
};

function areEqual(prevProps, nextProps) {
	return (
		prevProps.visible === nextProps.visible && _.isEqual(prevProps.current, nextProps.current)
	);
}
export const CalendarModal = React.memo(CalendarComponent, areEqual);

const styles = StyleSheet.create({
	container: {
		height: 355,
		backgroundColor: colors.white,
		width: '100%',
		borderTopLeftRadius: 20,
		borderTopRightRadius: 20,
		overflow: 'hidden'
	},
	modal: {
		width: '100%',
		borderTopLeftRadius: 5,
		borderTopRightRadius: 5,
		height: 200,
		margin: 0,
		justifyContent: 'flex-end'
	},
	contentModal: {
		flex: 1,
		paddingLeft: 20,
		paddingRight: 15,
		paddingBottom: 10,
		marginBottom: 20,
		flexDirection: 'column'
	}
});
