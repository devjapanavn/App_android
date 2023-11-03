import React from 'react';
import { Text, View } from 'react-native';
import _ from 'lodash';
import { StyleSheet } from 'react-native';
import { colors, spacing } from '@app/assets';
import { material } from 'react-native-typography';
import * as Animatable from 'react-native-animatable'
export const ProccessModal = React.memo(
	({ visible, value }) => {
		if (visible) {
			return (
				<Animatable.View style={styles.overlay} animation="fadeIn">
					<View
						style={styles.container}>
						<Text style={material.display1}>{Math.round(value)}%</Text>
						<Text>Đang upload nội dung</Text>
					</View>
				</Animatable.View>
			);
		}
		return <View />;
	},
	(prevProps, nextProps) =>
		prevProps.visible === nextProps.visible && prevProps.value === nextProps.value
);
const styles = StyleSheet.create({
	overlay: {
		...StyleSheet.absoluteFillObject,
		top: 0,
		left: 0,
		right: 0,
		bottom: 0,
		flex: 1,
		backgroundColor: 'rgba(0,0,0,0.5)',
		zIndex: 999,
		justifyContent: 'center',
		alignItems: 'center'
	},
	container: {
		backgroundColor: colors.white,
		alignItems: 'center',
		padding: spacing.medium,
		borderRadius: 8
	}
});
