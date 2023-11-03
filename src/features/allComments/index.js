import { convertTimeAgo, stringHelper } from '@app/utils';
import React, { useEffect, useState } from 'react';
import {
  FlatList,
  StyleSheet,
  View,
  Image,
  InteractionManager,
  Pressable,
} from 'react-native';
import {
  Avatar,
  Text,
  Button,
  Divider,
  LinearProgress,
  Chip,
} from 'react-native-elements';
import StarRating from 'react-native-star-rating';
import { iOSColors } from 'react-native-typography';
import { navigateRoute } from '@app/route';
import { ROUTES } from '@app/constants';
import { appDimensions, colors, images } from '@app/assets';
import { useRoute } from '@react-navigation/native';
import { useQuery } from 'react-query';
import api from '@app/api';
import _ from 'lodash';
import ImageView from 'react-native-image-viewing';
import FastImage from 'react-native-fast-image';

const Screen = props => {
  const route = useRoute();
  const [onReady, setOnReady] = useState(false);
  const [comments, setComments] = useState([]);
  const [filter, setFilter] = useState({
    page: 1,
    rate: [],
  });
  const [imageViewer, setImageViewer] = useState({
    index: 0,
    visible: false,
    images: [],
  });

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  const fetchgetCommnet = async () => {
    return await api.getListComment(
      route.params?.id,
      filter.rate.join(','),
      filter.page,
    );
  };
  console.log('route.params', route.params)
  const { status, data, error, refetch } = useQuery(
    ['getCommnet', { productId: route.params?.id, ...filter }],
    fetchgetCommnet,
  );

  useEffect(() => {
    if (data && data.length > 0) {
      if (filter.page === 1) {
        setComments(data);
      } else {
        setComments(prev => [...prev, ...data]);
      }
    } else if (filter.page === 1) {
      setComments([]);
    }
  }, [data]);

  function onLoadMore() {
    setFilter(prev => ({ ...prev, page: prev.page + 1 }));
  }
  const _ratingRow = (rating, percent, count) => {
    const percentNum = stringHelper.formatToNumber(percent) / 100;
    return (
      <View style={{ flexDirection: 'row', alignItems: 'center' }}>
        <Text style={styles.rowRatingTitle}>{rating}</Text>
        <StarRating
          containerStyle={styles.rowRatingStar}
          starSize={14}
          disabled={true}
          maxStars={1}
          rating={1}
          fullStarColor={iOSColors.yellow}
        />
        <LinearProgress
          value={percentNum}
          variant="determinate"
          color="#474747"
          trackColor="#E3E3E3"
          style={{ flex: 1, height: 6, borderRadius: 6 }}
        />
        <Text style={styles.rowRatingCount}>{count}</Text>
      </View>
    );
  };
  const _ratingFilter = rating => {
    const isSelected = _.includes(filter.rate, rating);
    return (
      <Chip
        onPress={() => {
          if (!isSelected) {
            setFilter(prev => ({ rate: [...prev.rate, rating], page: 1 }));
          } else {
            const rates = _.filter(filter.rate, rt => rt !== rating);
            setFilter(prev => ({ rate: rates, page: 1 }));
          }
        }}
        title={rating}
        titleStyle={{ color: !isSelected ? iOSColors.gray : iOSColors.blue }}
        iconRight={true}
        type="outline"
        buttonStyle={[
          styles.chipRatingButton,
          !isSelected ? styles.chipRatingNotSelect : null,
        ]}
        containerStyle={styles.chipRatingContainer}
        icon={{
          name: 'star',
          type: 'ionicon',
          size: 14,
          color: !isSelected ? iOSColors.gray : iOSColors.yellow,
        }}
      />
    );
  };
  const _renderHeader = () => {
    return (
      <>
        <View style={{ margin: 10 }}>
          <View
            style={{
              flexDirection: 'row',
              alignItems: 'center',
            }}>
            <Text style={styles.ratingPoint}>
              {route?.params?.dataRate?.medium_rate || 5.0}
            </Text>
            <StarRating
              containerStyle={styles.ratingStar}
              starSize={20}
              disabled={true}
              maxStars={5}
              rating={stringHelper.formatToNumber(route?.params?.dataRate?.medium_rate || '5')}
              fullStarColor={iOSColors.yellow}
              emptyStarColor={iOSColors.gray}
              starStyle={{ marginHorizontal: 2 }}
            />
            <Text style={styles.ratingCount}>
              {stringHelper.formatMoney(comments.length)} đánh giá
            </Text>
          </View>
          {_ratingRow(
            5,
            route?.params?.dataRate?.percent_rate_5,
            route?.params?.dataRate?.total_rate_5,
          )}
          {_ratingRow(
            4,
            route?.params?.dataRate?.percent_rate_4,
            route?.params?.dataRate?.total_rate_4,
          )}
          {_ratingRow(
            3,
            route?.params?.dataRate?.percent_rate_3,
            route?.params?.dataRate?.total_rate_3,
          )}
          {_ratingRow(
            2,
            route?.params?.dataRate?.percent_rate_2,
            route?.params?.dataRate?.total_rate_2,
          )}
          {_ratingRow(
            1,
            route?.params?.dataRate?.percent_rate_1,
            route?.params?.dataRate?.total_rate_1,
          )}

          <Button
            title="Viết đánh giá"
            type="outline"
            containerStyle={{ margin: 10 }}
            onPress={() =>
              navigateRoute(ROUTES.WRITE_COMMENT, route.params, null, true)
            }
          />
          <Divider />
          <View style={{ flexDirection: 'row' }}>
            {_ratingFilter(5)}
            {_ratingFilter(4)}
            {_ratingFilter(3)}
            {_ratingFilter(2)}
            {_ratingFilter(1)}
          </View>
        </View>
      </>
    );
  };



  const _renderComment = ({ item: comment }) => {
    return (
      <View style={styles.itemContainer}>
        <View style={{ flexDirection: 'row', marginBottom: 10 }}>
          <Avatar
            source={images.ic_no_avatar}
            title={comment.fullname}
            rounded
            size={'small'}
          />
          <View style={{ flexDirection: 'column', marginLeft: 8 }}>
            <Text style={{ fontSize: 14, color: '#050505' }}>
              {comment.fullname}
            </Text>
            <View style={{ flexDirection: 'row', marginVertical: 2 }}>
              <StarRating
                starSize={16}
                disabled={true}
                maxStars={5}
                rating={comment.rate}
                fullStarColor={iOSColors.yellow}
                emptyStarColor={iOSColors.gray}
                starStyle={{ marginHorizontal: 2 }}
              />
              <Text style={{ fontSize: 12, color: '#888', marginLeft: 10 }}>
                ({convertTimeAgo(comment.created)})
              </Text>
            </View>
          </View>
        </View>
        <View>
          <Text style={{ fontSize: 13, color: '#050505', lineHeight: 20 }}>
            {comment.comments}
          </Text>
        </View>
        {comment.images ? (
          <>
            <FlatList
              horizontal
              style={{ marginVertical: 8 }}
              showsHorizontalScrollIndicator={false}
              data={comment.images}
              ItemSeparatorComponent={() => <View style={{ width: 10 }} />}
              renderItem={({ item, index }) => (
                <Pressable
                  onPress={() => {
                    setImageViewer({
                      visible: true,
                      index: index,
                      images: _.map(comment.images, img => ({ uri: img })),
                    });
                  }}>
                  <FastImage
                    source={{ uri: item }}
                    style={{
                      width: 80,
                      height: 80,
                      borderRadius: 4,
                      resizeMode: 'contain',
                    }}
                  />
                </Pressable>
              )}
            />
          </>
        ) : null}

        {comment.rep_comment ? (
          <FlatList
            style={{ marginVertical: 8 }}
            showsHorizontalScrollIndicator={false}
            data={comment.rep_comment}
            renderItem={({ item }) => (
              <View style={{ flexDirection: 'row', marginLeft: 10 }}>
                <Avatar
                  source={images.ic_comment}
                  imageProps={{ resizeMethod: 'auto', resizeMode: 'center' }}
                  title={item.fullname}
                  rounded
                  containerStyle={{ flex: 0,backgroundColor: '#fff' }}
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
      </View>
    );
  };

  if (!onReady) {
    return <View style={styles.box} />;
  }
  return (
    <View style={styles.box}>
      <FlatList
        ListHeaderComponent={_renderHeader}
        ItemSeparatorComponent={() => (
          <Divider
            style={{ backgroundColor: colors.border, marginHorizontal: 10 }}
          />
        )}
        onEndReachedThreshold={0.5}
        onEndReached={onLoadMore}
        data={comments}
        renderItem={_renderComment}
      />
      <ImageView
        keyExtractor={item => item}
        presentationStyle="overFullScreen"
        images={imageViewer.images}
        imageIndex={imageViewer.index}
        swipeToCloseEnabled={true}
        visible={imageViewer.visible}
        onRequestClose={() =>
          setImageViewer({ visible: false, index: 0, images: [] })
        }
      />
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    flex: 1,
    backgroundColor: '#fff',
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
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
  itemTitle: {
    flex: 1,
    flexWrap: 'wrap',
    fontSize: 12,
    color: '#000',
    marginLeft: 8,
  },
  rowRatingTitle: { color: '#9B9B9B', fontSize: 13, paddingLeft: 5 },
  rowRatingStar: {
    marginHorizontal: 4,
  },
  rowRatingCount: {
    paddingHorizontal: 8,
    width: 70,
    textAlign: 'center',
  },
  chipRatingContainer: {
    width: (appDimensions.width - 20) / 5,
    marginVertical: 5,
    padding: 5,
  },
  chipRatingButton: {},
  chipRatingNotSelect: {
    borderColor: iOSColors.gray,
    backgroundColor: '#F1F1F1',
  },
});

function areEqual(prev, next) {
  return true;
}

export const AllCommentsScreen = Screen;
