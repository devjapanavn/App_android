import React from 'react';
import {StyleSheet, TouchableOpacity, View} from 'react-native';
import _ from 'lodash';
import Protypes from 'prop-types';
import {ImageReponsive} from './imageReponsive';
import {spacing} from '@app/assets';
import {Icon, Text} from 'react-native-elements';
import {convertDateStringToString, stringHelper} from '@app/utils';
import { navigateRoute } from '@app/route';
import { ROUTES } from '@app/constants';
const Component = ({news, type}) => {
  return (
    <TouchableOpacity
      activeOpacity={0.8}
      onPress={() => navigateRoute(ROUTES.NEWS_DETAIL, {...news})}>
      <View style={styles.container}>
        <ImageReponsive
          source={{uri: news.images}}
          containerStyle={styles.img}
        />
        <View style={styles.blockInfo}>
          <Text style={styles.txtTitle} numberOfLines={2}>
            {news.name}
          </Text>
          <View style={styles.blockDateAndView}>
            <Icon name="eye" type="ionicon" color={'#ccc'} size={12} />
            <Text style={styles.txt_blockDateAndView}>
              {stringHelper.formatMoney(news.visit)}
            </Text>
            <Icon name="eye" type="ionicon" color={'#ccc'} size={12} />
            <Text style={styles.txt_blockDateAndView}>
              {convertDateStringToString(
                news.date_publish,
                'YYYY-MM-DD HH:mm:ss',
                'DD-MM-YYYY',
              )}
            </Text>
          </View>
        </View>
      </View>
    </TouchableOpacity>
  );
};

Component.propTypes = {
  news: Protypes.object,
  type: Protypes.oneOf(['Vertical', 'Horizontal']),
};

Component.defaultProps = {
  type: 'Horizontal',
};

function areEqual(prev, next) {
  return _.isEqual(prev.news, next.news);
}
export const NewsItem = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  container: {flexDirection: 'row', marginBottom: spacing.medium},
  img: {
    width: 124,
    borderRadius: 8,
    height: 70,
  },
  blockInfo: {
    marginLeft: spacing.medium,
    flex: 1,
  },
  txtTitle: {
    color: '#000000',
    fontSize: 14,
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
});
