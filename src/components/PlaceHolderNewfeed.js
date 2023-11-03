import { appDimensions, colors, spacing } from '@app/assets';
import { ImageGrid } from '@app/components';
import { CONFIGS } from '@app/constants';
import { stringHelper } from '@app/utils';
import React, { useEffect, useState } from 'react';
import { StyleSheet } from 'react-native';
import { View, Text } from 'react-native';
import { Avatar, Divider, Icon } from 'react-native-elements';
import { human } from 'react-native-typography';
import { Fade, Placeholder, PlaceholderLine, PlaceholderMedia } from 'rn-placeholder';

const PlaceHolderNewfeedComponent = ({ numberItem = 5 }) => {
	let placeholderList = [];
	for (let i = 0; i < numberItem; i++) {
		placeholderList.push(
			<View style={styles.container}>
				<View style={styles.header}>
					<Placeholder
						Animation={Fade}
						Left={props => <PlaceholderMedia isRound={true} />}>
						<PlaceholderLine width={80} />
						<PlaceholderLine width={30} />
					</Placeholder>
				</View>
				<Divider />
				<Placeholder Animation={Fade} style={styles.description}>
					<PlaceholderLine width={90} />
				</Placeholder>
				<Placeholder Animation={Fade}>
					<PlaceholderMedia style={styles.imageList} />
				</Placeholder>
			</View>
		);
	}

	return (
		<>
			{placeholderList.map((item, index) => (
				<View key={'placeholder_' + index}>{item}</View>
			))}
		</>
	);
};

export const PlaceHolderNewfeed = React.memo(PlaceHolderNewfeedComponent);

const styles = StyleSheet.create({
	container: {
		backgroundColor: colors.white,
		margin: spacing.medium
	},
	header: {
		flexDirection: 'row',
		alignItems: 'center',
		padding: spacing.small
	},
	header_title: {
		flex: 1,
		paddingHorizontal: spacing.small
	},
	header_name: {},
	header_date: {
		...human.caption1Object,
		color: colors.border
	},
	description: {
		...human.caption1,
		padding: spacing.medium
	},
	imageList: {
		width: appDimensions.width - spacing.medium * 2,
		height: 300
	}
});
