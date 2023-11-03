import {appDimensions, globalStyles, images} from '@app/assets';
import {ProductSlide} from '@app/components';
import React from 'react';
import {StyleSheet} from 'react-native';
import {View} from 'react-native';
import {Avatar, Icon, Text} from 'react-native-elements';
import StarRating from 'react-native-star-rating';
const Component = () => {
  return (
    <View style={styles.container}>
      <Avatar source={images.news_detail_avatar} rounded size={160} />
      <Text style={styles.fullname}>Sương Trần</Text>
      <StarRating
        starSize={16}
        disabled={true}
        maxStars={5}
        rating={5}
        fullStarColor={'rgb(243, 18, 89)'}
        emptyStarColor={'rgb(59, 72, 89)'}
        starStyle={{marginHorizontal: 2}}
        iconSet="MaterialIcons"
        fullStar={'favorite'}
      />
      <View style={styles.info}>
        <Icon name="article" type="material" color="rgb(138, 138, 143)" />
        <Text style={[styles.textLink, styles.mr_15, styles.ml_4]}>
          452 bài viết
        </Text>
        <Icon name="comment" type="material" color="rgb(138, 138, 143)" />
        <Text style={[styles.text, styles.ml_4]}>1.362 tương tác</Text>
      </View>
      <View style={styles.description}>
        <Text style={styles.descriptionText}>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Vestibulum ac
          vehicula leo. Donec urna lacus gravida ac vulputate sagittis tristique
          vitae lectus. Nullam rhoncus tortor at dignissim vehicula.
        </Text>
      </View>
    </View>
  );
};
export const AuthorInfomation = React.memo(Component, (prev, next) => false);
const styles = StyleSheet.create({
  separator: {
    width: 0.3,
    backgroundColor: '#fff',
  },
  info: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 10,
  },
  container: {
    backgroundColor: '#fff',
    paddingHorizontal: 10,
    paddingVertical: 15,
    marginBottom: 10,
    alignItems: 'center',
  },
  title: {
    ...globalStyles.text,
    fontSize: 16,
    fontWeight: '500',
    lineHeight: 24,
    paddingVertical: 4,
  },
  headerContainer: {
    borderBottomColor: '#ffa200',
    borderBottomWidth: 2,
  },
  textLink: {
    ...globalStyles.text,
    color: '#2367ff',
    fontSize: 14,
  },
  text: {
    ...globalStyles.text,
    fontSize: 14,
    color: '#3b4859',
  },
  mr_15: {
    marginRight: 15,
  },
  ml_4: {
    marginLeft: 4,
  },
  fullname: {
    ...globalStyles.text,
    color: '#000',
    fontSize: 16,
    lineHeight: 24,
    textAlign: 'center',
    marginTop: 15,
  },
  description: {
    backgroundColor: '#f0f8ff',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 15,
  },
  descriptionText: {
    ...globalStyles.text,
    fontSize: 14,
    color: '#3b4859',
    lineHeight: 24,
  },
});
