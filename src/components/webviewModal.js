import React, { useEffect, useState } from 'react';
import Modal from 'react-native-modal';
import { InteractionManager, View, StyleSheet } from 'react-native';
import { colors, spacing } from '@app/assets';
import _ from 'lodash';
import { RootSiblingParent } from 'react-native-root-siblings';
import WebView from 'react-native-webview';
import { Header } from 'react-native-elements';

const ModalWebViewComponent = ({ visible, onClose, url }) => {
	const [modalRenderReady, setModalRenderReady] = useState(false);
	useEffect(() => {
		const handleScreen = InteractionManager.runAfterInteractions(() => {
			setModalRenderReady(true);
		});
		return () => {
			setModalRenderReady(false);
			handleScreen.cancel();
		};
	}, []);


	function onCloseModal() {
		onClose();
	}
	function onNavigationStateChange(navigator) {
		if (navigator && navigator.url && navigator.url.indexOf('/thank.jp') !== -1) {
			onCloseModal();
		}
	}
	function onError(err) {
		console.log('err', err)
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
					<Header
						statusBarProps={{ translucent: true, barStyle: 'dark-content' }}
						centerComponent={{
							text: 'Thanh Toán',
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
					<WebView source={{ uri: url }}
						scalesPageToFit
						startInLoadingState
						onNavigationStateChange={onNavigationStateChange}
						onError={onError} />
				</RootSiblingParent>
			) : (
				<View />
			)}
		</Modal>
	);
};

function isEqual(prevProps, nextProps) {
	return prevProps.visible === nextProps.visible && prevProps.url === nextProps.url;
}
export const ModalWebView = React.memo(ModalWebViewComponent, isEqual);
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
