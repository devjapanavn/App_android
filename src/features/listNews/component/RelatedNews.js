import {globalStyles, images} from '@app/assets';
import {stringHelper} from '@app/utils';
import React from 'react';
import {ScrollView, StyleSheet, TouchableOpacity} from 'react-native';
import {View} from 'react-native';
import {Avatar, Divider, Text, Icon, Chip} from 'react-native-elements';
import StarRating from 'react-native-star-rating';

const Component = () => {
  return (
    <View style={{marginTop: 15}}>
      <Divider />
      <View style={styles.container}>
        <TouchableOpacity style={[styles.box]}>
          <View style={styles.row}>
            <Icon
              name="arrow-back"
              type="material"
              color="rgb(138, 138, 143)"
            />
            <View
              style={{
                flex: 1,
                marginVertical: 15,
                marginHorizontal: 4,
                borderRightColor: '#d9d9d9',
                borderRightWidth: 1,
              }}>
              <Text style={styles.title}>Bài trước đó </Text>
              <Text style={styles.text}>
                Review 3 sản phẩm makeup giúp giữ lớp trang điểm lâu trôi
              </Text>
            </View>
          </View>
        </TouchableOpacity>
        <TouchableOpacity style={styles.box}>
          <View style={[styles.row]}>
            <View style={{flex: 1, marginVertical: 15, marginHorizontal: 4}}>
              <Text style={[styles.title, {textAlign: 'right'}]}>
                Bài trước đó{' '}
              </Text>
              <Text style={[styles.text, {textAlign: 'right'}]}>
                7 bí kíp làm đẹp mùa hè cho phái đẹp
              </Text>
            </View>
            <Icon
              name="arrow-forward"
              type="material"
              color="rgb(138, 138, 143)"
            />
          </View>
        </TouchableOpacity>
      </View>
      <Divider />
    </View>
  );
};
export const RelatedNews = React.memo(Component, (prev, next) => false);
const styles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  container: {
    flexDirection: 'row',
  },
  box: {
    flex: 1,
  },
  title: {
    ...globalStyles.text,
    color: '#8a8a8f',
    fontSize: 14,
  },
  text: {
    ...globalStyles.text,
    color: '#000000',
    fontSize: 14,
    lineHeight: 24,
  },
});
