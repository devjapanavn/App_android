import {globalStyles, images} from '@app/assets';
import {stringHelper} from '@app/utils';
import React from 'react';
import {ScrollView, StyleSheet} from 'react-native';
import {View} from 'react-native';
import {Avatar, Divider, Text, Icon, Chip} from 'react-native-elements';
import StarRating from 'react-native-star-rating';

const tags = [
  {name: 'Sức khỏe', color: '#23b864'},
  {name: 'Làm đẹp', color: '#5e8ae8'},
  {name: 'Sinh lý nam nữ', color: '#ffa70f'},
  {name: 'Chăm sóc cơ thể', color: '#6f38fa'},
  {name: 'Chống nắng', color: '#ff5757'},
];
const Component = () => {
  return (
    <View style={styles.container}>
      {tags.map((item, index) => (
        <Chip
          title={`# ${item.name}`}
          key={'tag_news_' + index}
          buttonStyle={[styles.chipContainer, {backgroundColor: item.color}]}
        />
      ))}
    </View>
  );
};
export const Tag = React.memo(Component, (prev, next) => false);
const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  chipContainer: {
    borderRadius: 4,
    paddingVertical: 4,
    paddingHorizontal: 15,
    margin: 4,
  },
  chipText: {
    ...globalStyles.text,
    fontSize: 13,
    lineHeight: 20,
    color: '#fff',
  },
});
