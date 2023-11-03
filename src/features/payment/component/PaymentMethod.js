import api from '@app/api';
import { colors, globalStyles } from '@app/assets';
import _ from 'lodash';
import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Image, TouchableOpacity } from 'react-native';
import { CheckBox, Text } from 'react-native-elements';
import Spinner from 'react-native-spinkit';
import { useSelector } from 'react-redux';
export const PaymentMethod = React.memo(
  ({ typePayment, payments, userId }) => {
    const [selectedPayment, setSelectedPayment] = useState(typePayment);
    const [isLoading, setIsLoading] = useState(false);
    const { bank } = useSelector(state => ({
      bank: state.root.bank,
    }));
    useEffect(() => {
      setSelectedPayment(typePayment);
    }, [typePayment]);

    async function onSelectPayment(item) {
      setSelectedPayment(item.id);
      setIsLoading(true);
      try {
        await api.updateCheckOutTemp({
          member_id: userId,
          type_payment: item.id,
        });
        setTimeout(() => {
          setIsLoading(false);
        }, 500);
      } catch (error) {
        setIsLoading(false);
      }
  }

    const renderMoreInfo = () => {
      const payment = _.find(payments, pay => pay.id === selectedPayment)
      if (payment && payment.code === 'ck')

        return <View style={styles.boxPayment}>
          <View style={styles.row}>
            <Text style={styles.bankTitle}>Chủ TK: </Text>
            <Text style={styles.bankName}>{bank.account}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.bankTitle}>Số TK: </Text>
            <Text style={styles.bankName}>{bank.number_account}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.bankTitle}>Chi nhánh: </Text>
            <Text style={styles.bankName}>{bank.bank}</Text>
          </View>
        </View>
    }

    return (
      <>
        {payments &&
          payments.length > 0 &&
          payments.map(item => {
            return (
              <>
                <TouchableOpacity
                  style={styles.itemContainer}
                  key={item.id}
                  onPress={() => onSelectPayment(item)}>
                  {isLoading && item.id === selectedPayment ? (
                    <Spinner
                      type="Circle"
                      color={colors.link}
                      size={22}
                      style={{
                        padding: 0,
                        marginVertical: 8,
                        marginHorizontal: 10,
                      }}
                    />
                  ) : (
                    <CheckBox
                      checkedIcon="dot-circle-o"
                      uncheckedIcon="circle-o"
                      checked={selectedPayment === item.id}
                      containerStyle={styles.itemCheckbox}
                      onPress={() => onSelectPayment(item)}
                    />
                  )}
                  <View style={styles.itemRightContainer}>
                    <View style={styles.itemRightLogoContainer}>
                      <Image
                        style={styles.itemRightLogo}
                        resizeMode="contain"
                        source={{ uri: item.icon }}
                      />
                    </View>
                    <Text style={styles.itemRightTitle}>{item.title}</Text>
                  </View>
                </TouchableOpacity>
              </>
            );
          })}
        {renderMoreInfo()}

      </>
    );
  },
  (prev, next) => prev.typePayment === next.typePayment,
);

const styles = StyleSheet.create({
  itemContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
  },
  itemCheckbox: {
    padding: 0,
    margin: 0,
  },

  itemRightContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderBottomColor: '#dde4eb',
    borderBottomWidth: 1,
    flex: 1,
    paddingBottom: 5,
  },
  itemRightLogoContainer: {
    padding: 4,
    borderColor: '#e3e3e3',
    borderWidth: 1,
    borderRadius: 4,
    marginRight: 10,
  },
  itemRightLogo: {
    height: 20,
    width: 36,
  },
  itemRightTitle: {
    fontSize: 14,
    color: '#000',
  },
  boxPayment: {
    backgroundColor: '#F0F4C3',
    borderWidth: 0.5,
    borderRadius: 5,
    borderColor: '#4CAF50',
    padding: 4,
    marginLeft: 40,
    marginVertical: 10
  },
  row: {
    flexDirection: 'row'
  },
  bankTitle: {
    ...globalStyles.text,
    fontSize: 12,
    color: colors.gray,
    width: 60,
  },
  bankName: {
    ...globalStyles.text,
    fontSize: 12,
    fontWeight: 'bold'
  }
});
