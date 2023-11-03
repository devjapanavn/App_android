import {appDimensions, images, spacing} from '@app/assets';
import {stringHelper} from '@app/utils';
import React, {useEffect, useState} from 'react';
import {
  FlatList,
  StyleSheet,
  View,
  Image,
  InteractionManager,
} from 'react-native';
import {Avatar, Text, Button, Divider} from 'react-native-elements';
import StarRating from 'react-native-star-rating';
import {iOSColors} from 'react-native-typography';
import {navigateRoute} from '@app/route';
import {ROUTES} from '@app/constants';
const data = [
  {
    avatar: 'https://picsum.photos/300',
    name: 'Vũ Minh Khang',
    rating: 5,
    createDate: '22/08/2021',
    comment:
      'Mình mới nhận được sản phẩm, chưa check hạn sử dụng hay chưa dùng thử. Khi dùng mình sẽ review lại sau.',
    images: [
      'https://picsum.photos/300',
      'https://picsum.photos/300',
      'https://picsum.photos/300',
      'https://picsum.photos/300',
    ],
    relies: [
      {
        avatar: 'https://japana.vn/assets/frontend/assets/images/favicon.png',
        name: 'Siêu Thị Nhật Bản Japana',
        createDate: '22/08/2021',
        comment:
          'Japana xin cảm ơn quý khách đã mua hàng. Mọi nhận xét chính xác và rõ ràng của quý khách là động lực để chúng tôi làm tốt hơn mỗi ngày, và cũng sẽ giúp những khách hàng khác có cái nhìn khách quan hơn để quyết định sử dụng sản phẩm. Mong quý khách sẽ tiếp tục ủng hộ Japana. Chân thành cám ơn.',
      },
    ],
  },
];

const Component = () => {
  const _renderHeader = () => {
    return (
      <>
        <View style={{marginHorizontal: 10}}>
          <View
            style={{
              flexDirection: 'row',
              alignItems: 'center',
            }}>
            <Text style={styles.ratingPoint}>4.9</Text>
            <StarRating
              containerStyle={styles.ratingStar}
              starSize={20}
              disabled={true}
              maxStars={5}
              rating={4}
              fullStarColor={iOSColors.yellow}
              emptyStarColor={iOSColors.gray}
              starStyle={{marginHorizontal: 2}}
            />
            <Text style={styles.ratingCount}>
              {stringHelper.formatMoney(228)} đánh giá
            </Text>
          </View>
          <Divider />
        </View>
      </>
    );
  };

  const _renderFooter = () => {
    return (
      <>
        <View style={{flexDirection: 'row'}}>
          <Button
            onPress={() => navigateRoute(ROUTES.ALL_COMMENT)}
            title="Xem tất cả bình luận"
            titleStyle={{fontSize: 13}}
            containerStyle={{flex: 1, marginHorizontal: 10}}
          />
          <Button
            title="Viết bình luận"
            type="outline"
            titleStyle={{fontSize: 13}}
            onPress={() => navigateRoute(ROUTES.WRITE_COMMENT, null, null, true)}
            containerStyle={{flex: 1, marginHorizontal: 10}}
          />
        </View>
      </>
    );
  };

  const _renderComment = ({item: comment, index}) => {
    return (
      <View style={styles.itemContainer}>
        <View style={{flexDirection: 'row', marginBottom: 10}}>
          <Avatar
            source={{uri: comment.avatar}}
            title={comment.name}
            rounded
            size={'small'}
          />
          <View style={styles.itemHeader}>
            <Text style={styles.itemTitle}>{comment.name}</Text>
            <View style={{flexDirection: 'row', marginVertical: 2}}>
              <StarRating
                starSize={16}
                disabled={true}
                maxStars={5}
                rating={comment.rating}
                fullStarColor={iOSColors.yellow}
                emptyStarColor={iOSColors.gray}
                starStyle={{marginHorizontal: 2}}
              />
              <Text style={styles.commentHeaderCreated}>
                ({comment.createDate})
              </Text>
            </View>
          </View>
        </View>
        <View>
          <Text style={styles.commentText}>{comment.comment}</Text>
        </View>
        {comment.images ? (
          <FlatList
            horizontal
            style={{marginVertical: 8}}
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={{
              flex: 1,
              justifyContent: 'center',
              alignItems: 'center',
            }}
            data={comment.images}
            ItemSeparatorComponent={() => <View style={{width: 10}} />}
            renderItem={({item}) => (
              <Image source={{uri: item}} style={styles.itemImage} />
            )}
          />
        ) : null}

        {comment.relies ? (
          <FlatList
            style={styles.relyContainer}
            scrollEnabled={false}
            showsHorizontalScrollIndicator={false}
            data={comment.relies}
            renderItem={({item}) => (
              <View style={styles.relyItemContainer}>
                <Avatar
                  source={{uri: item.avatar}}
                  title={item.name}
                  rounded
                  size={'small'}
                />
                <View style={styles.relyComment}>
                  <View style={styles.relyCommentHeader}>
                    <Text style={styles.relyCommentHeaderName}>
                      {item.name}
                    </Text>
                    <Text style={styles.commentHeaderCreated}>
                      ({item.createDate})
                    </Text>
                  </View>
                  <Text style={styles.relyCommentText}>{item.comment}</Text>
                </View>
              </View>
            )}
          />
        ) : null}
        <Divider />
      </View>
    );
  };

  return (
    <View style={styles.box}>
      <Text style={styles.headerTitleStyle}>Khách hàng đánh giá</Text>
      <FlatList
        ListHeaderComponent={_renderHeader}
        ListFooterComponent={_renderFooter}
        data={data}
        scrollEnabled={false}
        renderItem={_renderComment}
      />
    </View>
  );
};

function areEqual(prev, next) {
  return true;
}
export const Comments = React.memo(Component, areEqual);

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor: '#fff',
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
    marginVertical: 10,
    marginHorizontal: 10,
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
});
