import {images} from '@app/assets';
import {ProductItem} from '@app/components';
import React from 'react';
import {StyleSheet, View, FlatList} from 'react-native';
import {Text} from 'react-native-elements';
import api from '@app/api';
import {useQuery} from 'react-query';
import {useSelector} from 'react-redux';
import {stringHelper} from '@app/utils';
import { GLOBAL_FUNC } from '@app/constants';

const Component = ({id}) => {
  const {user} = useSelector(state => ({
    user: state.auth.user,
  }));

  const fetchgetProductViewd = async () => {
    return await api.getProductViewd(id, user?.id || 0);
  };

  const {status, data, error, refetch} = useQuery(
    ['getProductViewd'],
    fetchgetProductViewd,
    {
      retry: 1,
    },
  );
  if (data && data.length > 0) {
    return (
      <View style={styles.box}>
        <View style={styles.header}>
          <Text style={styles.headerTitleStyle}>Sản phẩm đã xem</Text>
          <Text style={styles.headerSubTitleStyle}>
            ({stringHelper.formatMoney(data.length)} sản phẩm)
          </Text>
        </View>

        <FlatList
          showsHorizontalScrollIndicator={false}
          data={data}
          horizontal
          renderItem={({item, index}) => {
                item = GLOBAL_FUNC.filterPrice(item)
            return <ProductItem product={item} imageStyle={{aspectRatio:1}}/>;
          }}
        />
      </View>
    );
  }
  return <View />;
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor: '#fff',
  },
  header: {
    marginBottom: 15,
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
  },
  headerSubTitleStyle: {
    color: '#555',
    fontSize: 12,
  },
});

function areEqual(prev, next) {
  return true;
}
export const ProductViewed = React.memo(Component, areEqual);
