import {globalStyles} from '@app/assets';
import {StyleSheet} from 'react-native';

export default styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingVertical: 10,
  },
  gifText: {
    backgroundColor: '#11d017',
    width: 57,
    height: 18,
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
    textAlign: 'center',
    fontSize: 11,
    color: 'white',
  },
  box: {
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#d9d9d9',
    borderRadius: 8,
    marginHorizontal: 10,
    paddingVertical: 10,
    elevation: 2,
    backgroundColor: '#fff',
  },
  boxTitle: {
    fontWeight: '500',
    fontSize: 16,
    textAlign: 'left',
    paddingVertical: 5,
    paddingHorizontal: 10,
  },
  row: {
    flexDirection: 'row',
    padding: 5,
  },
  icon: {
    paddingHorizontal: 6,
  },
  text: {
    ...globalStyles.text,
    fontSize: 13,
    lineHeight: 22,
    flex: 1,
    fontFamily: 'SF Pro Display',
  },
  code: {
    textTransform: 'uppercase',
  },
  subText: {
    color: '#8a8a8f',
  },
  money: {
    color: '#2367ff',
    fontWeight: '500',
  },
  productImage: {
    width: 60,
    height: 60,
    resizeMode: 'contain',
    marginHorizontal: 5,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#d9d9d9',
  },
  productInfo: {
    flex: 1,
    marginHorizontal: 5,
  },
  productInfoTitle: {
    fontSize: 13,
    lineHeight: 20,
    fontFamily: 'SF Pro Display',
  },
  productInfoSubTitle: {
    fontSize: 13,
    lineHeight: 20,
    color: '#3b4859',
    fontFamily: 'SF Pro Display',
  },
  productInfoPrice: {
    fontSize: 13,
    lineHeight: 20,
    color: '#000',
    fontWeight: '500',
    fontFamily: 'SF Pro Display',
  },
  footer: {
    margin: 10,
  },
  footerTag: {
    flexDirection: 'row',
    marginVertical: 5,
  },
  footerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginVertical: 5,
  },
  footerLeftTitle: {
    fontSize: 15,
    color: '#3b4859',
  },
  footerRightTitle: {
    fontSize: 15,
    color: '#3b4859',
  },
  discountCode: {
    marginRight: 10,
  },
  discountCodeChip: {
    paddingVertical: 4,
    paddingHorizontal: 15,
  },
  discountCodeText: {
    fontSize: 12,
  },
});
