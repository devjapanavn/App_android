import React, { useEffect, useState } from 'react';
import Modal from 'react-native-modal';
import { ScrollView, InteractionManager, View, StyleSheet, Text, TextInput } from 'react-native';
import { colors, spacing } from '@app/assets';
import { ListItem, Button, Header } from 'react-native-elements';
import api from '@app/api';
import { human } from 'react-native-typography';
import { toastAlert } from '@app/utils';
import _ from 'lodash';
import { RootSiblingParent } from 'react-native-root-siblings';

const options = [
	'Nội dung nhạy cảm',
	'Ảnh khoả thân',
	'Lừa đảo',
	'Bạo lực',
	'Spam',
	'Khủng bố',
	'Lí do khác'
];

const ModalReport = ({ visible, onClose, id }) => {
	const [modalRenderReady, setModalRenderReady] = useState(false);
	const [reasonIndex, setReasonIndex] = useState(-1);
	const [reason, setReason] = useState('');

	useEffect(() => {
		const handleScreen = InteractionManager.runAfterInteractions(() => {
			setModalRenderReady(true);
		});
		return () => {
			setModalRenderReady(false);
			handleScreen.cancel();
		};
	}, []);

	async function reportNewFeed() {
		try {
			let detail = '';
			if (reasonIndex === options.length - 1) {
				detail = reason.trim();
			} else {
				detail = options[reasonIndex];
			}
			if (!detail) {
				toastAlert('Vui lòng nhập lý do báo cáo');
				return false;
			}
			const res = await api.reportNewFeed(id, detail);
			onCloseModal();
			setTimeout(() => {
				toastAlert('Đã báo cáo bài viết');
			}, 300);
		} catch (err) {
			onCloseModal();
			setTimeout(() => {
				toastAlert('Xảy ra lỗi không báo cáo bài viết');
			}, 300);
		}
	}

	function onCloseModal() {
		setReasonIndex(-1);
		setReason('');
		onClose();
	}
	return (
		<Modal
			isVisible={visible}
			style={styles.modalFullScreen}
			onBackButtonPress={onCloseModal}
			useNativeDriver={true}
			onBackdropPress={onCloseModal}>
			{modalRenderReady ? (
				<RootSiblingParent>
					<>
						<ScrollView style={styles.modal_container} stickyHeaderIndices={[0]}>
							<Header
								statusBarProps={{ translucent: true, barStyle: 'dark-content' }}
								centerComponent={{
									text: 'Báo cáo bài viết',
									style: styles.header_title,
									numberOfLines: 2
								}}
								leftComponent={{
									icon: 'chevron-back-outline',
									type: 'ionicon',
									onPress: onCloseModal,
									color: colors.primary
								}}
								backgroundColor={colors.white}
							/>
							{options.map((l, i) => (
								<ListItem key={i} bottomDivider onPress={() => setReasonIndex(i)}>
									<ListItem.Content>
										<ListItem.Title>{l}</ListItem.Title>
									</ListItem.Content>
									<ListItem.CheckBox
										onPress={() => setReasonIndex(i)}
										checkedColor={colors.primary}
										checked={reasonIndex === i}
									/>
								</ListItem>
							))}
							{reasonIndex === options.length - 1 ? (
								<View style={styles.section_container}>
									<Text style={human.body}>Nhập lý do khác</Text>
									<TextInput
										style={styles.note_container}
										defaultValue={reason}
										onChangeText={setReason}
										multiline={true}
									/>
								</View>
							) : null}
						</ScrollView>
						<Button
							title="Báo cáo"
							containerStyle={{
								padding: spacing.medium,
								backgroundColor: colors.background
							}}
							buttonStyle={{ backgroundColor: colors.primary }}
							onPress={reportNewFeed}
						/>
					</>
				</RootSiblingParent>
			) : (
				<View />
			)}
		</Modal>
	);
};

function isEqual(prevProps, nextProps) {
	return prevProps.visible === nextProps.visible;
}
export const ReportModal = React.memo(ModalReport, isEqual);
const styles = StyleSheet.create({
	modalFullScreen: {
		padding: 0,
		margin: 0
	},
	modal_container: {
		flex: 1,
		backgroundColor: colors.background
	},
	header_title: {
		fontSize: 16,
		fontWeight: '700',
		color: colors.primary
	},
	time: {
		textAlign: 'right',
		padding: spacing.small,
		fontStyle: 'italic',
		color: colors.text
	},
	note_container: {
		marginVertical: spacing.small,
		backgroundColor: colors.white,
		borderRadius: 8,
		padding: spacing.small,
		height: 120,
		textAlignVertical: 'top',
		color: colors.text
	},
	section_container: {
		marginHorizontal: spacing.medium,
		marginVertical: spacing.small
	}
});
