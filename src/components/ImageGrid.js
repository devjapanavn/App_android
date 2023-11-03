import { colors } from '@app/assets';
import React from 'react';
import { Text, View, TouchableOpacity, StyleSheet } from 'react-native';
import FastImage from 'react-native-fast-image';
import PropTypes from 'prop-types';
import { ViewPropTypes } from 'react-native';
import _ from 'lodash';
import { getExtension } from '@app/utils';
import { Icon } from 'react-native-elements';
const ImageItem = props => {
	const ext = props.image ? getExtension(props.image) : null;
	return props.image ? (
		<TouchableOpacity
			style={styles.image_view}
			onPress={event => props.onPress(props.image, props.index, event)}>
			<FastImage
				style={styles.image}
				resizeMode="cover"
				source={{
					uri: props.image
					, priority: 'normal'
				}}>
				{_.isEqual(ext, 'mp4') ? (
					<Icon
						name="play-circle-outline"
						type="ionicon"
						color={colors.white}
						size={60}
					/>
				) : null}
			</FastImage>
		</TouchableOpacity>
	) : (
		<View />
	);
};

const TwoImages = props => {
	return (
		<>
			<ImageItem image={props.images[0]} onPress={props.onPress} index={0} />
			<ImageItem image={props.images[1]} onPress={props.onPress} index={1} />
		</>
	);
};

const renderImages = (start, overflow, images, onPress) => {
	return (
		<>
			<ImageItem image={images[start]} onPress={onPress} index={start} />
			{images[start + 1] && (
				<View style={styles.image_view}>
					<ImageItem image={images[start + 1]} onPress={onPress} index={start + 1} />
					{overflow && (
						<TouchableOpacity
							onPress={event => onPress(images[start + 1], start + 1, event)}
							style={styles.item_view_overlay}>
							<Text style={styles.text}>{`+${images.length - 5}`}</Text>
						</TouchableOpacity>
					)}
				</View>
			)}
		</>
	);
};

const ImageGridComponent = props => {
	const { images, style, onPress } = props;
	return images.length > 0 ? (
		<View style={{ ...styles.container_row, ...style }}>
			{images.length < 3 ? (
				<TwoImages images={images} onPress={onPress} />
			) : (
				<ImageItem image={images[0]} onPress={onPress} index={0} />
			)}
			{images.length > 2 && (
				<View style={styles.container}>{renderImages(1, false, images, onPress)}</View>
			)}
			{images.length > 3 && (
				<View style={styles.container}>
					{renderImages(3, images.length > 5, images, onPress)}
				</View>
			)}
		</View>
	) : null;
};
function areEqual(prevProps, nextProps) {
	return _.isEqual(prevProps.images, nextProps.images);
}
export const ImageGrid = React.memo(ImageGridComponent, areEqual);

ImageGridComponent.propTypes = {
	images: PropTypes.array,
	style: ViewPropTypes.style,
	onPress: PropTypes.func
};

ImageGridComponent.defaultProps = {
	images: [],
	onPress: (image, index, event) => null
};

export const styles = StyleSheet.create({
	container_row: {
		flexDirection: 'row'
	},

	container: {
		flex: 1
	},

	image_view: {
		flex: 1,
		borderColor: colors.white,
		borderWidth: 0.5
	},

	image: {
		width: '100%',
		height: '100%',
		backgroundColor: 'grey',
		justifyContent: 'center',
		alignItems: 'center'
	},

	item_view: {
		flex: 1,
		backgroundColor: 'white',
		alignItems: 'center',
		justifyContent: 'center'
	},

	item_view_overlay: {
		width: '100%',
		height: '100%',
		position: 'absolute',
		backgroundColor: 'rgba(52, 52, 52, 0.8)',
		justifyContent: 'center',
		alignItems: 'center'
	},

	text: {
		color: 'white',
		fontSize: 18
	}
});
