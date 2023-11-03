import {globalStyles, images} from '@app/assets';
import {stringHelper} from '@app/utils';
import React from 'react';
import {StyleSheet} from 'react-native';
import {View} from 'react-native';
import {Avatar, Divider, Text, Icon} from 'react-native-elements';
import StarRating from 'react-native-star-rating';

const Component = () => {
  return (
    <>
      <Text style={styles.title}>Cẩm nang sử dụng kem chống nắng từ A-Z</Text>
      <View style={styles.subheader}>
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
        <Text style={{fontSize: 14, color: '#2a2a2a', marginLeft: 10}}>
          {4.5}
        </Text>
        <Divider
          style={{
            width: 1,
            height: 20,
            backgroundColor: '#e3e3e3',
            marginHorizontal: 10,
          }}
        />
        <Text style={{fontSize: 14, color: '#000'}}>688 đánh giá</Text>
      </View>

      <View style={[styles.subheader, {marginTop: 18}]}>
        <Avatar rounded source={images.news_detail_avatar} size="medium" />
        <View style={{marginHorizontal: 8}}>
          <Text>Sương Trần</Text>
          <StarRating
            starSize={16}
            disabled={true}
            maxStars={5}
            rating={5}
            fullStarColor={'rgb(243, 18, 89)'}
            emptyStarColor={'rgb(59, 72, 89)'}
            starStyle={{marginTop: 5}}
            iconSet="MaterialIcons"
            fullStar={'favorite'}
          />
        </View>
        <View style={{flex: 1, alignItems: 'flex-end'}}>
          <View style={styles.subheader}>
            <Icon
              type="material"
              name="date-range"
              color="rgb(59, 72, 89)"
              size={18}
            />
            <Text style={styles.textDate}>22/12/2021</Text>
          </View>
          <View style={styles.subheader}>
            <View style={[styles.subheader, {marginRight: 20}]}>
              <Icon
                type="material"
                name="remove-red-eye"
                color="rgb(59, 72, 89)"
                size={18}
              />
              <Text style={styles.textDate}>
                {stringHelper.formatMoney(8850)}
              </Text>
            </View>
            <View style={styles.subheader}>
              <Icon
                type="material"
                name="comment"
                color="rgb(59, 72, 89)"
                size={18}
              />
              <Text style={styles.textDate}>
                {stringHelper.formatMoney(2506)}
              </Text>
            </View>
          </View>
        </View>
      </View>
    </>
  );
};
export const Header = React.memo(Component, (prev, next) => false);
const styles = StyleSheet.create({
  title: {
    ...globalStyles.text,
    fontWeight: '500',
    fontSize: 28,
    lineHeight: 38,
    marginVertical: 5,
  },
  textDate: {
    ...globalStyles.text,
    fontSize: 14,
    marginLeft: 5,
  },
  subheader: {
    flexDirection: 'row',
    marginVertical: 2,
  },
});
