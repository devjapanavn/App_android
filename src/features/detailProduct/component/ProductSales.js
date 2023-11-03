import {stringHelper} from '@app/utils';
import React from 'react';
import {StyleSheet, View} from 'react-native';
import {Text} from 'react-native-elements';
import {DataTable} from 'react-native-paper';

const Component = () => {
  return (
    <View style={styles.box}>
      <Text style={styles.headerTitleStyle}>Mua nhiều giá tốt</Text>
      <DataTable>
        <DataTable.Header>
          <DataTable.Title>Số lượng</DataTable.Title>
          <DataTable.Title>Giảm thêm</DataTable.Title>
          <DataTable.Title>Giá bán</DataTable.Title>
        </DataTable.Header>

        <DataTable.Row>
          <DataTable.Cell>Mua từ 5</DataTable.Cell>
          <DataTable.Cell>
            <View style={styles.cellDiscountPercent}>
              <Text style={styles.cellDiscountPercentTitle}>5%</Text>
            </View>
          </DataTable.Cell>
          <DataTable.Cell>{stringHelper.formatMoney(890000)} đ</DataTable.Cell>
        </DataTable.Row>
        <DataTable.Row>
          <DataTable.Cell>Mua từ 10</DataTable.Cell>
          <DataTable.Cell>
            <View style={styles.cellDiscountPercent}>
              <Text style={styles.cellDiscountPercentTitle}>10%</Text>
            </View>
          </DataTable.Cell>
          <DataTable.Cell>{stringHelper.formatMoney(890000)} đ</DataTable.Cell>
        </DataTable.Row>
      </DataTable>
      <DataTable.Row>
          <DataTable.Cell>Mua từ 15</DataTable.Cell>
          <DataTable.Cell>
            <View style={styles.cellDiscountPercent}>
              <Text style={styles.cellDiscountPercentTitle}>15%</Text>
            </View>
          </DataTable.Cell>
          <DataTable.Cell>{stringHelper.formatMoney(843000)} đ</DataTable.Cell>
        </DataTable.Row>
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginBottom: 4,
    padding: 10,
    backgroundColor:'#fff'
  },
  headerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
  },
  cellDiscountPercent: {
    backgroundColor: '#dc0000',
    width: 40,
    height: 18,
    borderRadius: 2,
  },
  cellDiscountPercentTitle: {
    textAlign: 'center',
    color: '#fff',
    fontSize: 12,
  },
});

function areEqual(prev, next) {
  return true;
}
export const ProductSales = React.memo(Component, areEqual);
