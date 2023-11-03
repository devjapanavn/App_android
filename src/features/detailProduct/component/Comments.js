import { colors, globalStyles, images } from '@app/assets';
import { convertTimeAgo, stringHelper } from '@app/utils';
import React, { useState } from 'react';
import {
  FlatList,
  StyleSheet,
  TouchableWithoutFeedback,
  View,
} from 'react-native';
import { Avatar, Text, Button, Divider, } from 'react-native-elements';
import StarRating from 'react-native-star-rating';
import { iOSColors } from 'react-native-typography';
import { navigateRoute } from '@app/route';
import { ROUTES } from '@app/constants';
import FastImage from 'react-native-fast-image';
import _ from 'lodash';
import ImageView from 'react-native-image-viewing';

export const CommentHeader = React.memo(
  ({ dataRating }) => {
    if (dataRating && !_.isEmpty(dataRating)) {
      return (
        <>
          <View style={styles.box}>
            <Text
              style={{ ...globalStyles.text, fontSize: 15, fontWeight: '600' }}>
              Khách hàng đánh giá
            </Text>
            <View style={{ marginHorizontal: 10 }}>
              <View
                style={{
                  flexDirection: 'row',
                  alignItems: 'center',
                }}>
                <Text style={styles.ratingPoint}>
                  {dataRating.medium_rate || 0}
                </Text>
                <StarRating
                  containerStyle={styles.ratingStar}
                  starSize={20}
                  disabled={true}
                  maxStars={5}
                  rating={stringHelper.formatToNumber(dataRating.medium_rate)}
                  fullStarColor={iOSColors.yellow}
                  emptyStarColor={iOSColors.gray}
                  starStyle={{ marginHorizontal: 2 }}
                />
                <Text style={styles.ratingCount}>
                  {stringHelper.formatMoney(dataRating.total_comment || 0)} đánh
                  giá
                </Text>
              </View>
              <Divider />
            </View>
          </View>
        </>
      );
    }
    return <View />;
  },
  (prev, next) => _.isEqual(prev.dataRating, next.dataRating),
);

export const CommentItem = React.memo(
  ({ comment }) => {
    const [imageViewer, setImageViewer] = useState({ index: 0, visible: false });
    return (
      <View style={styles.itemContainer}>
        <View style={{ flexDirection: 'row', marginBottom: 10 }}>
          <Avatar
            // source={images.ic_no_avatar}
            title={stringHelper.getTitleAvatar(comment.fullname)}
            titleStyle={{ color: '#fff' }}
            overlayContainerStyle={{ backgroundColor: '#007bff' }}
            rounded
            size={'small'}
          />
          <View style={styles.itemHeader}>
            <Text style={styles.itemTitle}>{comment.fullname}</Text>
            <View style={{ flexDirection: 'row', marginVertical: 2 }}>
              <StarRating
                starSize={16}
                disabled={true}
                maxStars={5}
                rating={stringHelper.formatToNumber(comment.rate)}
                fullStarColor={iOSColors.yellow}
                emptyStarColor={iOSColors.gray}
                starStyle={{ marginHorizontal: 2 }}
              />
              <Text style={styles.commentHeaderCreated}>
                ({convertTimeAgo(comment.created)})
              </Text>
            </View>
          </View>
        </View>
        <View>
          <Text style={styles.commentText}>{comment.comments}</Text>
        </View>
        {comment.images ? (
          <>
            <FlatList
              horizontal
              style={{ marginVertical: 8 }}
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={{
                flex: 1,
                alignItems: 'center',
              }}
              data={comment.images}
              ItemSeparatorComponent={() => <View style={{ width: 10 }} />}
              renderItem={({ item, index }) => (
                <TouchableWithoutFeedback
                  onPress={() => setImageViewer({ index: index, visible: true })}>
                  <FastImage source={{ uri: item }} style={styles.itemImage} />
                </TouchableWithoutFeedback>
              )}
            />
            <ImageView
              presentationStyle="overFullScreen"
              images={comment.images.map(img => ({ uri: img }))}
              imageIndex={imageViewer.index}
              swipeToCloseEnabled={true}
              visible={imageViewer.visible}
              onRequestClose={() => setImageViewer({ visible: false, index: 0 })}
            />
          </>
        ) : null}

        {comment.rep_comment ? (
          <FlatList
            key={'product_rely_comment'}
            removeClippedSubviews={true}
            style={styles.relyContainer}
            scrollEnabled={false}
            showsHorizontalScrollIndicator={false}
            data={comment.rep_comment}
            keyExtractor={item => 'product_rely_comment_' + item.id}
            renderItem={({ item }) => (
              <View style={{ flexDirection: 'row', marginLeft: 10 }}>
                <Avatar
                  source={images.ic_comment}
                  imageProps={{ resizeMethod: 'auto', resizeMode: 'center' }}
                  title={item.fullname}
                  rounded
                  containerStyle={{ flex: 0, backgroundColor: colors.primary }}
                  size={'small'}
                />
                <View
                  style={{
                    marginLeft: 8,
                    backgroundColor: '#fafdff',
                    padding: 10,
                    borderRadius: 4,
                    borderColor: '#e5eeff',
                    borderWidth: 1,
                    flex: 1,
                  }}>
                  <View
                    style={{
                      flexDirection: 'row',
                      alignItems: 'center',
                      marginBottom: 10,
                    }}>
                    <Text
                      style={{ fontSize: 13, color: '#000', fontWeight: '500' }}>
                      {item.fullname}
                    </Text>
                    <Text style={{ fontSize: 12, color: '#888', marginLeft: 10 }}>
                      ({convertTimeAgo(comment.created)})
                    </Text>
                  </View>
                  <Text
                    style={{
                      fontSize: 13,
                      color: '#000',
                      lineHeight: 20,
                      letterSpacing: 0,
                      textAlign: 'left',
                    }}>
                    {item.comments}
                  </Text>
                </View>
              </View>
            )}
          />
        ) : null}
        <Divider style={{ backgroundColor: '#e3e3e3' }} />

      </View>
    );
  },
  () => true,
);

