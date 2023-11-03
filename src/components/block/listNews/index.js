import React, {useEffect, useState} from 'react';
import {View, StyleSheet, FlatList, Pressable} from 'react-native';
import {Text, Button, Icon} from 'react-native-elements';
import {appDimensions, spacing} from '@app/assets';
import {ImageReponsive} from '@app/components';
import {convertDateStringToString, stringHelper} from '@app/utils';
import {NewsItem} from 'src/components/newsItem';
import {navigateRoute} from '@app/route';
import {ROUTES} from '@app/constants';
import _ from 'lodash';

const NewsComponent = ({news,title}) => {

  if (news && news.length > 0) {
    return (
      <View style={styles.container}>
        <View style={styles.headerContainer}>
          <Text style={styles.headerTitleStyle}>{title}</Text>
          {/* <Button
            type="clear"
            iconRight
            title="Xem tất cả >"
            titleStyle={styles.headerTitleMoreStyle}
            // icon={{
            //   type: 'ionicon',
            //   name: 'chevron-forward',
            //   size: 15,
            //   color: '#0f83ff',
            // }}
          /> */}
        </View>
        <Pressable onPress={() => navigateRoute(ROUTES.NEWS_DETAIL,{...news[0]})}>
          <ImageReponsive
            source={{
              uri: news[0].images,
            }}
            containerStyle={styles.imagNewsFirst}
          />
          <Text style={styles.txtTitleNewsFirst} numberOfLines={2}>
            {news[0].name}
          </Text>
          <Text style={styles.txtDesNewsFirst} numberOfLines={3}>
            {news[0].notes}
          </Text>
          <View style={styles.blockDateAndView}>
            <Icon name="eye" type="ionicon" color={'#ccc'} size={12} />
            <Text style={styles.txt_blockDateAndView}>
              {stringHelper.formatMoney(news[0].visit)}
            </Text>
            <Icon name="eye" type="ionicon" color={'#ccc'} size={12} />
            <Text style={styles.txt_blockDateAndView}>
              {convertDateStringToString(
                news[0].date_publish,
                'YYYY-MM-DD HH:mm:ss',
                'DD-MM-YYYY',
              )}
            </Text>
          </View>
        </Pressable>
        {news.length > 1 ? (
          <FlatList
            style={styles.flListNews}
            key={'news_list'}
            keyExtractor={(item, index) => `news_${item.id}`}
            scrollEnabled={false}
            showsVerticalScrollIndicator={false}
            data={news.slice(1)}
            renderItem={({item}) => {
              return <NewsItem news={item} />;
            }}
          />
        ) : null}
      </View>
    );
  }
  return <View />;
};
function areEqual(prev, next) {
  return _.isEqual(prev.news, next.news);
}
export const ListNews = React.memo(NewsComponent, areEqual);
const styles = StyleSheet.create({
  container: {
    margin: spacing.medium,
  },
  headerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerTitleStyle: {
    fontSize: 15,
    color: '#000',
    flex: 1,
  },
  headerTitleMoreStyle: {
    fontSize: 12,
    textAlign: 'right',
  },
  imagNewsFirst: {
    width: appDimensions.width - spacing.medium * 2,
    height: null,
  },
  txtTitleNewsFirst: {
    fontSize: 16,
    fontWeight: '600',
    color: '#000',
  },
  txtDesNewsFirst: {
    fontSize: 13,
    color: '#555',
  },
  blockDateAndView: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 6,
  },
  txt_blockDateAndView: {
    fontSize: 12,
    color: '#0F83FF',
    marginHorizontal: 6,
  },
  flListNews: {
    marginVertical: spacing.medium,
  },
});
