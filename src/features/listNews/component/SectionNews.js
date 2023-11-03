import {appDimensions, globalStyles, images} from '@app/assets';
import {ImageReponsive} from '@app/components';
import {stringHelper} from '@app/utils';
import React from 'react';
import {SectionList, StyleSheet} from 'react-native';
import {FlatList, View} from 'react-native';
import {Button, Divider, Icon, Text} from 'react-native-elements';
const news = [
  {
    id: 1,
    title: 'Tin cùng chuyên mục',
    data: [
      {
        id: 1,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post1,
        created: '22/12/2021',
      },
      {
        id: 2,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post2,
        created: '22/12/2021',
      },
      {
        id: 3,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post3,
        created: '22/12/2021',
      },
    ],
  },
  {
    id: 2,
    title: 'Tin mới nhất',
    data: [
      {
        id: 4,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post4,
        created: '22/12/2021',
      },
      {
        id: 5,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post5,
        created: '22/12/2021',
      },
      {
        id: 6,
        name: 'Thực phẩm chức năng - Những lưu ý sử dụng TPCN đúng cách',
        imgs: images.news_detail_post7,
        created: '22/12/2021',
      },
    ],
  },
];
const Component = ({type}) => {
  const renderItem = ({item, index}) => {
    switch (type) {
      case 'vertical':
        return (
          <View style={styles.itemContainerVertical}>
            <ImageReponsive
              source={item.imgs}
              containerStyle={styles.itemImageVertical}
            />
            <Text style={styles.itemTextVertical}>{item.name}</Text>
            <View style={styles.itemInfoVertical}>
              <View
                style={{
                  flexDirection: 'row',
                  marginTop: 10,
                  alignItems: 'center',
                  justifyContent: 'space-between',
                }}>
                <View
                  style={{
                    flexDirection: 'row',
                    alignItems: 'center',
                  }}>
                  <Icon
                    name="date-range"
                    type="material"
                    color="rgb(59, 72, 89)"
                    size={16}
                  />
                  <Text style={styles.itemSubtitle}>{item.created}</Text>
                </View>
                <Button title={'Còn hạn'} buttonStyle={{paddingVertical: 4}} />
              </View>
            </View>
            <Divider />
          </View>
        );
      default:
        return (
          <View style={styles.itemContainer}>
            <ImageReponsive
              source={item.imgs}
              containerStyle={styles.itemImage}
            />
            <View style={styles.itemRight}>
              <Text style={styles.itemText}>{item.name}</Text>
              <View
                style={{
                  flexDirection: 'row',
                  marginTop: 10,
                  alignItems: 'center',
                }}>
                <Icon
                  name="remove-red-eye"
                  type="material"
                  color="rgb(59, 72, 89)"
                  size={16}
                />
                <Text style={styles.itemSubtitle}>
                  {stringHelper.formatMoney(2233)}
                </Text>
                <Icon
                  name="date-range"
                  type="material"
                  color="rgb(59, 72, 89)"
                  size={16}
                />
                <Text style={styles.itemSubtitle}>{item.created}</Text>
              </View>
            </View>
          </View>
        );
    }
  };
  const renderHeader = ({section}) => {
    return (
      <View style={styles.headerContainer}>
        <Text style={styles.title}>{section.title}</Text>
      </View>
    );
  };
  return (
    <View>
      <SectionList
        contentContainerStyle={{
          paddingHorizontal: 10,
          backgroundColor: '#fff',
          marginBottom: 10,
        }}
        sections={news}
        keyExtractor={(item, index) => item + index}
        renderItem={renderItem}
        renderSectionHeader={renderHeader}
      />
    </View>
  );
};
export const SectionNews = React.memo(Component, (prev, next) => true);
const styles = StyleSheet.create({
  separator: {
    width: 0.3,
    backgroundColor: '#fff',
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
  itemContainer: {
    paddingVertical: 4,
    flexDirection: 'row',
  },
  itemContainerVertical: {
    paddingVertical: 4,
  },
  itemImage: {
    width: 124,
    resizeMode: 'contain',
    borderRadius: 4,
  },
  itemImageVertical: {
    width: appDimensions.width - 20,
    resizeMode: 'contain',
    borderRadius: 4,
  },
  itemRight: {
    marginLeft: 8,
    flex: 1,
  },
  itemInfoVertical: {
    marginBottom: 8,
    flex: 1,
  },
  itemSubtitle: {
    fontSize: 13,
    marginHorizontal: 6,
  },
  itemText: {
    ...globalStyles.text,
    fontSize: 14,
  },
  itemTextVertical: {
    ...globalStyles.text,
    fontSize: 14,
    lineHeight: 22,
    paddingVertical: 4,
  },
});