export const CommentFooter = React.memo(
  ({ ratings, mainProduct }) => {
    if (ratings && !_.isEmpty(ratings))
      return (
        <>
          <View
            style={{
              flexDirection: 'row',
              backgroundColor: '#fff',
              paddingBottom: 5,
              marginBottom: 5,
            }}>
            <Button
              type="outline"
              onPress={() =>
                navigateRoute(ROUTES.ALL_COMMENT, mainProduct, null, false)
              }
              title="Xem tất cả đánh giá"
              containerStyle={{ flex: 1, marginHorizontal: 10 }}
            />
            <Button
              title="Viết đánh giá"
              type="outline"
              onPress={() =>
                navigateRoute(ROUTES.WRITE_COMMENT, mainProduct, null, true)
              }
              containerStyle={{ flex: 1, marginHorizontal: 10 }}
            />
          </View>
        </>
      );
    return (
      <View style={styles.emptyContainer}>
        <View
          style={{
            flexDirection: 'row',
            justifyContent: 'center',
            alignItems: 'flex-end',
          }}>
          <FastImage source={images.ic_like} style={{ width: 30, height: 30 }} />
          <StarRating
            starSize={20}
            disabled={true}
            maxStars={5}
            rating={5}
            fullStarColor={iOSColors.yellow}
          />
        </View>
        <Text style={styles.emptyTitle}>Mời quý khách đánh giá sản phẩm</Text>
        <Button
          title="Viết đánh giá"
          type="outline"
          onPress={() =>
            navigateRoute(ROUTES.WRITE_COMMENT, mainProduct, null, true)
          }
          containerStyle={{ flex: 1, marginHorizontal: 10 }}
        />
      </View>
    );
  },
  () => true,
);

const styles = StyleSheet.create({
  box: {
    padding: 10,
    backgroundColor: '#fff',
    paddingVertical: 4,
  },
  headerTitleStyle: {
    fontSize: 16,
    margin: 10,
    color: '#000',
    fontWeight: '500',
    flex: 1,
  },
  ratingPoint: {
    fontSize: 24,
    color: '#000',
  },
  ratingStar: {
    marginHorizontal: 10,
  },
  ratingCount: {
    color: '#888',
    fontSize: 12,
  },
  itemContainer: {
    backgroundColor: '#fff',
    padding: 10,
  },
  itemHeader: {
    flexDirection: 'column',
    marginLeft: 8,
  },
  itemTitle: {
    fontSize: 14,
    color: '#050505',
  },
  itemImage: {
    width: 80,
    height: 80,
    borderRadius: 4,
    resizeMode: 'contain',
  },
  commentHeaderCreated: {
    fontSize: 12,
    color: '#888',
    marginLeft: 10,
  },
  commentText: {
    fontSize: 13,
    color: '#000',
    lineHeight: 20,
    letterSpacing: 0,
    textAlign: 'left',
  },
  relyContainer: {
    marginVertical: 8,
  },
  relyItemContainer: {
    flexDirection: 'row',
    marginLeft: 10,
  },
  relyComment: {
    marginLeft: 8,
    backgroundColor: '#fafdff',
    padding: 10,
    borderRadius: 4,
    borderColor: '#e5eeff',
    borderWidth: 1,
    flex: 1,
  },
  relyCommentHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  relyCommentHeaderName: {
    fontSize: 13,
    color: '#000',
    fontWeight: '500',
  },
  emptyContainer: {
    backgroundColor: '#fff',
    justifyContent: 'center',
    padding: 10,
  },
  emptyTitle: {
    ...globalStyles.text,
    textAlign: 'center',
    paddingVertical: 4,
  },
});
